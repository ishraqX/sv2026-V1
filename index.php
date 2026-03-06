<?php
// Start session for visitor counter
session_start();

// Define base path for includes
define('BASE_PATH', __DIR__);

// Simple visitor counter with proper locking
$counter_file = 'visitor_count.txt';
$current_count = 0;

if (file_exists($counter_file)) {
    $current_count = (int)trim(file_get_contents($counter_file));
}

// Only increment if this is a new session visit
$session_key = 'sv_visited_' . date('Y-m-d');
if (!isset($_SESSION[$session_key])) {
    $current_count++;
    if (file_put_contents($counter_file, $current_count, LOCK_EX) === false) {
        error_log('Failed to update visitor count');
    }
    $_SESSION[$session_key] = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sound Vision - Transforming Lives Through Innovation</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Sound Vision is revolutionizing accessibility through innovative audio solutions. Transforming lives through cutting-edge technology and inclusive design.">
    <meta name="keywords" content="sound vision, accessibility, audio technology, innovation, inclusive design, assistive technology">
    <meta name="author" content="Sound Vision Team">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://soundvision.com.bd/">
    <meta property="og:title" content="Sound Vision - Transforming Lives Through Innovation">
    <meta property="og:description" content="Revolutionizing accessibility through innovative audio solutions and inclusive design.">
    <meta property="og:image" content="https://soundvision.com.bd/assets/images/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://soundvision.com.bd/">
    <meta property="twitter:title" content="Sound Vision - Transforming Lives Through Innovation">
    <meta property="twitter:description" content="Revolutionizing accessibility through innovative audio solutions and inclusive design.">
    <meta property="twitter:image" content="https://soundvision.com.bd/assets/images/twitter-image.jpg">

    <!-- Performance & Security -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    
    <link rel="stylesheet" href="assets/css/overflow-fix.css">
    
    

    <style>
        /* Force visibility for Award Images in Carousel */
        .sv-award-img {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        /* Initial state for the section to prevent layout shift */
        .sv-awards-section { 
            opacity: 1 !important; 
            min-height: 400px;
        }

        /* Fix for potential z-index issues with navigation */
        .sv-tm-section, .sv-awards-section {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>

    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <?php
    // Navigation
    if (file_exists('components/0.nav/navigation.php')) include 'components/0.nav/navigation.php';

    // Hero Banner
    if (file_exists('components/1.hero/hero.php')) include 'components/1.hero/hero.php';

    // SDG Section
    if (file_exists('components/2.sdg/sdg.php')) include 'components/2.sdg/sdg.php';

    // Solutions/Features
    if (file_exists('components/3.feature/features.php')) include 'components/3.feature/features.php';

    // Videos
    if (file_exists('components/4.media/video.php')) include 'components/4.media/video.php';

    // Teams
    if (file_exists('components/5.team/team.php')) include 'components/5.team/team.php';

    // App Download
    if (file_exists('components/6.download/app-download.php')) include 'components/6.download/app-download.php';

    // Who We Are (About)
    if (file_exists('components/7.about/about.php')) include 'components/7.about/about.php';

    // Awards & Achievements
    if (file_exists('components/8.award/award.php')) include 'components/8.award/award.php';

    // backedby
    if (file_exists('components/9.backedby/backedby.php')) include 'components/9.backedby/backedby.php';

    // Testimonials
    if (file_exists('components/10.testimonials/testimonials.php')) include 'components/10.testimonials/testimonials.php';


    // Visitor Counter
    if (file_exists('components/11.visitor/visitor_counter.php')) include 'components/11.visitor/visitor_counter.php';

    // Footer
    if (file_exists('components/12.footer/footer.php')) include 'components/12.footer/footer.php';
    ?>

    <script src="assets/js/combined.min.js" defer></script>

    <script>
        /**
         * Image Recovery & Fallback Script
         * Runs after DOM is ready to ensure no images are stuck in 'loading' or 'error' state
         */
        window.addEventListener('DOMContentLoaded', function() {
            // Check Award Images
            const awardImgs = document.querySelectorAll('.sv-award-img');
            awardImgs.forEach(img => {
                // If the image failed to load, try to recover from the data attribute
                img.onerror = function() {
                    const slide = this.closest('.sv-award-slide');
                    if (slide && slide.dataset.image && this.src !== slide.dataset.image) {
                        console.log('Recovering image:', slide.dataset.image);
                        this.src = slide.dataset.image;
                    }
                };
                
                // Force a reload if it's currently broken/blank
                if (img.naturalWidth === 0) {
                    const currentSrc = img.src;
                    img.src = '';
                    img.src = currentSrc;
                }
            });
        });

        /**
         * Simple Fallback for Awards if the main script fails
         */
        setTimeout(function() {
            if (typeof initCarousel === 'undefined' && typeof window.SoundVisionAwards === 'undefined') {
                console.warn('Awards script not initialized, applying basic interactions');
                document.querySelectorAll('.sv-award-slide').forEach(slide => {
                    slide.style.cursor = 'pointer';
                    slide.addEventListener('click', function() {
                        const title = this.querySelector('h3')?.innerText || this.dataset.title;
                        if (title) alert("Award Details: " + title);
                    });
                });
            }
        }, 4000);
    </script>

    <!-- Scripts -->
    <script src="assets/js/combined.js"></script>

</body>
</html>