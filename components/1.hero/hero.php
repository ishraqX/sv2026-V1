<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sound Vision — See the World in Sound</title>

    <!--
    ╔══════════════════════════════════════════════════════════╗
    ║  FONT LOADING — Outfit from Google Fonts                 ║
    ║  • preconnect speeds up DNS resolution                   ║
    ║  • display=swap → system font shows IMMEDIATELY,        ║
    ║    then swaps to Outfit once loaded.                     ║
    ║  • Fallback stack in CSS: Outfit → Trebuchet MS →        ║
    ║    Helvetica → Arial → sans-serif                        ║
    ║  • This combination guarantees text ALWAYS renders,      ║
    ║    never flattens or goes invisible.                     ║
    ╚══════════════════════════════════════════════════════════╝
    -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="hero.css">
</head>
<body>

<!-- ══════════════════════════════════════════════════════════
     HERO SECTION
     Text LEFT · Image RIGHT (desktop)
     Image TOP · Text BELOW (mobile — image always visible)
══════════════════════════════════════════════════════════ -->
<section id="hero" aria-label="Sound Vision Hero">

    <div class="hs-track" id="hsTrack">

        <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             SLIDE 1
             ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             IMAGE GUIDE:
             • Replace src below with your photo
             • Best: PNG with transparent background
             • Also works: JPG portrait photo
             • Ideal: 600–900px wide, portrait ratio (3:4)
             • Resize: change --img-h in the :root CSS above
             ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <!-- SLIDE 1 — Core Vision -->
        <div class="hs-slide is-active" data-idx="0">
            <div class="hs-inner">

                <div class="hs-copy">
                    <div class="hs-pill blue">
                        <span class="hs-pill-dot"></span>
                        AI Smart Glasses · Sound Vision
                    </div>

                    <h1 class="hs-h1">
                        See the World<br>
                        Through <em class="blue">Sound</em><br>
                        Using AI
                    </h1>

                    <p class="hs-body">
                        
                        Designed to help visually impaired individuals 
                    </p>

                    <div class="hs-cta">
                        <a href="#download" class="hs-btn hs-btn-primary">
                            Explore the Technology <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="#demo" class="hs-btn hs-btn-ghost">
                            <i class="fas fa-play"></i> Watch Live Demo
                        </a>
                    </div>

                    <div class="hs-stats">
                        <div class="hs-stat">
                            <strong>Real-Time</strong>
                            <span>Object Detection</span>
                        </div>
                        <div class="hs-stat-sep"></div>
                        <div class="hs-stat">
                            <strong>Voice</strong>
                            <span>Audio Feedback</span>
                        </div>
                        <div class="hs-stat-sep"></div>
                        <div class="hs-stat">
                            <strong>Mobile + AI</strong>
                            <span>Smart Integration</span>
                        </div>
                    </div>
                </div>

                <div class="hs-visual">
                    <div class="hs-imgwrap">
                        <div class="hs-blob blue"></div>

                        <img
                            src="assets/hero/hero1.png"
                            alt="Person using Sound Vision AI Smart Glasses"
                            class="hs-img"
                            fetchpriority="high"
                            loading="eager"
                            draggable="false"
                            onerror="this.onerror=null; this.src='assets/hero/fallback.jpg';">

                        <div class="hs-chip top">
                            <span class="hs-chip-icon blue"><i class="fas fa-ear-listen"></i></span>
                            Live Audio Feedback
                        </div>
                        <div class="hs-chip top2">
                            <span class="hs-chip-icon blue"><i class="fas fa-eye"></i></span>
                            Object Detection
                        </div>
                        <div class="hs-chip bot">
                            <span class="hs-chip-icon blue"><i class="fas fa-brain"></i></span>
                            AI Vision Processing
                        </div>
                        <div class="hs-chip bot2">
                            <span class="hs-chip-icon blue"><i class="fas fa-route"></i></span>
                            Smart Navigation
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /slide 1 -->


        <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             SLIDE 2 — Healthcare
             ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <!-- SLIDE 2 — Healthcare Use Case -->
        <div class="hs-slide" data-idx="1">
            <div class="hs-inner">

                <div class="hs-copy">
                    <div class="hs-pill teal">
                        <span class="hs-pill-dot" style="background:#0891B2;box-shadow:0 0 0 3px rgba(8,145,178,.2)"></span>
                        Healthcare Accessibility
                    </div>

                    <h1 class="hs-h1">
                        Supporting<br>
                        <em class="teal">Accessible</em><br>
                        Healthcare
                    </h1>

                    <p class="hs-body">
                        Translating environments into clear audio guidance.
                    </p>

                    <div class="hs-cta">
                        <a href="#features" class="hs-btn hs-btn-primary teal">
                            Healthcare Applications <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="#impact" class="hs-btn hs-btn-ghost">
                            <i class="fas fa-chart-line"></i> Project Impact
                        </a>
                    </div>

                    <div class="hs-stats">
                        <div class="hs-stat">
                            <strong>Obstacle</strong>
                            <span>Detection</span>
                        </div>
                        <div class="hs-stat-sep"></div>
                        <div class="hs-stat">
                            <strong>Environment</strong>
                            <span>Recognition</span>
                        </div>
                        <div class="hs-stat-sep"></div>
                        <div class="hs-stat">
                            <strong>Safe</strong>
                            <span>Navigation</span>
                        </div>
                    </div>
                </div>

                <div class="hs-visual">
                    <div class="hs-imgwrap">
                        <div class="hs-blob teal"></div>

                        <img
                            src="assets/hero/hero2.png"
                            alt="Healthcare use case of Sound Vision Smart Glasses"
                            class="hs-img"
                            draggable="true"
                            onerror="this.onerror=null; this.src='assets/hero/hero1.png';">

                        <div class="hs-chip top">
                            <span class="hs-chip-icon teal"><i class="fas fa-stethoscope"></i></span>
                            Clinical Assistance
                        </div>
                        <div class="hs-chip top2">
                            <span class="hs-chip-icon teal"><i class="fas fa-heartbeat"></i></span>
                            Patient Safety
                        </div>
                        <div class="hs-chip bot">
                            <span class="hs-chip-icon teal"><i class="fas fa-shield-halved"></i></span>
                            Reliable AI Support
                        </div>
                        <div class="hs-chip bot2">
                            <span class="hs-chip-icon teal"><i class="fas fa-hospital"></i></span>
                            Medical Grade
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /slide 2 -->


        <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             SLIDE 3 — Urban
             ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <!-- SLIDE 3 — Smart Cities -->
        <div class="hs-slide" data-idx="2">
            <div class="hs-inner">

                <div class="hs-copy">
                    <div class="hs-pill emerald">
                        <span class="hs-pill-dot" style="background:#059669;box-shadow:0 0 0 3px rgba(5,150,105,.2)"></span>
                        Smart Cities & SDG Impact
                    </div>

                    <h1 class="hs-h1">
                        Building<br>
                        <em class="emerald">Inclusive</em><br>
                        Communities
                    </h1>

                    <p class="hs-body">
                        Independent mobility for visually impaired users.
                    </p>

                    <div class="hs-cta">
                        <a href="#download" class="hs-btn hs-btn-primary emerald">
                            Join the Initiative <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="#sdg" class="hs-btn hs-btn-ghost">
                            <i class="fas fa-globe"></i> SDG Alignment
                        </a>
                    </div>

                    <div class="hs-stats">
                        <div class="hs-stat">
                            <strong>AI</strong>
                            <span>Navigation</span>
                        </div>
                        <div class="hs-stat-sep"></div>
                        <div class="hs-stat">
                            <strong>Urban</strong>
                            <span>Mobility</span>
                        </div>
                        <div class="hs-stat-sep"></div>
                        <div class="hs-stat">
                            <strong>Inclusive</strong>
                            <span>Technology</span>
                        </div>
                    </div>
                </div>

                <div class="hs-visual">
                    <div class="hs-imgwrap">
                        <div class="hs-blob emerald"></div>
                        <!-- CHECK THESE POSSIBILITIES:
                             1. Make sure assets/hero/hero3.png exists
                             2. Try renaming to hero3.jpg if that's the format
                             3. Check for uppercase/lowercase: Hero3.png vs hero3.png
                        -->
                        <img
                            src="assets/hero/hero3.png"
                            alt="Urban professional wearing Sound Vision Smart Glasses"
                            class="hs-img"
                           
                            draggable="true"
                            onerror="this.onerror=null; console.log('hero3.png failed to load'); this.src='assets/hero/hero1.png';">

                        <div class="hs-chip top">
                            <span class="hs-chip-icon emerald"><i class="fas fa-leaf"></i></span>
                            Eco Certified
                        </div>
                        <div class="hs-chip top2">
                            <span class="hs-chip-icon emerald"><i class="fas fa-person-walking"></i></span>
                            Free Mobility
                        </div>
                        <div class="hs-chip bot">
                            <span class="hs-chip-icon emerald"><i class="fas fa-city"></i></span>
                            Smart City
                        </div>
                        <div class="hs-chip bot2">
                            <span class="hs-chip-icon emerald"><i class="fas fa-globe"></i></span>
                            SDG Aligned
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /slide 3 -->


    </div><!-- /hs-track -->


    <!-- ── Controls ────────────────────────────────────────── -->
    <div class="hs-controls">
        <div class="hs-progress">
            <div class="hs-progress-bar" id="hsProgressBar"></div>
        </div>
        <div class="hs-nav">
            <button class="hs-arrow" id="hsPrev" aria-label="Previous slide">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="hs-dots" role="tablist">
                <button class="hs-dot active" data-idx="0" role="tab" aria-label="Slide 1" aria-selected="true"></button>
                <button class="hs-dot"        data-idx="1" role="tab" aria-label="Slide 2" aria-selected="false"></button>
                <button class="hs-dot"        data-idx="2" role="tab" aria-label="Slide 3" aria-selected="false"></button>
            </div>
            <button class="hs-arrow" id="hsNext" aria-label="Next slide">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="hs-scroll" aria-hidden="true">
        <span class="hs-scroll-label">Scroll</span>
        <span class="hs-scroll-line"></span>
    </div>

</section>
<script src="components/1.hero/hero.js" defer></script>
</body>
</html>