<?php
session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/Validation.php';

$database = getDatabase();

$categories = $database->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

// Determine active committee type
$selectedType = trim($_GET['type'] ?? $_GET['category'] ?? '');
$validCategoryIds = array_column($categories, 'id');

if (!in_array($selectedType, $validCategoryIds, true)) {
  // Default to 'technicals' if available, otherwise first category
  $selectedType = in_array('technicals', $validCategoryIds, true) ? 'technicals' : ($validCategoryIds[0] ?? '');
}

// Fetch current category details
$currentCategory = null;
foreach ($categories as $cat) {
  if ($cat['id'] === $selectedType) {
    $currentCategory = $cat;
    break;
  }
}

// Fetch organizations with approved open postings for this committee
$postingsStmt = $database->prepare("
  SELECT postings.*,
         users.name AS client_name,
         users.org_name,
         users.email AS client_email,
         users.avatar AS client_avatar,
         (SELECT COUNT(*) FROM applications WHERE posting_id = postings.id AND status = 'accepted') AS accepted_count,
         (SELECT COUNT(*) FROM applications WHERE posting_id = postings.id AND status IN ('pending', 'reviewed')) AS applying_count,
         (SELECT COUNT(*) FROM applications WHERE posting_id = postings.id AND status = 'accepted') AS filled_count
  FROM postings
  JOIN users ON users.id = postings.client_id
  WHERE postings.category_id = ?
    AND postings.status = 'open'
    AND (postings.moderation_status = 'approved' OR postings.moderation_status IS NULL)
  ORDER BY postings.created_at DESC
");
$postingsStmt->execute([$selectedType]);
$postings = $postingsStmt->fetchAll(PDO::FETCH_ASSOC);

// If aspirant is logged in, check which postings they applied to
$myApplications = [];
if (isLoggedIn() && isAspirant()) {
  $myAppsStmt = $database->prepare("SELECT posting_id, status FROM applications WHERE aspirant_id = ?");
  $myAppsStmt->execute([$_SESSION['user_id']]);
  while ($row = $myAppsStmt->fetch(PDO::FETCH_ASSOC)) {
    $myApplications[$row['posting_id']] = $row['status'];
  }
}

$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($currentCategory['name'] ?? 'Committees'); ?> — ComMEETtee</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Climate+Crisis&display=swap" rel="stylesheet">
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
        <li class="has-dropdown">
          <button class="nav-pill nav-pill-size" aria-expanded="false">Committees <svg class="chev" viewBox="0 0 12 8" width="10" height="7"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none"/></svg></button>
          <ul class="dropdown">
            <?php foreach ($categories as $cat): ?>
              <li><a href="committee.php?type=<?php echo urlencode($cat['id']); ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li><a href="index.php#aspirants" class="nav-pill">Aspirants</a></li>
        <li><a href="index.php#clients" class="nav-pill">Clients</a></li>
        <li><a href="contact.php" class="nav-pill">Contact</a></li>
      </ul>
    </nav>
    <?php include __DIR__ . '/includes/nav-actions.php'; ?>
  </div>
</header>

<main class="committee-page">
  <div class="wrap">
    <!-- Top Header Banner -->
    <div class="committee-header">
      <p class="eyebrow eyebrow--dark">Committee Exploration</p>
      <h1 class="committee-title"><?php echo htmlspecialchars($currentCategory['name'] ?? 'Committees'); ?> Committee</h1>
      <p class="committee-desc">
        <?php echo htmlspecialchars($currentCategory['description'] ?? 'Browse campus organizations seeking committee members.'); ?>
      </p>

      <!-- Committee Selection Buttons -->
      <div class="committee-selector-wrap" aria-label="Select committee type">
        <div class="committee-buttons-group">
          <?php foreach ($categories as $cat): ?>
            <?php $isActive = ($cat['id'] === $selectedType); ?>
            <a href="committee.php?type=<?php echo urlencode($cat['id']); ?>"
               class="committee-type-btn <?php echo $isActive ? 'is-active' : ''; ?>">
               <?php echo htmlspecialchars($cat['name']); ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="account-flash-msg" style="max-width: 900px; margin: 1.5rem auto 0; text-align: center;">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <!-- Openings / Organizations Section -->
    <section class="committee-openings-section">
      <div class="committee-openings-head">
        <h2>Organizations Looking for <?php echo htmlspecialchars($currentCategory['name'] ?? ''); ?> Members</h2>
        <span class="openings-count-badge"><?php echo count($postings); ?> Opening<?php echo count($postings) === 1 ? '' : 's'; ?> Available</span>
      </div>

      <?php if (empty($postings)): ?>
        <div class="committee-empty-card">
          <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 8v4"/>
            <path d="M12 16h.01"/>
          </svg>
          <h3>No Openings Listed Yet</h3>
          <p>There are currently no active openings posted by student organizations for the <?php echo htmlspecialchars($currentCategory['name'] ?? ''); ?> Committee.</p>
          <div class="empty-card-actions">
            <a href="account.php" class="btn btn--orange">Post an Opening for Your Org</a>
            <a href="index.php#committees" class="btn btn--outline">Browse Other Committees</a>
          </div>
        </div>
      <?php else: ?>
        <div class="committee-grid">
          <?php foreach ($postings as $posting): ?>
            <?php
              $remainingSlots = max(0, (int) $posting['slots'] - (int) $posting['accepted_count']);
              $orgDisplayName = !empty($posting['org_name']) ? $posting['org_name'] : $posting['client_name'];
              $myStatus = $myApplications[$posting['id']] ?? null;
              $isFull = ($remainingSlots === 0);
            ?>
            <article class="committee-org-card">
              <!-- Org identity header -->
              <div class="org-card-header">
                <div class="org-avatar-badge">
                  <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </div>
                <div class="org-header-text">
                  <h3 class="org-name"><?php echo htmlspecialchars($orgDisplayName); ?></h3>
                  <span class="org-verified-label">NORSU Student Organization</span>
                </div>
              </div>

              <!-- Posting role title & slots badge -->
              <div class="org-card-role-strip">
                <h4 class="posting-role-title"><?php echo htmlspecialchars($posting['title']); ?></h4>
                <div class="slots-pill <?php echo $isFull ? 'slots-pill--full' : ''; ?>">
                  <?php if ($isFull): ?>
                    Slots Filled
                  <?php else: ?>
                    <strong><?php echo $remainingSlots; ?></strong> Slot<?php echo $remainingSlots === 1 ? '' : 's'; ?> Left
                  <?php endif; ?>
                </div>
              </div>

              <!-- Committee Numbers / Metrics Grid -->
              <div class="org-card-metrics-grid">
                <div class="metric-col metric-col--target">
                  <span class="metric-number"><?php echo (int) $posting['slots']; ?></span>
                  <span class="metric-caption">Committee Slots</span>
                </div>
                <div class="metric-col metric-col--applying">
                  <span class="metric-number"><?php echo (int) $posting['applying_count']; ?></span>
                  <span class="metric-caption">Aspirants Applying</span>
                </div>
                <div class="metric-col metric-col--accepted">
                  <span class="metric-number"><?php echo (int) $posting['accepted_count']; ?></span>
                  <span class="metric-caption">Already Accepted</span>
                </div>
              </div>

              <!-- Responsibilities & Description -->
              <div class="org-card-block">
                <h5 class="org-card-block-title">Responsibilities</h5>
                <p class="org-card-block-content">
                  <?php echo nl2br(htmlspecialchars($posting['description'])); ?>
                </p>
              </div>

              <!-- Skills & Qualifications -->
              <?php if (!empty($posting['skills_needed'])): ?>
                <div class="org-card-block">
                  <h5 class="org-card-block-title">Skills &amp; Qualifications Needed</h5>
                  <div class="skills-tag-cloud">
                    <?php
                      $skillsList = array_filter(array_map('trim', explode(',', $posting['skills_needed'])));
                      foreach ($skillsList as $skill):
                    ?>
                      <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Card Action / Application form -->
              <div class="org-card-footer">
                <span class="posted-date">Posted <?php echo date('M j, Y', strtotime($posting['created_at'])); ?></span>

                <div class="org-card-action">
                  <?php if (!isLoggedIn()): ?>
                    <a href="login.php?next=<?php echo urlencode('committee.php?type=' . $selectedType); ?>" class="btn btn--orange btn--sm">Log In to Apply</a>
                  <?php elseif (isAspirant()): ?>
                    <?php if ($myStatus): ?>
                      <div class="app-status-wrap">
                        <span class="application-status application-status--<?php echo htmlspecialchars($myStatus); ?>">
                          <?php echo htmlspecialchars(ucfirst($myStatus)); ?>
                        </span>
                        <?php if (in_array($myStatus, ['pending', 'reviewed'], true)): ?>
                          <form action="actions/application-actions.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="withdraw">
                            <input type="hidden" name="posting_id" value="<?php echo (int) $posting['id']; ?>">
                            <input type="hidden" name="redirect_to" value="committee.php?type=<?php echo urlencode($selectedType); ?>">
                            <button type="submit" class="link-withdraw">Withdraw</button>
                          </form>
                        <?php endif; ?>
                      </div>
                    <?php elseif ($isFull): ?>
                      <button class="btn btn--disabled btn--sm" disabled>Slots Full</button>
                    <?php else: ?>
                      <form action="actions/application-actions.php" method="post">
                        <input type="hidden" name="action" value="apply">
                        <input type="hidden" name="posting_id" value="<?php echo (int) $posting['id']; ?>">
                        <input type="hidden" name="redirect_to" value="committee.php?type=<?php echo urlencode($selectedType); ?>">
                        <button type="submit" class="btn btn--orange btn--sm">Apply for Role</button>
                      </form>
                    <?php endif; ?>
                  <?php elseif (isClient()): ?>
                    <span class="client-role-hint">Client Account</span>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
