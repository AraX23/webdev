<?php
session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/Validation.php';
?>
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
<link rel="stylesheet" href="style.css?v=<?php echo file_exists(__DIR__ . '/style.css') ? filemtime(__DIR__ . '/style.css') : time(); ?>">
</head>
<body>

<!-- ============ NAV BAR ============ -->
<header class="site-header">
    <div class="nav-wrap">
        <a href="#top" class="brand" aria-label="HOME">
            <img src="assets/logo.png" alt="ComMEETtee" class="brand-logo">
        </a>

<!-- ============ NAV BAR ============ -->
<!-- ============ main 5 ============ -->
        <nav class="primary-nav" aria-label="Primary">
            <ul>
                <li><a href="#top" class="nav-pill">Home</a></li>
                <li><a href="#aspirants" class="nav-pill" aria-expanded="false">Aspirants</a></li>
                <li class="has-dropdown">
                    <button class="nav-pill nav-pill-size" aria-expanded="false">Clients <svg class="chev" viewBox="0 0 12 8" width="10" height="7"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none"/></svg></button>
                        <ul class="dropdown">
                            <li><a href="#clients">All Clients</a></li>
                            <li><a href="#independent-client">Independent Clients</a></li>
                            <li><a href="#organization-client">Organization Client</a></li>
                        </ul>
                </li>
                <li class="has-dropdown">
                    <button class="nav-pill nav-pill-size" aria-expanded="false">Committees <svg class="chev" viewBox="0 0 12 8" width="10" height="7"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none"/></svg></button>
                        <ul class="dropdown">
                            <li><a href="committee.php?type=technicals">Technicals</a></li>
                            <li><a href="committee.php?type=documentation">Documentation</a></li>
                            <li><a href="committee.php?type=decorations">Decorations</a></li>
                            <li><a href="committee.php?type=logistics">Logistics</a></li>
                        </ul>
                </li>
                <li><a href="#about" class="nav-pill">About Us</a></li>
                <li><a href="contact.php" class="nav-pill">Contact</a></li>
            </ul>
        </nav>
        <?php include __DIR__ . '/includes/nav-actions.php'; ?>
    </div>

    <!-- mobile menu -->
    <nav class="mobile-nav" id="mobileNav" aria-label="Mobile">
        <ul>
            <li><a href="#top">Home</a></li>
            <li><a href="#aspirants">Aspirants</a></li>
            <li><a href="#clients">Clients</a></li>
            <li><a href="committee.php">Committees</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li class="mobile-actions">
              <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="<?php echo !empty($_SESSION['is_admin']) ? 'adminfiles/admin.php' : 'account.php'; ?>" class="btn btn--orange">Dashboard</a>
                <a href="logout.php" class="link-ghost">Log Out</a>
              <?php else: ?>
                <a href="login.php" class="link-ghost">Log In</a>
                <a href="register.php" class="btn btn--orange">Sign Up</a>
              <?php endif; ?>
            </li>
        </ul>
    </nav>
</header>

<!-- ============ HERO ============ -->
<section class="hero" id="top">
        <div class="hero-pitch">
                <img src="assets/toptext.svg" alt="description">
                        <div> 
                            <p class="aspirants">ASPIRANTS,</p>
                            <h1 class = "line">Find where your skills<br>can make an impact.</h1>
                        </div>
                    <div>
                <p class="spotlight-caption">Connect with university committees that match your responsibility, interests, and availability.</p>
            </div>
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
<section class="categories">
    <div class="categories-inner">
            <div class="categories-grid">
                <div class="category-card" style="--i:0"> 
                    <img src="assets/category-individual.jpg" alt="Individual Aspirants">
                        <button class="category-tag" type="button" onclick="location.href='#individual-aspirants'">
                            INDIVIDUAL<br><small>ASPIRANTS</small>
                        </button>
                    </div>
                
                <div class="category-card" style="--i:1" >
                    <img src="assets/category-independent.jpg" alt="Independent Clients">
                        <button class="category-tag" type="button" onclick="location.href='#independent-client'">
                            INDEPENDENT<br><small>CLIENT</small> 
                        </button>
                    </div>

                <div class="category-card" style="--i:2">
                    <img src="assets/category-organization.jpg" alt="Organization Clients">
                        <button class="category-tag" type="button" onclick="location.href='#organization-client'">
                            ORGANIZATION<br> <small>CLIENT</small> 
                    </button>
                </div>
            </div>

<!-- ============ engaging text ============ -->
    <div class="categories-copy">
        <div class="thin-square"></div>
            <h2>FIND YOUR WAY TO THE RIGHT CONNECTION </h2>
                <p id = "categories-description"> Where do you fit? 
                    <strong> Click and explore</strong> 
                    these paths to see which matches your goals and skills. Whether you're looking to contribute or build a team, start here.
                </p>
        </div>
    </div>
</section>

<!-- ============ COMMITTEES CAROUSEL ============ -->

<!-- ============ choices for carousel ============ -->
<section class="carousel-section" id="committees">
 <div class="categories-links">
        <a href="#committees" class="pill-link">Committees</a>
        <a href="#" class="pill-link">Organizations</a>
    </div>

    <p class="carousel-kicker">Check out</p>
    <p class="carousel-subtext">Committee description &amp; registered organizations.</p>

    <div class="carousel" data-committees='[{"key":"documentation","title":"Documentation","image":"assets/committee-documentation.jpg","desc":"Captures every milestone in photos, videos, and files, so nothing about the event goes unrecorded."},{"key":"technicals","title":"Technicals","image":"assets/committee-technicals.jpg","desc":"Handles the technical equipment, setup, and operations needed to ensure smooth event execution."},{"key":"decorations","title":"Decorations","image":"assets/committee-decorations.jpg","desc":"Shapes the look and feel of the venue, from overall layout down to the smallest visual details."},{"key":"logistics","title":"Logistics","image":"assets/committee-technicals.jpg","desc":"Coordinates transportation, equipment, supply distributions, and venue setups to keep campus events running."}]'>
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
        <h3 id="carouselTitle">Documentation</h3>
        <p id="carouselDesc">Captures every milestone in photos, videos, and files, so nothing about the event goes unrecorded.</p>
        <a href="committee.php?type=documentation" class="carousel-more" id="carouselMoreBtn">See more.</a>
    </div>
</section>

<!-- ============ SET UP YOUR PROFILE ============ -->
<section class="profile-cta">
    <div class="profile-copy">
        <h2>Set up your<br>profile now!</h2>
        <p><strong>Choose your role</strong> and complete <strong>your profile</strong> with the information that best represents you, including your skills, interests, experience, and availability. This helps <strong>ComMEETtee</strong> connect you with the right opportunities.</p>
        <a href="<?php echo !empty($_SESSION['user_id']) ? 'account.php' : 'register.php'; ?>" class="btn btn--outline">Click Here!</a>
    </div>

    <div class="profile-preview" aria-hidden="true">
        <div class="profile-card">
            <div class="profile-avatar">
                <svg viewBox="0 0 24 24" width="34" height="34"><circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 20c1.5-4 4.5-6 8-6s6.5 2 8 6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </div>
            <p class="profile-name">Ciara Amber T. Saycon</p>
            <p class="profile-role">Graphic Designer <span>Aspirant</span></p>

            <div class="profile-info-group">
                <p class="profile-info-label">Program:</p>
                <p class="profile-info-val">BS Information Technology - 3</p>
            </div>

            <div class="profile-info-group">
                <p class="profile-info-label">Contact Information:</p>
                <p class="profile-info-val">+63 977 654 4558</p>
                <p class="profile-info-val">ciaraamberx23@gmail.com</p>
                <p class="profile-info-val">Ciara Amber T. Saycon</p>
            </div>
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
        <a href="<?php echo !empty($_SESSION['user_id']) ? 'account.php' : 'login.php?next=account.php'; ?>" class="btn btn--dark">Post an Opening</a>
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

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="script.js"></script>
</body>
</html>
