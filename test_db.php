<?php
/**
 * Database Connection Status & Verification
 * ComMEETtee
 */
session_start();
require_once __DIR__ . '/database.php';

$error = null;
$db = null;
$tables = [];
$counts = [];

try {
    $db = getDatabase();
    $serverVersion = $db->getAttribute(PDO::ATTR_SERVER_VERSION);
    
    // Fetch all tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    // Fetch counts
    foreach (['categories', 'users', 'postings', 'applications'] as $table) {
        if (in_array($table, $tables, true)) {
            $counts[$table] = (int) $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        }
    }

    // Fetch sample committees
    $categories = $db->query("SELECT id, name, description FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch sample postings
    $samplePostings = $db->query("
        SELECT p.title, p.slots, c.name AS committee_name, u.org_name,
               (SELECT COUNT(*) FROM applications WHERE posting_id = p.id AND status IN ('pending', 'reviewed')) AS applying_count,
               (SELECT COUNT(*) FROM applications WHERE posting_id = p.id AND status = 'accepted') AS accepted_count
        FROM postings p
        JOIN categories c ON c.id = p.category_id
        JOIN users u ON u.id = p.client_id
        WHERE p.status = 'open'
        LIMIT 6
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Database Connection Status — ComMEETtee</title>
<style>
  :root {
    --navy: #15112f;
    --navy-light: #211d54;
    --orange: #f8a430;
    --green: #10b981;
    --bg: #0d0a1e;
    --card-bg: rgba(255, 255, 255, 0.05);
    --border: rgba(255, 255, 255, 0.12);
    --text: #f0eff8;
    --text-muted: rgba(240, 239, 248, 0.65);
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background: var(--bg);
    color: var(--text);
    padding: 2.5rem 1.5rem;
    line-height: 1.5;
  }
  .container { max-width: 900px; margin: 0 auto; }
  .header { margin-bottom: 2rem; }
  .header h1 { font-size: 2rem; color: #fff; margin-bottom: 0.35rem; }
  .badge-success {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.4);
    padding: 0.45rem 1rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-top: 0.5rem;
  }
  .badge-error {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(239, 68, 68, 0.15);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.4);
    padding: 0.45rem 1rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-top: 0.5rem;
  }
  .dot { width: 10px; height: 10px; border-radius: 50%; background: currentColor; }
  .grid-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin: 1.75rem 0;
  }
  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
  }
  .stat-number {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--orange);
  }
  .stat-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 0.25rem;
  }
  .section-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }
  .section-card h2 {
    font-size: 1.2rem;
    color: #fff;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--border);
    padding-bottom: 0.5rem;
  }
  table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
  th, td { padding: 0.65rem 0.75rem; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.08); }
  th { color: var(--orange); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
  .pill {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.75rem;
    background: rgba(248,164,48,0.18);
    color: var(--orange);
    border: 1px solid rgba(248,164,48,0.3);
  }
  .links-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1.5rem;
  }
  .btn {
    display: inline-block;
    padding: 0.65rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: 0.2s ease;
  }
  .btn-orange { background: var(--orange); color: #15112f; }
  .btn-orange:hover { background: #ffb752; }
  .btn-outline { background: transparent; color: #fff; border: 1px solid var(--border); }
  .btn-outline:hover { background: rgba(255,255,255,0.1); }
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <h1>Database Connection Status</h1>
    <p style="color:var(--text-muted);">ComMEETtee &middot; Negros Oriental State University (NORSU)</p>

    <?php if ($error): ?>
      <div class="badge-error">
        <span class="dot"></span> Connection Error: <?php echo htmlspecialchars($error); ?>
      </div>
    <?php else: ?>
      <div class="badge-success">
        <span class="dot"></span> Connected to MySQL Database (<code>commeettee</code>) via PHP PDO
      </div>
    <?php endif; ?>
  </div>

  <?php if (!$error): ?>
    <div class="grid-stats">
      <div class="stat-card">
        <div class="stat-number"><?php echo $counts['categories'] ?? 0; ?></div>
        <div class="stat-label">Committees</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $counts['postings'] ?? 0; ?></div>
        <div class="stat-label">Open Postings</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $counts['users'] ?? 0; ?></div>
        <div class="stat-label">Registered Accounts</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $counts['applications'] ?? 0; ?></div>
        <div class="stat-label">Applications</div>
      </div>
    </div>

    <div class="section-card">
      <h2>Committees Configured in Database</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Direct Link</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td><code><?php echo htmlspecialchars($cat['id']); ?></code></td>
              <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
              <td style="color:var(--text-muted); font-size:0.85rem;"><?php echo htmlspecialchars($cat['description']); ?></td>
              <td>
                <a href="committee.php?type=<?php echo urlencode($cat['id']); ?>" class="pill" style="text-decoration:none;">View &rarr;</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="section-card">
      <h2>Sample Active Postings from Database</h2>
      <table>
        <thead>
          <tr>
            <th>Role Title</th>
            <th>Committee</th>
            <th>Organization</th>
            <th>Committee Slots</th>
            <th>Applying</th>
            <th>Accepted</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($samplePostings as $posting): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($posting['title']); ?></strong></td>
              <td><span class="pill"><?php echo htmlspecialchars($posting['committee_name']); ?></span></td>
              <td><?php echo htmlspecialchars($posting['org_name']); ?></td>
              <td><strong><?php echo (int) $posting['slots']; ?></strong></td>
              <td style="color:#38bdf8; font-weight:700;"><?php echo (int) $posting['applying_count']; ?></td>
              <td style="color:#34d399; font-weight:700;"><?php echo (int) $posting['accepted_count']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="section-card">
      <h2>Quick Navigation</h2>
      <div class="links-group">
        <a href="committee.php?type=technicals" class="btn btn-orange">Open Technical Committee Page</a>
        <a href="index.php" class="btn btn-outline">Go to Homepage</a>
        <a href="account.php" class="btn btn-outline">Go to Dashboard</a>
      </div>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
