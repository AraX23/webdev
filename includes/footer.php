<!-- ============ FOOTER ============ -->
<footer class="site-footer" id="about">
    <div class="footer-top">
        <div class="brand-logo-plate">
            <img src="assets/logo.png" alt="ComMEETtee — where skills and responsibility meet" class="brand-logo brand-logo--footer">
        </div>

        <div class="footer-contacts">
            <div class="footer-contact-block">
                <h4>ComMEETtee</h4>
                <p><svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M4 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L14 13l5 2v4a2 2 0 0 1-2 2C9.5 21 3 14.5 3 6a2 2 0 0 1 1-2z" fill="currentColor"/></svg> +63 915 532 4760</p>
                <p><svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M13 21v-7h3l1-4h-4V7.5C13 6.3 13.3 5.5 15 5.5h2V2.1C16.7 2 15.5 2 14.2 2 11.5 2 9.5 3.6 9.5 6.5V10H6.5v4h3v7h3.5z" fill="currentColor"/></svg> ComMEETtee</p>
                <p><svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18z" fill="none" stroke="currentColor" stroke-width="1.6"/></svg> www.commeettee.com</p>
            </div>

            <div class="footer-contact-block">
                <h4>CEO</h4>
                <p><svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M4 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L14 13l5 2v4a2 2 0 0 1-2 2C9.5 21 3 14.5 3 6a2 2 0 0 1 1-2z" fill="currentColor"/></svg> +63 977 654 4558</p>
                <p><svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M13 21v-7h3l1-4h-4V7.5C13 6.3 13.3 5.5 15 5.5h2V2.1C16.7 2 15.5 2 14.2 2 11.5 2 9.5 3.6 9.5 6.5V10H6.5v4h3v7h3.5z" fill="currentColor"/></svg> Ciara Amber Saycon</p>
                <p><svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 6l9 7 9-7" fill="none" stroke="currentColor" stroke-width="1.6"/></svg> ciaraamberx23@gmail.com</p>
            </div>
        </div>

        <div class="footer-cols">
            <div>
                <h4>Platform</h4>
                <a href="index.php#aspirants">Aspirants</a>
                <a href="index.php#clients">Clients</a>
                <a href="index.php#committees">Committees</a>
            </div>
            <div>
                <h4>Company</h4>
                <a href="contact.php">Contact Us</a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="account.php">Dashboard</a>
                    <a href="logout.php">Log Out</a>
                <?php else: ?>
                    <a href="login.php">Log In</a>
                    <a href="register.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <p class="footer-copy">&copy; 2026 ComMEETtee. All rights reserved.</p>
</footer>
