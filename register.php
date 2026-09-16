<?php
session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/Validation.php';

if (!empty($_SESSION['user_id'])) {
  header('Location: account.php');
  exit;
}

function safeNextUrl(?string $next): ?string
{
  if (!$next) {
    return null;
  }
  if (preg_match('~^(?!//)(?!https?:)[A-Za-z0-9_\-]+\.php(\?[A-Za-z0-9_\-=&%.]*)?(#[A-Za-z0-9_\-]*)?$~', $next)) {
    return $next;
  }
  return null;
}

$nextUrl = safeNextUrl($_GET['next'] ?? $_POST['next'] ?? null);
$database = getDatabase();
$error = '';

$accountType = $_POST['account_type'] ?? ($_GET['type'] ?? $_GET['role'] ?? 'aspirant');
if (!in_array($accountType, ['aspirant', 'organization_client', 'independent_client', 'client'], true)) {
  $accountType = 'aspirant';
}
if ($accountType === 'client') {
  $accountType = 'organization_client';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = $_POST['password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';

  if ($accountType === 'organization_client') {
    $role = 'client';
    $clientType = 'organization';
    $orgName = trim($_POST['org_name'] ?? '');
    $name = trim($_POST['org_rep_name'] ?? $_POST['name'] ?? '');
    if ($name === '' && $orgName !== '') {
      $name = $orgName;
    }
    $email = strtolower(trim($_POST['org_email'] ?? $_POST['email'] ?? ''));
    $program = '';
    $yearLevel = '';
    $skills = '';
  } elseif ($accountType === 'independent_client') {
    $role = 'client';
    $clientType = 'independent';
    $name = trim($_POST['ind_name'] ?? $_POST['name'] ?? '');
    $email = strtolower(trim($_POST['ind_email'] ?? $_POST['email'] ?? ''));
    $orgName = trim($_POST['ind_project'] ?? $_POST['org_name'] ?? '');
    $program = '';
    $yearLevel = '';
    $skills = '';
  } else {
    $role = 'aspirant';
    $clientType = '';
    $name = trim($_POST['aspirant_name'] ?? $_POST['name'] ?? '');
    $email = strtolower(trim($_POST['aspirant_email'] ?? $_POST['email'] ?? ''));
    $orgName = '';
    $program = trim($_POST['program'] ?? '');
    $yearLevel = trim($_POST['year_level'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
  }

  $error = validateRegistrationFields($name, $email, $password, $confirmPassword, $role, $clientType, $orgName);

  if ($error === '') {
    try {
      $statement = $database->prepare('INSERT INTO users (name, email, password, role, client_type, org_name, program, year_level, skills) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $clientType, $orgName, $program, $yearLevel, $skills]);
      $_SESSION['user_id'] = (int) $database->lastInsertId();
      $_SESSION['user_name'] = $name;
      $_SESSION['role'] = $role;
      $_SESSION['client_type'] = $clientType;
      $_SESSION['org_name'] = $orgName;
      unset($_SESSION['is_admin']);
      header('Location: ' . ($nextUrl ?: 'account.php'));
      exit;
    } catch (PDOException $exception) {
      $error = 'That email is already registered.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — ComMEETtee</title>
<link rel="stylesheet" href="style.css?v=<?php echo file_exists(__DIR__ . '/style.css') ? filemtime(__DIR__ . '/style.css') : time(); ?>">
<script src="script.js?v=<?php echo file_exists(__DIR__ . '/script.js') ? filemtime(__DIR__ . '/script.js') : time(); ?>" defer></script>
</head>
<body>
<main class="auth-page register-page">
  <form class="auth-card" method="post">
    <div class="eyebrow">ComMEETtee</div>
    <h1 class="display">Join ComMEETtee</h1>
    <p class="form-intro">Choose your account type to get started.</p>

    <div class="role-toggle role-toggle--3" role="tablist" aria-label="Account type">
      <button type="button" class="role-toggle-btn <?php echo $accountType === 'aspirant' ? 'is-active' : ''; ?>" data-role-btn="aspirant">Aspirant</button>
      <button type="button" class="role-toggle-btn <?php echo $accountType === 'organization_client' ? 'is-active' : ''; ?>" data-role-btn="organization_client">Organization Client</button>
      <button type="button" class="role-toggle-btn <?php echo $accountType === 'independent_client' ? 'is-active' : ''; ?>" data-role-btn="independent_client">Independent Client</button>
    </div>
    <input type="hidden" name="account_type" id="register-role" value="<?php echo htmlspecialchars($accountType); ?>">

    <?php if ($error): ?><p class="form-error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($nextUrl): ?><input type="hidden" name="next" value="<?php echo htmlspecialchars($nextUrl); ?>"><?php endif; ?>

    <!-- ASPIRANT FIELDS -->
    <div data-role-field="aspirant" <?php echo $accountType !== 'aspirant' ? 'hidden' : ''; ?>>
      <label>Full Name<input type="text" name="aspirant_name" autocomplete="name" placeholder="e.g. Maria Santos" <?php echo $accountType === 'aspirant' ? 'required' : 'disabled'; ?>></label>
      <label>Student / Personal Email<input type="email" name="aspirant_email" autocomplete="email" placeholder="e.g. maria@norsu.edu.ph" <?php echo $accountType === 'aspirant' ? 'required' : 'disabled'; ?>></label>
      <label>Program
        <select name="program">
          <option value="">Select your program…</option>
          <?php foreach (norsuProgramOptions() as $programOption): ?>
            <option value="<?php echo htmlspecialchars($programOption); ?>"><?php echo htmlspecialchars($programOption); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Year Level
        <select name="year_level">
          <option value="">Select your year level…</option>
          <?php foreach (yearLevelOptions() as $yearOption): ?>
            <option value="<?php echo htmlspecialchars($yearOption); ?>"><?php echo htmlspecialchars($yearOption); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Skills &amp; Interests<input type="text" name="skills" placeholder="e.g. Graphic Design, Video Editing, Sound Mixing" autocomplete="off"></label>
    </div>

    <!-- ORGANIZATION CLIENT FIELDS -->
    <div data-role-field="organization_client" <?php echo $accountType !== 'organization_client' ? 'hidden' : ''; ?>>
      <label>Organization Name<input type="text" name="org_name" autocomplete="organization" placeholder="e.g. Information Technology Organization (ITO / ITS)" <?php echo $accountType === 'organization_client' ? 'required' : 'disabled'; ?>></label>
      <label>Representative / Officer Name<input type="text" name="org_rep_name" autocomplete="name" placeholder="e.g. Maria Santos (President)" <?php echo $accountType === 'organization_client' ? 'required' : 'disabled'; ?>></label>
      <label>Organization Email / Login<input type="email" name="org_email" autocomplete="email" placeholder="e.g. its@norsu.edu.ph" <?php echo $accountType === 'organization_client' ? 'required' : 'disabled'; ?>></label>
      <p class="field-hint" style="font-size:0.8rem; color:var(--ink-soft); margin-top:-0.4rem; margin-bottom:0.8rem;">You can use this organization email or organization name to log in later.</p>
    </div>

    <!-- INDEPENDENT CLIENT FIELDS -->
    <div data-role-field="independent_client" <?php echo $accountType !== 'independent_client' ? 'hidden' : ''; ?>>
      <label>Full Name<input type="text" name="ind_name" autocomplete="name" placeholder="e.g. Engr. Roberto Gomez" <?php echo $accountType === 'independent_client' ? 'required' : 'disabled'; ?>></label>
      <label>Email (for login)<input type="email" name="ind_email" autocomplete="email" placeholder="e.g. roberto@example.com" <?php echo $accountType === 'independent_client' ? 'required' : 'disabled'; ?>></label>
      <label>Project / Department (Optional)<input type="text" name="ind_project" placeholder="e.g. Campus Hackathon Committee, Event Chair"></label>
    </div>

    <label>Password
      <div class="password-input-wrap">
        <input type="password" name="password" id="register-password" minlength="6" required autocomplete="new-password">
        <button type="button" class="password-toggle" data-toggle-for="register-password" aria-label="Show password" aria-pressed="false">
          <svg class="icon-eye" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
          <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M3 3l18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
      </div>
    </label>
    <label>Confirm Password
      <div class="password-input-wrap">
        <input type="password" name="confirm_password" id="register-confirm-password" minlength="6" required autocomplete="new-password">
        <button type="button" class="password-toggle" data-toggle-for="register-confirm-password" aria-label="Show password" aria-pressed="false">
          <svg class="icon-eye" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
          <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M3 3l18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
      </div>
      <span class="form-error password-match-error" id="confirm-password-error" hidden>Passwords do not match.</span>
    </label>

    <button class="cart-button" type="submit">Create Account</button>
    <a class="back-link" href="login.php">Already have an account? Log in</a>
    <a class="back-link" href="index.php">Back to home</a>
  </form>
</main>
</body>
</html>