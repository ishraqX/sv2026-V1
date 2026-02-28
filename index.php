<?php
// Start session for visitor counter
session_start();

// Simple visitor counter
$counter_file = 'visitor_count.txt';
if (!file_exists($counter_file)) {
    file_put_contents($counter_file, '0');
}
$current_count = (int)file_get_contents($counter_file);
$current_count++;
file_put_contents($counter_file, $current_count);

// Define base path for includes
define('BASE_PATH', __DIR__);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sound Vision - Transforming Lives Through Innovation</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="components/0.nav/navigation.css">
    <link rel="stylesheet" href="components/1.hero/hero.css">
    <link rel="stylesheet" href="components/2.sdg/sdg.css">
    <link rel="stylesheet" href="components/3.feature/features.css">
    <link rel="stylesheet" href="components/4.media/video.css">
    <link rel="stylesheet" href="components/5.team/team.css">
    <link rel="stylesheet" href="components/6.download/app-download.css">
    <link rel="stylesheet" href="components/7.about/about.css">
    <link rel="stylesheet" href="components/8.award/award.css">
    <link rel="stylesheet" href="components/9.backedby/backedby.css">
    <link rel="stylesheet" href="components/10.testimonials/testimonials.css">
    <link rel="stylesheet" href="components/11.visitor/visitor_counter.css">
    <link rel="stylesheet" href="components/12.footer/footer.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
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

    <script src="assets/js/main.js" defer></script>
    
    
    <script src="components/5.team/team.js" defer></script>
    <script src="components/8.award/award.js" defer></script>
    <script src="components/8.award/award-music.js"></script>
    <script src="components/9.backedby/backedby.js"></script>

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

</body>
</html>