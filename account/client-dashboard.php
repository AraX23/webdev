<?php
$isOrg = ($profile['client_type'] ?? '') === 'organization' || !empty($profile['org_name']);
$orgTitle = !empty($profile['org_name']) ? $profile['org_name'] : $profile['name'];
?>

<div class="dash-client-info" style="background:#f4f0ff; border:1px solid #dcd2f3; border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
  <div>
    <?php if ($isOrg): ?>
      <span class="account-badge" style="background:var(--navy); color:#fff; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.04em;">Organization Client</span>
      <h3 style="margin:0.3rem 0 0; color:var(--navy); font-size:1.2rem;"><?php echo htmlspecialchars($orgTitle); ?></h3>
      <p class="profile-panel-hint" style="margin:0.2rem 0 0;">Representative: <strong><?php echo htmlspecialchars($profile['name']); ?></strong> · Organization Email: <strong><?php echo htmlspecialchars($profile['email']); ?></strong></p>
    <?php else: ?>
      <span class="account-badge" style="background:#0284c7; color:#fff; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.04em;">Independent Client</span>
      <h3 style="margin:0.3rem 0 0; color:var(--navy); font-size:1.2rem;"><?php echo htmlspecialchars($profile['name']); ?></h3>
      <p class="profile-panel-hint" style="margin:0.2rem 0 0;">Email: <strong><?php echo htmlspecialchars($profile['email']); ?></strong><?php echo !empty($profile['org_name']) ? ' · Project/Affiliation: <strong>' . htmlspecialchars($profile['org_name']) . '</strong>' : ''; ?></p>
    <?php endif; ?>
  </div>
  <div>
    <a href="committee.php" class="btn btn--outline" style="border:1px solid var(--navy); color:var(--navy); padding:0.45rem 1rem; font-size:0.85rem; text-decoration:none; font-weight:600; border-radius:6px;">Browse Committee Openings &rarr;</a>
  </div>
</div>

<section class="dash-section">
  <h2>Post a New Opening</h2>
  <p class="profile-panel-hint" style="margin-top:-0.5rem; margin-bottom:1rem;">Openings posted here are automatically added to the database and will appear immediately on the committee page for students to apply.</p>
  <form method="post" class="account-form">
    <input type="hidden" name="action" value="create_posting">
    <label>Title<input type="text" name="title" required placeholder="e.g. Technical Crew Member, Graphic Artist"></label>
    <label>Committee Category
      <select name="category_id" required>
        <?php foreach ($categories as $category): ?>
          <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Responsibilities &amp; Description<textarea name="description" rows="3" placeholder="Describe the responsibilities and duties of this committee role…"></textarea></label>
    <label>Skills &amp; Qualifications Needed<input type="text" name="skills_needed" placeholder="e.g. Sound mixing, OBS, Photography, Teamwork (comma separated)"></label>
    <label>Number of Slots<input type="number" name="slots" min="1" value="1"></label>
    <button class="cart-button" type="submit">Post Opening</button>
  </form>
</section>

<section class="dash-section">
  <h2>My Postings</h2>
  <?php if (!$myPostings): ?>
    <p class="postings-empty">You haven't posted any openings yet — use the form above to create one.</p>
  <?php else: ?>
    <div class="posting-manage-list">
      <?php foreach ($myPostings as $posting): ?>
        <?php
          $accepted = (int) ($posting['accepted_count'] ?? 0);
          $slots = (int) $posting['slots'];
          $isExhausted = ($accepted >= $slots);
        ?>
        <div class="posting-manage-row">
          <div>
            <h4><?php echo htmlspecialchars($posting['title']); ?></h4>
            <p class="profile-panel-hint" style="margin:0.2rem 0 0;">
              <strong><?php echo htmlspecialchars($posting['category_name']); ?></strong> ·
              Slots: <strong><?php echo $slots; ?></strong> ·
              Accepted: <strong><?php echo $accepted; ?></strong> ·
              Applicants: <strong><?php echo (int) $posting['applicant_count']; ?></strong> ·
              <?php if ($posting['status'] !== 'open'): ?>
                <span class="application-status application-status--declined">Closed</span>
              <?php elseif ($isExhausted): ?>
                <span class="application-status application-status--accepted" title="All slots filled. Automatically hidden from public committee page.">Slots Exhausted (Hidden)</span>
              <?php else: ?>
                <span class="application-status application-status--pending">Live on Committee Page</span>
              <?php endif; ?>
            </p>
          </div>
          <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
            <form method="post" style="display:inline;">
              <input type="hidden" name="posting_id" value="<?php echo (int) $posting['id']; ?>">
              <?php if ($posting['status'] === 'open'): ?>
                <input type="hidden" name="action" value="close_posting">
                <button class="cart-button posting-withdraw-button" type="submit" title="Close this posting">Close</button>
              <?php else: ?>
                <input type="hidden" name="action" value="reopen_posting">
                <button class="cart-button" type="submit" title="Reopen this posting">Reopen</button>
              <?php endif; ?>
            </form>

            <form method="post" onsubmit="return confirm('Are you sure you want to permanently remove this opening? This cannot be undone.');" style="display:inline;">
              <input type="hidden" name="posting_id" value="<?php echo (int) $posting['id']; ?>">
              <input type="hidden" name="action" value="delete_posting">
              <button class="cart-button posting-withdraw-button" type="submit" style="background:#fee2e2; color:#b91c1c; border:1px solid #f87171;" title="Remove this opening completely">Remove</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section class="dash-section">
  <h2>Applicants</h2>
  <?php if (!$applicants): ?>
    <p class="postings-empty">No one has applied to your postings yet.</p>
  <?php else: ?>
    <div class="application-list">
      <?php foreach ($applicants as $applicant): ?>
        <div class="application-row application-row--wide">
          <div>
            <h4><?php echo htmlspecialchars($applicant['aspirant_name']); ?> <span class="profile-panel-hint">applied to <?php echo htmlspecialchars($applicant['posting_title']); ?></span></h4>
            <p class="profile-panel-hint"><?php echo htmlspecialchars($applicant['program']); ?> <?php echo $applicant['year_level'] ? '· ' . htmlspecialchars($applicant['year_level']) : ''; ?><?php echo $applicant['aspirant_skills'] ? ' · Skills: ' . htmlspecialchars($applicant['aspirant_skills']) : ''; ?></p>
          </div>
          <div class="application-row-actions">
            <span class="application-status application-status--<?php echo htmlspecialchars($applicant['status']); ?>"><?php echo htmlspecialchars(ucfirst($applicant['status'])); ?></span>
            <?php if (in_array($applicant['status'], ['pending', 'reviewed'], true)): ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="decide_application">
                <input type="hidden" name="application_id" value="<?php echo (int) $applicant['id']; ?>">
                <input type="hidden" name="decision" value="accepted">
                <button class="cart-button" type="submit">Accept</button>
              </form>
              <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="decide_application">
                <input type="hidden" name="application_id" value="<?php echo (int) $applicant['id']; ?>">
                <input type="hidden" name="decision" value="declined">
                <button class="cart-button posting-withdraw-button" type="submit">Decline</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
