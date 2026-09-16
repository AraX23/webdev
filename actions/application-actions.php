<?php
/**
 * Handles the "Apply" / "Withdraw" form posts from the postings grid on
 * index.php. AJAX requests (fetch) hit applications.php instead — this
 * file only covers the plain-HTML-form fallback, so the page still works
 * with JavaScript disabled.
 *
 * $message is read here (not in includes/site-data.php) because index.php
 * displays it right above the postings grid.
 */

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../Validation.php';
$database = getDatabase();

$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $action = $_POST['action'];
  $postingId = (int) ($_POST['posting_id'] ?? 0);

  if (!isLoggedIn()) {
    $_SESSION['flash_message'] = 'Please log in as an Aspirant to apply.';
    header('Location: ../login.php');
    exit;
  }

  if (!isAspirant()) {
    $_SESSION['flash_message'] = 'Only Aspirant accounts can apply to postings.';
    header('Location: ../index.php');
    exit;
  }

  $aspirantId = (int) $_SESSION['user_id'];

  if ($action === 'apply' && $postingId) {
    $statement = $database->prepare("SELECT slots, (SELECT COUNT(*) FROM applications WHERE posting_id = postings.id AND status = 'accepted') AS filled FROM postings WHERE id = ? AND status = 'open'");
    $statement->execute([$postingId]);
    $posting = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$posting) {
      $_SESSION['flash_message'] = 'That posting is no longer available.';
    } elseif ((int) $posting['filled'] >= (int) $posting['slots']) {
      $_SESSION['flash_message'] = 'That posting is already full.';
    } else {
      $insertStatement = $database->prepare('
        INSERT INTO applications (aspirant_id, posting_id, status)
        VALUES (?, ?, "pending")
        ON DUPLICATE KEY UPDATE status = "pending", created_at = CURRENT_TIMESTAMP
      ');
      $insertStatement->execute([$aspirantId, $postingId]);
      $_SESSION['flash_message'] = 'Application sent! You are now applying for this committee role.';
    }
  } elseif ($action === 'withdraw' && $postingId) {
    $database->prepare("DELETE FROM applications WHERE aspirant_id = ? AND posting_id = ?")->execute([$aspirantId, $postingId]);
    $_SESSION['flash_message'] = 'Application withdrawn.';
  }

  $redirect = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : 'index.php#committees';
  if (!preg_match('#^(https?://|/)#', $redirect)) {
    $redirect = '../' . ltrim($redirect, '/');
  }
  header('Location: ' . $redirect);
  exit;
}