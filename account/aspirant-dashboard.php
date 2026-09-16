<section class="dash-section profile-edit-section">
  <div class="profile-avatar-block">
    <div class="profile-avatar-placeholder"><?php echo htmlspecialchars(strtoupper(substr($profile['name'], 0, 1))); ?></div>
    <div>
      <p class="profile-name" style="margin:0 0 4px;"><?php echo htmlspecialchars($profile['name']); ?></p>
      <p class="profile-panel-hint"><?php echo htmlspecialchars($profile['email']); ?></p>
    </div>
  </div>

  <h2>My Profile</h2>
  <form method="post" class="account-form">
    <input type="hidden" name="action" value="update_profile">
    <label>Program
      <select name="program">
        <option value="">Select your program…</option>
        <?php if ($profile['program'] !== '' && !in_array($profile['program'], norsuProgramOptions(), true)): ?>
          <option value="<?php echo htmlspecialchars($profile['program']); ?>" selected><?php echo htmlspecialchars($profile['program']); ?></option>
        <?php endif; ?>
        <?php foreach (norsuProgramOptions() as $programOption): ?>
          <option value="<?php echo htmlspecialchars($programOption); ?>" <?php echo $profile['program'] === $programOption ? 'selected' : ''; ?>><?php echo htmlspecialchars($programOption); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Year Level
      <select name="year_level">
        <option value="">Select your year level…</option>
        <?php if ($profile['year_level'] !== '' && !in_array($profile['year_level'], yearLevelOptions(), true)): ?>
          <option value="<?php echo htmlspecialchars($profile['year_level']); ?>" selected><?php echo htmlspecialchars($profile['year_level']); ?></option>
        <?php endif; ?>
        <?php foreach (yearLevelOptions() as $yearOption): ?>
          <option value="<?php echo htmlspecialchars($yearOption); ?>" <?php echo $profile['year_level'] === $yearOption ? 'selected' : ''; ?>><?php echo htmlspecialchars($yearOption); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Skills &amp; Interests<input type="text" name="skills" value="<?php echo htmlspecialchars($profile['skills']); ?>" placeholder="e.g. Graphic Design, Video Editing"></label>
    <label>Availability<input type="text" name="availability" value="<?php echo htmlspecialchars($profile['availability']); ?>" placeholder="e.g. Weekends, 5–10 hrs/week"></label>
    <label>About Me<textarea name="bio" rows="3" placeholder="A short introduction clients will see on your applications."><?php echo htmlspecialchars($profile['bio']); ?></textarea></label>
    <button class="cart-button" type="submit">Save Profile</button>
  </form>
</section>

<section class="dash-section dash-committees-section">
  <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 1.25rem; flex-wrap:wrap; gap:0.5rem;">
    <div>
      <h2 style="margin:0 0 4px;">Explore Committees</h2>
      <p class="profile-panel-hint" style="margin:0;">Tap any committee below to view NORSU organizations currently looking for committee members, open slots, and required qualifications.</p>
    </div>
    <a href="committee.php" class="btn btn--outline" style="font-size:0.8rem; padding:0.4rem 0.9rem;">All Committees &rarr;</a>
  </div>

  <div class="dash-committee-grid">
    <?php if (!empty($dashCommittees)): ?>
      <?php foreach ($dashCommittees as $comm): ?>
        <a href="committee.php?type=<?php echo urlencode($comm['id']); ?>" class="dash-committee-card">
          <div>
            <div class="dash-committee-card-head">
              <span class="dash-committee-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
              </span>
              <span class="dash-committee-badge">
                <?php echo (int) $comm['openings_count']; ?> Opening<?php echo (int) $comm['openings_count'] === 1 ? '' : 's'; ?>
              </span>
            </div>
            <h3><?php echo htmlspecialchars($comm['name']); ?></h3>
            <p><?php echo htmlspecialchars($comm['description'] ?: 'Find NORSU organizations recruiting for this committee.'); ?></p>
          </div>
          <div class="dash-committee-footer">
            <span class="dash-committee-action-text">View Organizations</span>
            <span class="dash-committee-arrow">&rarr;</span>
          </div>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<section class="dash-section">
  <h2>My Applications</h2>
  <?php if (!$myApplications): ?>
    <p class="postings-empty">You haven't applied to any committees yet. <a href="committee.php">Browse committee openings</a>.</p>
  <?php else: ?>
    <div class="application-list">
      <?php foreach ($myApplications as $application): ?>
        <div class="application-row">
          <div>
            <h4><?php echo htmlspecialchars($application['posting_title']); ?></h4>
            <p class="profile-panel-hint"><?php echo htmlspecialchars($application['category_name']); ?> · <?php echo htmlspecialchars($application['client_org'] ?: $application['client_name']); ?></p>
          </div>
          <span class="application-status application-status--<?php echo htmlspecialchars($application['status']); ?>"><?php echo htmlspecialchars(ucfirst($application['status'])); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>