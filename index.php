<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ComMEETtee</title> 
<link rel="preconnect" href="https://fonts.google.com/specimen/Climate+Crisis">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Climate+Crisis&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ============ NAV ============ -->
<header class="site-header">
    <div class="nav-wrap">
        <a href="#top" class="brand" aria-label="HOME">
            <img src="assets/logo.png" alt="ComMEETtee" class="brand-logo">
        </a>

        <nav class="primary-nav" aria-label="Primary">
            <ul>
                <li><a href="#top" class="nav-pill">Home</a></li>
                <li><a href="#aspirants" class="nav-pill" aria-expanded="false">Aspirants</a></li>
                <li><a href="#clients" class="nav-pill">Clients</a></li>
                <li class="has-dropdown">
                <button class="nav-pill" aria-expanded="false">Committees <svg class="chev" viewBox="0 0 12 8" width="10" height="7"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none"/></svg></button>
                    <ul class="dropdown">
                        <li><a href="#">Technicals</a></li>
                        <li><a href="#">Decorations</a></li>
                        <li><a href="#">Logistics</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Marketing</a></li>
                        <li><a href="#">Finance</a></li>
                    </ul>
                </li>
                <li><a href="#about" class="nav-pill">About Us</a></li>
            </ul>
        </nav>

        <div class="nav-actions">
            <a href="#" class="link-ghost">LOG IN</a>
            <span class="divider" aria-hidden="true"></span>
            <a href="#" class="link-ghost">SIGN UP</a>
            <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <!-- mobile menu -->
    <nav class="mobile-nav" id="mobileNav" aria-label="Mobile">
        <ul>
            <li><a href="#top">Home</a></li>
            <li><a href="#aspirants">Aspirants</a></li>
            <li><a href="#clients">Clients</a></li>
            <li><a href="#committees">Committees</a></li>
            <li><a href="#about">About Us</a></li>
            <li class="mobile-actions"><a href="#" class="link-ghost">Log In</a><a href="#" class="btn btn--orange">Sign Up</a></li>
        </ul>
    </nav>
</header>

<!-- ============ HERO ============ -->
<section class="hero" id="top">
    <div class="hero-grid">
         <box class="hero-pitch">
                <img src="assets/toptext.svg" alt="description" width="24" height="24">
                <div> <p class="aspirants">ASPIRANTS,</p>
                <h1 class = "line">Find where your skills<br>can make an impact.</h1>
                </div>
        </box>
    </div>
    <div class = "hero-box-for-search">
        <form class="hero-search" role="search" action="#" method="get">
            <input type="search" name="q" placeholder="Organizations, People, Skills…" aria-label="Search tab">
            <button type="submit" aria-label="Search"> <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><circle cx="10.5" cy="10.5" r="7" fill="none" stroke="currentColor" stroke-width="2"/><path d="M20 20l-4.5-4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        </form>
    </div>
        
</section>

<!-- ============ CATEGORIES ============ -->
<section class="categories" id="aspirants">
    <div class="categories-grid">
                <div class="category-card" style="--i:0">
                <img src="assets/category-individual.jpg" alt="Individual Aspirants">
                 <span class="category-tag">INDIVIDUAL<br><small>ASPIRANTS</small></span>
        </div>
        <div class="category-card" style="--i:1">
            <img src="assets/category-independent.jpg" alt="Independent Clients">
            <span class="category-tag">INDEPENDENT<br><small>CLIENTS</small></span>
        </div>
        <div class="category-card" style="--i:2">
            <img src="assets/category-organization.jpg" alt="Organization Clients">
            <span class="category-tag">ORGANIZATION<br><small>CLIENTS</small></span>
        </div>
    </div>

    <div class="categories-copy">
        <h2>Find your way to the right connection</h2>
        <p>Where do you fit? <strong>Click and explore</strong> these paths to see which matches your goals and skills. Whether you're looking to contribute or build a team, start here.</p>
    </div>

    <div class="categories-links">
        <a href="#committees" class="pill-link">Committees</a>
        <a href="#" class="pill-link">Organizations</a>
    </div>
</section>

<!-- ============ COMMITTEES CAROUSEL ============ -->
<section class="carousel-section" id="committees">
    <p class="carousel-kicker">Check out</p>
    <p class="carousel-subtext">Committee description &amp; registered organizations.</p>

    <div class="carousel" data-committees='[{"key":"documentation","title":"Documentation","image":"assets/committee-documentation.jpg","desc":"Captures every milestone in photos, videos, and files, so nothing about the event goes unrecorded."},{"key":"technicals","title":"Technicals","image":"assets/committee-technicals.jpg","desc":"Handles the technical equipment, setup, and operations needed to ensure smooth event execution."},{"key":"decorations","title":"Decorations","image":"assets/committee-decorations.jpg","desc":"Shapes the look and feel of the venue, from overall layout down to the smallest visual details."}]'>
        <button class="carousel-arrow carousel-arrow--prev" aria-label="Previous committee" type="button">
            <svg viewBox="0 0 12 20" width="14" height="22"><path d="M10 2L2 10l8 8" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        <div class="carousel-track">
            <button class="carousel-slide carousel-slide--side" data-role="prev" type="button">
                <img src="" alt="">
                <span class="carousel-slide-label"></span>
            </button>
            <div class="carousel-slide carousel-slide--center" data-role="center">
                <img src="" alt="">
                <span class="carousel-slide-label"></span>
            </div>
            <button class="carousel-slide carousel-slide--side" data-role="next" type="button">
                <img src="" alt="">
                <span class="carousel-slide-label"></span>
            </button>
        </div>

        <button class="carousel-arrow carousel-arrow--next" aria-label="Next committee" type="button">
            <svg viewBox="0 0 12 20" width="14" height="22"><path d="M2 2l8 8-8 8" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>

    <div class="carousel-description">
        <h3 id="carouselTitle">Technicals</h3>
        <p id="carouselDesc">Handles the technical equipment, setup, and operations needed to ensure smooth event execution.</p>
        <a href="#" class="carousel-more">See more.</a>
    </div>
</section>

<!-- ============ SET UP YOUR PROFILE ============ -->
<section class="profile-cta">
    <div class="profile-copy">
        <h2>Set up your<br>profile now!</h2>
        <p><strong>Choose your role</strong> and complete <strong>your profile</strong> with the information that best represents you, including your skills, interests, experience, and availability. This helps <strong>ComMEETtee</strong> connect you with the right opportunities.</p>
        <a href="#" class="btn btn--outline">Click Here!</a>
    </div>

    <div class="profile-preview" aria-hidden="true">
        <div class="profile-card">
            <div class="profile-avatar">
                <svg viewBox="0 0 24 24" width="34" height="34"><circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 20c1.5-4 4.5-6 8-6s6.5 2 8 6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </div>
            <p class="profile-name">Ciara Amber T. Saycon</p>
            <p class="profile-role">Graphic Designer <span>Aspirant</span></p>
            <p class="profile-program">BS Information Technology · Year 3</p>
        </div>

        <div class="profile-panels">
            <div class="profile-panel">
                <h4>Affiliations</h4>
                <ol>
                    <li>Information Technology Society</li>
                    <li>League of Student Organizations</li>
                </ol>
            </div>
            <div class="profile-panel profile-panel--row">
                <div>
                    <h4>Portfolio Uploads</h4>
                    <p class="profile-panel-hint">Click to view documents.</p>
                </div>
                <span class="profile-badge">12+</span>
            </div>
            <div class="profile-panel">
                <h4>About Me</h4>
                <ul>
                    <li>Digital &amp; traditional visual artist</li>
                    <li>An aspiring graphic designer</li>
                </ul>
            </div>
            <div class="profile-panel">
                <h4>Ratings</h4>
                <div class="profile-stars" style="--fill:80%"></div>
                <p class="profile-panel-hint">Rated by 20p</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ CLIENTS STRIP ============ -->
<section class="cta-strip" id="clients">
    <div class="cta-strip-inner">
        <div>
            <p class="eyebrow eyebrow--dark">Every roster starts somewhere</p>
            <h2>Post a role. Fill the seat.</h2>
        </div>
        <a href="#" class="btn btn--dark">Post an Opening</a>
    </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="testimonials">
    <img src="assets/testimonial-bg.jpg" alt="" class="testimonials-bg" aria-hidden="true">
    <div class="testimonials-overlay" aria-hidden="true"></div>
    <h2>Testimonies</h2>
    <div class="testimonials-grid">
        <blockquote class="testimonial-card" style="--i:0">
            <p class="testimonial-quote">&ldquo;ComMEETtee made it easier for me to discover opportunities that matched my skills and interests. Instead of asking around for available committees, I could find them all in one place.&rdquo;</p>
            <cite>&mdash; <strong>Imnot O. Kay</strong>, Aspirant</cite>
        </blockquote>
        <blockquote class="testimonial-card" style="--i:1">
            <p class="testimonial-quote">&ldquo;I was looking for someone with graphic design and video editing skills for a project. ComMEETtee helped me find individuals whose skills matched what I needed.&rdquo;</p>
            <cite>&mdash; <strong>Ineed S. Leep</strong>, Independent Client</cite>
        </blockquote>
    </div>
    <p class="testimonials-tagline">Connect &bull; Co <span>Meet</span> &bull; Commit</p>
</section>

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
                <a href="#aspirants">Aspirants</a>
                <a href="#clients">Clients</a>
                <a href="#committees">Committees</a>
            </div>
            <div>
                <h4>Company</h4>
                <a href="#about">About Us</a>
                <a href="#">Log In</a>
                <a href="#">Sign Up</a>
            </div>
        </div>
    </div>
    <p class="footer-copy">&copy; 2026 ComMEETtee. All rights reserved.</p>
</footer>

<script src="script.js"></script>
</body>
</html>
