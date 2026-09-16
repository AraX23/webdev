<?php
session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/Validation.php';

requireLogin();

if (isAdmin()) {
  header('Location: adminfiles/admin.php');
  exit;
}

$database = getDatabase();
$userStatement = $database->prepare('SELECT * FROM users WHERE id = ?');
$userStatement->execute([$_SESSION['user_id']]);
$profile = $userStatement->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
  session_unset();
  session_destroy();
  header('Location: login.php');
  exit;
}

$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

$role = $profile['role'];

if ($role === 'aspirant') {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_profile') {
      $program = trim($_POST['program'] ?? '');
      $yearLevel = trim($_POST['year_level'] ?? '');
      $skills = trim($_POST['skills'] ?? '');
      $availability = trim($_POST['availability'] ?? '');
      $bio = trim($_POST['bio'] ?? '');

      $update = $database->prepare('UPDATE users SET program = ?, year_level = ?, skills = ?, availability = ?, bio = ? WHERE id = ?');
      $update->execute([$program, $yearLevel, $skills, $availability, $bio, $profile['id']]);
      $_SESSION['flash_message'] = 'Profile saved successfully.';
      header('Location: account.php');
      exit;
    }
  }

  $appsStatement = $database->prepare("
    SELECT applications.*, postings.title AS posting_title, categories.name AS category_name,
           clients.name AS client_name, clients.org_name AS client_org
    FROM applications
    JOIN postings ON postings.id = applications.posting_id
    JOIN categories ON categories.id = postings.category_id
    JOIN users AS clients ON clients.id = postings.client_id
    WHERE applications.aspirant_id = ?
    ORDER BY applications.created_at DESC
  ");
  $appsStatement->execute([$profile['id']]);
  $myApplications = $appsStatement->fetchAll(PDO::FETCH_ASSOC);

  // Committees with active opening counts for exploration
  $dashCommittees = $database->query("
    SELECT categories.*,
           (SELECT COUNT(*) FROM postings
            WHERE postings.category_id = categories.id
              AND postings.status = 'open'
              AND (postings.moderation_status = 'approved' OR postings.moderation_status IS NULL)
           ) AS openings_count
    FROM categories
    ORDER BY CASE categories.id
      WHEN 'technicals' THEN 1
      WHEN 'documentation' THEN 2
      WHEN 'decorations' THEN 3
      WHEN 'logistics' THEN 4
      ELSE 5
    END ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

} elseif ($role === 'client') {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create_posting') {
      $title = trim($_POST['title'] ?? '');
      $categoryId = trim($_POST['category_id'] ?? '');
      $description = trim($_POST['description'] ?? '');
      $skillsNeeded = trim($_POST['skills_needed'] ?? '');
      $slots = max(1, (int) ($_POST['slots'] ?? 1));

      if ($title === '' || $categoryId === '') {
        $_SESSION['flash_message'] = 'Title and category are required.';
      } else {
        $stmt = $database->prepare('INSERT INTO postings (client_id, category_id, title, description, skills_needed, slots, status, moderation_status) VALUES (?, ?, ?, ?, ?, ?, "open", "pending")');
        $stmt->execute([$profile['id'], $categoryId, $title, $description, $skillsNeeded, $slots]);
        $_SESSION['flash_message'] = 'Opening submitted for moderation. It will appear on the site once approved.';
      }
      header('Location: account.php');
      exit;
    } elseif ($action === 'close_posting') {
      $postingId = (int) ($_POST['posting_id'] ?? 0);
      $database->prepare('UPDATE postings SET status = "closed" WHERE id = ? AND client_id = ?')->execute([$postingId, $profile['id']]);
      $_SESSION['flash_message'] = 'Posting closed.';
      header('Location: account.php');
      exit;
    } elseif ($action === 'reopen_posting') {
      $postingId = (int) ($_POST['posting_id'] ?? 0);
      $database->prepare('UPDATE postings SET status = "open" WHERE id = ? AND client_id = ?')->execute([$postingId, $profile['id']]);
      $_SESSION['flash_message'] = 'Posting reopened.';
      header('Location: account.php');
      exit;
    } elseif ($action === 'decide_application') {
      $applicationId = (int) ($_POST['application_id'] ?? 0);
      $decision = $_POST['decision'] ?? '';
      if (in_array($decision, ['accepted', 'declined'], true)) {
        $database->prepare("
          UPDATE applications
          SET status = ?, decided_at = CURRENT_TIMESTAMP, status_seen = 0
          WHERE id = ? AND posting_id IN (SELECT id FROM postings WHERE client_id = ?)
        ")->execute([$decision, $applicationId, $profile['id']]);
        $_SESSION['flash_message'] = 'Application marked as ' . htmlspecialchars($decision) . '.';
      }
      header('Location: account.php');
      exit;
    }
  }

  $categories = $database->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

  $postingsStmt = $database->prepare("
    SELECT postings.*, categories.name AS category_name,
           (SELECT COUNT(*) FROM applications WHERE posting_id = postings.id) AS applicant_count
    FROM postings
    JOIN categories ON categories.id = postings.category_id
    WHERE postings.client_id = ?
    ORDER BY postings.created_at DESC
  ");
  $postingsStmt->execute([$profile['id']]);
  $myPostings = $postingsStmt->fetchAll(PDO::FETCH_ASSOC);

  $appStmt = $database->prepare("
    SELECT applications.*, users.name AS aspirant_name, users.program, users.year_level, users.skills AS aspirant_skills,
           postings.title AS posting_title
    FROM applications
    JOIN users ON users.id = applications.aspirant_id
    JOIN postings ON postings.id = applications.posting_id
    WHERE postings.client_id = ?
    ORDER BY applications.created_at DESC
  ");
  $appStmt->execute([$profile['id']]);
  $applicants = $appStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Dashboard — ComMEETtee</title>
<link rel="stylesheet" href="style.css?v=<?php echo file_exists(__DIR__ . '/style.css') ? filemtime(__DIR__ . '/style.css') : time(); ?>">
<script src="script.js?v=<?php echo file_exists(__DIR__ . '/script.js') ? filemtime(__DIR__ . '/script.js') : time(); ?>" defer></script>
</head>
<body>

<header class="site-header">
  <div class="nav-wrap">
    <a href="index.php" class="brand" aria-label="HOME"><img src="assets/logo.png" alt="ComMEETtee" class="brand-logo"></a>
    <nav class="primary-nav" aria-label="Primary">
      <ul>
        <li><a href="index.php" class="nav-pill">Home</a></li>
        <li><a href="index.php#aspirants" class="nav-pill">Aspirants</a></li>
        <li><a href="index.php#clients" class="nav-pill">Clients</a></li>
        <li class="has-dropdown">
          <button class="nav-pill nav-pill-size" aria-expanded="false">Committees <svg class="chev" viewBox="0 0 12 8" width="10" height="7"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none"/></svg></button>
          <ul class="dropdown">
            <li><a href="committee.php?type=technicals">Technicals</a></li>
            <li><a href="committee.php?type=documentation">Documentation</a></li>
            <li><a href="committee.php?type=decorations">Decorations</a></li>
            <li><a href="committee.php?type=logistics">Logistics</a></li>
          </ul>
        </li>
        <li><a href="contact.php" class="nav-pill">Contact</a></li>
      </ul>
    </nav>
    <?php include __DIR__ . '/includes/nav-actions.php'; ?>
  </div>
</header>

<main class="account-page-wrap">
  <div class="account-container">
    <div class="account-header">
      <div class="account-header-info">
        <div class="profile-avatar-placeholder"><?php echo htmlspecialchars(strtoupper(substr($profile['name'], 0, 1))); ?></div>
        <div>
          <h1><?php echo htmlspecialchars($profile['name']); ?></h1>
          <p class="profile-panel-hint" style="margin:0;"><?php echo htmlspecialchars($profile['email']); ?> · <span class="account-badge"><?php echo htmlspecialchars(ucfirst($role)); ?></span></p>
        </div>
      </div>
      <a href="logout.php" class="btn btn--outline" style="border:1px solid var(--gray-400); padding:0.5rem 1.2rem; font-size:0.85rem;">Log Out</a>
    </div>

    <?php if ($message): ?>
      <div class="account-flash-msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php
    if ($role === 'aspirant') {
      include __DIR__ . '/account/aspirant-dashboard.php';
    } elseif ($role === 'client') {
      include __DIR__ . '/account/client-dashboard.php';
    }
    ?>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
