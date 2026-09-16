<div class="nav-actions">
  <?php if (!empty($_SESSION['user_id'])): ?>
    <?php if (!empty($_SESSION['is_admin'])): ?>
      <a href="adminfiles/admin.php" class="btn btn--dark" style="padding:0.45rem 1rem; font-size:0.85rem;">Admin Panel</a>
    <?php else: ?>
      <a href="account.php" class="link-ghost" style="font-weight:bold;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Dashboard'); ?></a>
      <span class="divider" aria-hidden="true"></span>
      <a href="account.php" class="link-ghost">Dashboard</a>
    <?php endif; ?>
    <span class="divider" aria-hidden="true"></span>
    <a href="logout.php" class="link-ghost">Log Out</a>
  <?php else: ?>
    <a href="login.php" class="link-ghost">LOG IN</a>
    <span class="divider" aria-hidden="true"></span>
    <a href="register.php" class="link-ghost">SIGN UP</a>
  <?php endif; ?>
  <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
    <span></span>
    <span></span>
    <span></span>
  </button>
</div>
