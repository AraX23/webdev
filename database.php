<?php
/**
 * ComMEETtee — database bootstrap.
 * Creates the schema on first run (mirrors the "auto-migrate" pattern from
 * the Masalihit Luxe build: every ALTER TABLE is guarded by an
 * information_schema check, so this file is safe to run on every request).
 */

function getDatabase(): PDO
{
  static $database;
  if ($database instanceof PDO) {
    return $database;
  }

  // Database credentials (defaults for local WAMP / XAMPP)
  $host = getenv('DB_HOST') ?: '127.0.0.1';
  $port = getenv('DB_PORT') ?: '3306';
  $username = getenv('DB_USER') ?: 'root';
  $password = getenv('DB_PASS') ?: '';
  $databaseName = getenv('DB_NAME') ?: 'commeettee';

  $connected = false;
  $lastError = null;
  $portsToTry = array_unique([$port, '3306', '3307']);

  foreach ($portsToTry as $p) {
    try {
      $server = new PDO("mysql:host=$host;port=$p;charset=utf8mb4", $username, $password);
      $server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $server->exec("CREATE DATABASE IF NOT EXISTS `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

      $database = new PDO("mysql:host=$host;port=$p;dbname=$databaseName;charset=utf8mb4", $username, $password);
      $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $connected = true;
      break;
    } catch (PDOException $e) {
      $lastError = $e;
    }
  }

  if (!$connected) {
    die('<div style="font-family:sans-serif; padding:2rem; background:#fff3f3; color:#b3392c; border:1px solid #ffc9c9; border-radius:8px; max-width:600px; margin:3rem auto;">'
      . '<h3 style="margin-top:0;">Database Connection Failed</h3>'
      . '<p>Could not connect to MySQL server at <strong>' . htmlspecialchars($host) . '</strong> using user <strong>' . htmlspecialchars($username) . '</strong>.</p>'
      . '<p><small>Error details: ' . htmlspecialchars($lastError ? $lastError->getMessage() : 'Unknown error') . '</small></p>'
      . '<p>Please ensure your WampServer MySQL or MariaDB service is running.</p>'
      . '</div>');
  }

  /* ---------------- users ----------------
     role: 'aspirant' | 'client' | 'admin'
     Aspirant-only fields (program, year, skills, bio, portfolio) sit on the
     same row rather than a second table — keeps every profile read to one
     query, and the fields are simply blank for client/admin accounts. */
  $database->exec("CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'aspirant',
    client_type VARCHAR(50) NOT NULL DEFAULT '',
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    avatar VARCHAR(255) NOT NULL DEFAULT '',
    org_name VARCHAR(255) NOT NULL DEFAULT '',
    program VARCHAR(255) NOT NULL DEFAULT '',
    year_level VARCHAR(50) NOT NULL DEFAULT '',
    skills VARCHAR(500) NOT NULL DEFAULT '',
    availability VARCHAR(255) NOT NULL DEFAULT '',
    bio VARCHAR(1000) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB");

  $hasClientType = (int) $database->query("
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'client_type'
  ")->fetchColumn();
  if (!$hasClientType) {
    $database->exec("ALTER TABLE users ADD COLUMN client_type VARCHAR(50) NOT NULL DEFAULT '' AFTER role");
    $database->exec("UPDATE users SET client_type = 'organization' WHERE role = 'client' AND org_name != ''");
    $database->exec("UPDATE users SET client_type = 'independent' WHERE role = 'client' AND (org_name IS NULL OR org_name = '')");
  }

  $adminStatement = $database->prepare('SELECT id FROM users WHERE email = ?');
  $adminStatement->execute(['admin@commeettee.local']);
  if (!$adminStatement->fetchColumn()) {
    $adminStatement = $database->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
    $adminStatement->execute(['Administrator', 'admin@commeettee.local', password_hash('admin123', PASSWORD_DEFAULT), 'admin']);
  }

  /* ---------------- categories ----------------
     Seeded from the brand guide's committee types. Clients pick one of
     these when posting an opening. */
  $database->exec("CREATE TABLE IF NOT EXISTS categories (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(500) NOT NULL DEFAULT '',
    image VARCHAR(255) NOT NULL DEFAULT ''
  ) ENGINE=InnoDB");
  if ((int) $database->query('SELECT COUNT(*) FROM categories')->fetchColumn() === 0) {
    $seedCategories = [
      ['documentation', 'Documentation', 'Captures every milestone in photos, videos, and files, so nothing about the event goes unrecorded.', 'assets/committee-documentation.jpg'],
      ['technicals', 'Technicals', 'Handles the technical equipment, setup, and operations needed to ensure smooth event execution.', 'assets/committee-technicals.jpg'],
      ['decorations', 'Decorations', 'Shapes the look and feel of the venue, from overall layout down to the smallest visual detail.', 'assets/committee-decorations.jpg'],
      ['logistics', 'Logistics', 'Keeps people, supplies, and schedules moving so every event runs on time and on plan.', 'assets/committee-technicals.jpg'],
    ];
    $categoryStatement = $database->prepare('INSERT INTO categories (id, name, description, image) VALUES (?, ?, ?, ?)');
    foreach ($seedCategories as $category) {
      $categoryStatement->execute($category);
    }
  }
  $database->exec("UPDATE categories SET image = REPLACE(image, 'Pictures/', 'assets/') WHERE image LIKE 'Pictures/%'");


  /* ---------------- postings ----------------
     A committee opening posted by a client. */
  $database->exec("CREATE TABLE IF NOT EXISTS postings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    category_id VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(2000) NOT NULL DEFAULT '',
    skills_needed VARCHAR(500) NOT NULL DEFAULT '',
    slots INT UNSIGNED NOT NULL DEFAULT 1,
    status VARCHAR(20) NOT NULL DEFAULT 'open',
    image VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
  ) ENGINE=InnoDB");

  // Admin moderation: new postings need approval before they're visible on
  // the public site. Existing postings are backfilled as 'approved' so
  // nothing that was already live suddenly disappears when this ships.
  $hasModerationStatus = (int) $database->query("
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'postings' AND column_name = 'moderation_status'
  ")->fetchColumn();
  if (!$hasModerationStatus) {
    $database->exec("ALTER TABLE postings ADD COLUMN moderation_status VARCHAR(20) NOT NULL DEFAULT 'pending' AFTER status");
    $database->exec("UPDATE postings SET moderation_status = 'approved'");
  }

  if ((int) $database->query('SELECT COUNT(*) FROM postings')->fetchColumn() === 0) {
    $seedOrgs = [
      [
        'name' => 'Information Technology Society',
        'email' => 'its@norsu.edu.ph',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'client',
        'org_name' => 'Information Technology Organization (ITO / ITS)',
        'program' => 'Bachelor of Science in Information Technology',
        'bio' => 'The official academic student organization of the Information Technology department at Negros Oriental State University.'
      ],
      [
        'name' => 'League of Student Organizations',
        'email' => 'lso@norsu.edu.ph',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'client',
        'org_name' => 'League of Student Organizations (LSO)',
        'program' => 'Student Affairs Office',
        'bio' => 'The umbrella organization overseeing and coordinating all recognized student groups, clubs, and events across NORSU.'
      ],
      [
        'name' => 'Computer Science Guild',
        'email' => 'csg@norsu.edu.ph',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'client',
        'org_name' => 'Computer Science Guild (CSG)',
        'program' => 'Bachelor of Science in Computer Science',
        'bio' => 'Student organization fostering software engineering, algorithm competitions, and tech innovation at NORSU.'
      ],
      [
        'name' => 'NORSU Red Cross Youth Council',
        'email' => 'rcy@norsu.edu.ph',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'client',
        'org_name' => 'NORSU Red Cross Youth Council',
        'program' => 'Health & Community Services',
        'bio' => 'University chapter committed to humanitarian work, disaster response preparedness, and campus health drives.'
      ]
    ];

    $userInsert = $database->prepare('INSERT INTO users (name, email, password, role, client_type, org_name, program, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $orgIds = [];
    foreach ($seedOrgs as $org) {
      $existingId = $database->prepare('SELECT id FROM users WHERE email = ?');
      $existingId->execute([$org['email']]);
      $id = $existingId->fetchColumn();
      if (!$id) {
        $userInsert->execute([$org['name'], $org['email'], $org['password'], $org['role'], 'organization', $org['org_name'], $org['program'], $org['bio']]);
        $id = $database->lastInsertId();
      }
      $orgIds[$org['email']] = (int) $id;
    }

    $seedPostings = [
      [
        'client_id' => $orgIds['its@norsu.edu.ph'],
        'category_id' => 'technicals',
        'title' => 'AV & Live Stream Technical Crew',
        'description' => 'Operate audio mixers, stage microphones, LED monitors, and manage YouTube/FB live stream broadcasts for IT assemblies and symposiums.',
        'skills_needed' => 'Audio/Visual setup, OBS Studio / vMix, cable management, hardware diagnostics',
        'slots' => 4,
        'status' => 'open',
        'moderation_status' => 'approved'
      ],
      [
        'client_id' => $orgIds['csg@norsu.edu.ph'],
        'category_id' => 'technicals',
        'title' => 'Hackathon & Lab Technical Support Aide',
        'description' => 'Configure LAN networking, maintain testing workstations, and resolve technical issues for student programmers during university hackathons.',
        'skills_needed' => 'Basic networking, Linux / Windows setup, router configuration, troubleshooting',
        'slots' => 3,
        'status' => 'open',
        'moderation_status' => 'approved'
      ],
      [
        'client_id' => $orgIds['its@norsu.edu.ph'],
        'category_id' => 'documentation',
        'title' => 'Photo & Video Documentation Specialist',
        'description' => 'Capture high-resolution photos and video highlights of all departmental events, draft caption stories, and curate the media repository.',
        'skills_needed' => 'DSLR/Mirrorless camera operation, Adobe Lightroom / Premiere, creative storytelling',
        'slots' => 3,
        'status' => 'open',
        'moderation_status' => 'approved'
      ],
      [
        'client_id' => $orgIds['lso@norsu.edu.ph'],
        'category_id' => 'documentation',
        'title' => 'Campus Event Minutes & Media Archivist',
        'description' => 'Record committee proceedings, draft official press releases for student publications, and archive event documentation portfolios.',
        'skills_needed' => 'Technical writing, documentation filing, Google Workspace / MS Office, attention to detail',
        'slots' => 2,
        'status' => 'open',
        'moderation_status' => 'approved'
      ],
      [
        'client_id' => $orgIds['lso@norsu.edu.ph'],
        'category_id' => 'decorations',
        'title' => 'Stage Backdrop & Creative Production Team',
        'description' => 'Conceptualize, craft, and assemble thematic stage backgrounds, entrance installations, and floral/podium arrangements for university festivities.',
        'skills_needed' => 'Visual arts, backdrop fabrication, stage lighting concepts, craft craftsmanship',
        'slots' => 4,
        'status' => 'open',
        'moderation_status' => 'approved'
      ],
      [
        'client_id' => $orgIds['rcy@norsu.edu.ph'],
        'category_id' => 'decorations',
        'title' => 'Exhibition Booth & Poster Designer',
        'description' => 'Design informative and visually striking advocacy booths, health fair exhibits, and campus bulletin board displays.',
        'skills_needed' => 'Graphic design, layout planning, poster printing coordination, creative styling',
        'slots' => 2,
        'status' => 'open',
        'moderation_status' => 'approved'
      ],
      [
        'client_id' => $orgIds['lso@norsu.edu.ph'],
        'category_id' => 'logistics',
        'title' => 'University Arena Logistics & Floor Coordinator',
        'description' => 'Manage stage ingress/egress, transport sound and furniture equipment, oversee delegate registration booths, and enforce event timetables.',
        'skills_needed' => 'Physical inventory, teamwork, time management, crowd coordination',
        'slots' => 6,
        'status' => 'open',
        'moderation_status' => 'approved'
      ],
      [
        'client_id' => $orgIds['rcy@norsu.edu.ph'],
        'category_id' => 'logistics',
        'title' => 'First-Aid Station & Supply Logistics Officer',
        'description' => 'Organize first-aid response posts, track emergency supplies and medical inventory, and assist marshals during campus mass gatherings.',
        'skills_needed' => 'Inventory tracking, emergency response awareness, orderly coordination',
        'slots' => 3,
        'status' => 'open',
        'moderation_status' => 'approved'
      ]
    ];

    $postStmt = $database->prepare('INSERT INTO postings (client_id, category_id, title, description, skills_needed, slots, status, moderation_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($seedPostings as $post) {
      $postStmt->execute([
        $post['client_id'],
        $post['category_id'],
        $post['title'],
        $post['description'],
        $post['skills_needed'],
        $post['slots'],
        $post['status'],
        $post['moderation_status']
      ]);
    }
  }

  /* ---------------- applications ----------------
     status: pending | reviewed | accepted | declined | withdrawn */
  $database->exec("CREATE TABLE IF NOT EXISTS applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    aspirant_id INT UNSIGNED NOT NULL,
    posting_id INT UNSIGNED NOT NULL,
    note VARCHAR(1000) NOT NULL DEFAULT '',
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    status_seen TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    decided_at DATETIME NULL DEFAULT NULL,
    UNIQUE KEY unique_application (aspirant_id, posting_id),
    FOREIGN KEY (aspirant_id) REFERENCES users(id),
    FOREIGN KEY (posting_id) REFERENCES postings(id)
  ) ENGINE=InnoDB");

  $hasSeenByClient = (int) $database->query("
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'applications' AND column_name = 'seen_by_client'
  ")->fetchColumn();
  if (!$hasSeenByClient) {
    $database->exec("ALTER TABLE applications ADD COLUMN seen_by_client TINYINT(1) NOT NULL DEFAULT 0 AFTER status_seen");
    $database->exec("UPDATE applications SET seen_by_client = 1"); // don't flood clients with notifications for applications that already existed
  }

  /* ---------------- ratings ----------------
     Left by a client for an aspirant once a term/role has ended (mirrors
     the star ratings shown on the profile-preview mock-up). */
  $database->exec("CREATE TABLE IF NOT EXISTS ratings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    aspirant_id INT UNSIGNED NOT NULL,
    client_id INT UNSIGNED NOT NULL,
    application_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment VARCHAR(500) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rating (application_id),
    FOREIGN KEY (aspirant_id) REFERENCES users(id),
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (application_id) REFERENCES applications(id)
  ) ENGINE=InnoDB");

  /* ---------------- chat ---------------- */
  $database->exec("CREATE TABLE IF NOT EXISTS chat_threads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    guest_token VARCHAR(64) NULL,
    guest_name VARCHAR(255) NULL,
    last_message_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user (user_id),
    UNIQUE KEY unique_guest_token (guest_token),
    FOREIGN KEY (user_id) REFERENCES users(id)
  ) ENGINE=InnoDB");

  $database->exec("CREATE TABLE IF NOT EXISTS chat_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    thread_id INT UNSIGNED NOT NULL,
    sender VARCHAR(10) NOT NULL,
    message VARCHAR(2000) NOT NULL,
    read_by_admin TINYINT(1) NOT NULL DEFAULT 0,
    read_by_client TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES chat_threads(id)
  ) ENGINE=InnoDB");

  /* ---------------- reports ----------------
     A user reporting a posting or an account as inappropriate. target_id
     points at postings.id or users.id depending on target_type; the label
     is a snapshot so the report still reads clearly even if the reported
     posting/account is later deleted. */
  $database->exec("CREATE TABLE IF NOT EXISTS reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT UNSIGNED NULL,
    reporter_name VARCHAR(255) NOT NULL DEFAULT 'Guest',
    target_type VARCHAR(20) NOT NULL,
    target_id INT UNSIGNED NOT NULL,
    target_label VARCHAR(255) NOT NULL DEFAULT '',
    reason VARCHAR(1000) NOT NULL DEFAULT '',
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME NULL DEFAULT NULL,
    FOREIGN KEY (reporter_id) REFERENCES users(id)
  ) ENGINE=InnoDB");

  /* ---------------- activity_log ----------------
     A simple audit trail of moderation and admin actions, shown on the
     Activity tab so the admin can monitor what's happened on the platform. */
  $database->exec("CREATE TABLE IF NOT EXISTS activity_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_name VARCHAR(255) NOT NULL DEFAULT 'System',
    action VARCHAR(255) NOT NULL,
    details VARCHAR(500) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB");

  return $database;
}