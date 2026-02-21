<?php
// ============================================================
// award.php — Sound Vision Awards & Achievements Section
// ============================================================

// ── Image Base Path ──────────────────────────────────────────
// Set this to the folder where your award images live,
// relative to the ROOT of your website (not this file).
// Examples:
//   'assets/images/awards/'   → yoursite.com/assets/images/awards/award-1.jpg
//   'uploads/awards/'         → yoursite.com/uploads/awards/award-1.jpg
//   ''                        → images sit right next to your index file
$sv_award_img_base = 'components/8.award/lowaward/';

// ── Award Data ───────────────────────────────────────────────
// Only put the FILENAME in 'src' — the base path above is prepended automatically.
// To add more: copy any block and paste below the last one.
$sv_awards = [
    [
        'src'          => 'award-1.jpg',
        'title'        => 'Best Digital Agency 2024',
        'organization' => 'National Digital Excellence Awards',
        'date'         => 'December 2024',
        'description'  => 'Recognized as the top digital agency for outstanding creativity, innovation, and delivering measurable client results across digital platforms.',
        'category'     => 'Excellence',
    ],
    [
        'src'          => 'award-2.jpg',
        'title'        => 'Innovation in Branding',
        'organization' => 'Global Brand Summit',
        'date'         => 'October 2024',
        'description'  => 'Awarded for breakthrough branding campaigns that transformed client identities and drove significant market growth in competitive industries.',
        'category'     => 'Branding',
    ],
    [
        'src'          => 'award-3.jpg',
        'title'        => 'Top Creative Studio',
        'organization' => 'Creative Industry Federation',
        'date'         => 'August 2024',
        'description'  => 'Honored as the leading creative studio for producing culturally resonant visual campaigns with measurable impact and artistic integrity.',
        'category'     => 'Creative',
    ],
    [
        'src'          => 'award-4.jpg',
        'title'        => 'Best Video Production',
        'organization' => 'South Asian Media Awards',
        'date'         => 'June 2024',
        'description'  => 'Gold award for cinematic excellence in corporate video production, recognized for storytelling quality and technical mastery.',
        'category'     => 'Production',
    ],
    [
        'src'          => 'award-5.jpg',
        'title'        => 'Digital Marketing Champion',
        'organization' => 'Bangladesh Digital Week',
        'date'         => 'April 2024',
        'description'  => 'Champion award for data-driven digital marketing strategies that achieved record-breaking ROI for multiple clients across diverse sectors.',
        'category'     => 'Marketing',
    ],
    [
        'src'          => 'award-6.jpg',
        'title'        => 'UX Design of the Year',
        'organization' => 'Design Awards Asia',
        'date'         => 'March 2024',
        'description'  => 'Awarded for creating user experiences that balance aesthetic beauty with intuitive functionality, setting new benchmarks in UX standards.',
        'category'     => 'Design',
    ],
    [
        'src'          => 'award-7.jpg',
        'title'        => 'Community Impact Award',
        'organization' => 'Dhaka Business Forum',
        'date'         => 'February 2024',
        'description'  => 'Recognized for meaningful contributions to the local business community through pro-bono design work and skills training initiatives.',
        'category'     => 'Community',
    ],
    [
        'src'          => 'award-8.jpg',
        'title'        => 'Best E-Commerce Solution',
        'organization' => 'E-Commerce Asia Summit',
        'date'         => 'January 2024',
        'description'  => 'Gold recognition for developing e-commerce platforms that delivered exceptional conversion rates and seamless customer journeys.',
        'category'     => 'Technology',
    ],
    [
        'src'          => 'award-9.jpg',
        'title'        => 'Social Media Campaign Award',
        'organization' => 'Digital Marketing Awards BD',
        'date'         => 'November 2023',
        'description'  => 'Best social media campaign for viral reach, engagement metrics, and brand storytelling across Instagram, Facebook and YouTube channels.',
        'category'     => 'Social Media',
    ],
    [
        'src'          => 'award-10.jpg',
        'title'        => 'Photography Excellence',
        'organization' => 'Asia Photography Awards',
        'date'         => 'September 2023',
        'description'  => 'Excellence award for commercial photography that elevated product aesthetics and helped clients achieve premium market positioning.',
        'category'     => 'Photography',
    ],
    [
        'src'          => 'award-11.jpg',
        'title'        => 'Startup of the Year',
        'organization' => 'Bangladesh Startup Summit',
        'date'         => 'July 2023',
        'description'  => 'Honored as the most promising and impactful creative startup, recognized for rapid growth, innovation, and social responsibility.',
        'category'     => 'Business',
    ],
    [
        'src'          => 'award-12.jpg',
        'title'        => 'Best Motion Graphics',
        'organization' => 'Animation Festival South Asia',
        'date'         => 'May 2023',
        'description'  => 'First place for motion graphics excellence, delivering visually stunning animations that communicate complex ideas with clarity and beauty.',
        'category'     => 'Animation',
    ],
    [
        'src'          => 'award-13.jpg',
        'title'        => 'Corporate Identity Award',
        'organization' => 'Brand Identity Council',
        'date'         => 'March 2023',
        'description'  => 'Prestigious recognition for crafting cohesive corporate identities that powerfully communicate organizational values and market position.',
        'category'     => 'Branding',
    ],
    [
        'src'          => 'award-14.jpg',
        'title'        => 'Web Development Award',
        'organization' => 'Tech Innovation Bangladesh',
        'date'         => 'January 2023',
        'description'  => 'Award for developing high-performance web applications that combine cutting-edge technology with exceptional user-centered design principles.',
        'category'     => 'Technology',
    ],
    [
        'src'          => 'award-15.jpg',
        'title'        => 'Client Satisfaction Trophy',
        'organization' => 'Service Excellence Forum',
        'date'         => 'December 2022',
        'description'  => 'Trophy awarded based on verified client testimonials and satisfaction surveys, reflecting our unwavering commitment to exceeding expectations.',
        'category'     => 'Service',
    ],
    [
        'src'          => 'award-16.jpg',
        'title'        => 'Outstanding Creative Team',
        'organization' => 'Creative Professionals Guild',
        'date'         => 'October 2022',
        'description'  => 'Collective recognition for our team\'s exceptional collaboration, creativity, and consistent delivery of world-class creative solutions.',
        'category'     => 'Excellence',
    ],
];

$sv_award_count = count($sv_awards);

// ── Helper: build full image src ─────────────────────────────
function sv_award_src(string $base, string $file): string {
    // If $file is already an absolute URL or starts with /, use as-is
    if (str_starts_with($file, 'http') || str_starts_with($file, '/')) {
        return htmlspecialchars($file);
    }
    return htmlspecialchars(rtrim($base, '/') . '/' . ltrim($file, '/'));
}
?>
<link rel="stylesheet" href="award.css">

<section class="sv-awards-section" id="awards">

    <!-- Background atmosphere -->
    <div class="sv-awards-bg">
        <div class="sv-awards-bg-glow sv-awards-bg-glow--left"></div>
        <div class="sv-awards-bg-glow sv-awards-bg-glow--right"></div>
        <div class="sv-awards-bg-grid"></div>
        <div class="sv-awards-bg-particles" id="svAwardParticles"></div>
    </div>

    <div class="sv-awards-container">

        <!-- Section Header -->
        <header class="sv-awards-header" data-animate>
            <div class="sv-awards-label">
                <span class="sv-awards-label-line"></span>
                <span>Our Achievements</span>
                <span class="sv-awards-label-line"></span>
            </div>
            <h2 class="sv-awards-title">
                Awards &amp; <span class="sv-awards-gold-text">Recognition</span>
            </h2>
            <p class="sv-awards-subtitle">
                Milestones of excellence earned through dedication, creativity, and a relentless pursuit of quality
            </p>
            <div class="sv-awards-count-chip">
                
                <span><?= $sv_award_count ?>+ Awards &amp; Counting</span>
            </div>
        </header>

        <!-- Carousel Wrapper -->
        <div class="sv-awards-stage" id="svAwardsStage">

            <!-- Glow light effect behind active card -->
            <div class="sv-stage-spotlight" id="svStageSpotlight"></div>

            <!-- Cards Track -->
            <div class="sv-awards-track-outer">
                <div class="sv-awards-track" id="svAwardsTrack">
                    <?php foreach ($sv_awards as $idx => $award): ?>
                    <div class="sv-award-card"
                         data-index="<?= $idx ?>"
                         data-src="<?= sv_award_src($sv_award_img_base, $award['src']) ?>"
                         data-title="<?= htmlspecialchars($award['title']) ?>"
                         data-org="<?= htmlspecialchars($award['organization']) ?>"
                         data-date="<?= htmlspecialchars($award['date']) ?>"
                         data-desc="<?= htmlspecialchars($award['description']) ?>"
                         data-cat="<?= htmlspecialchars($award['category']) ?>"
                         role="button"
                         tabindex="0"
                         aria-label="View award: <?= htmlspecialchars($award['title']) ?>">
                        <div class="sv-award-card-inner">
                            <!-- Image -->
                            <div class="sv-award-img-wrap">
                                <img 
                                    src="<?= sv_award_src($sv_award_img_base, $award['src']) ?>" 
                                    alt="<?= htmlspecialchars($award['title']) ?>" 
                                    class="sv-award-img"
                                    onerror="this.style.display='none';this.parentElement.classList.add('sv-img-error')"
                                >
                                <div class="sv-award-img-placeholder">
                                    <span>🏆</span>
                                </div>
                                <!-- Shine overlay -->
                                <div class="sv-award-shine"></div>
                                <!-- Category badge -->
                                <div class="sv-award-cat-badge"><?= htmlspecialchars($award['category']) ?></div>
                            </div>
                            <!-- Info -->
                            <div class="sv-award-info">
                                <h3 class="sv-award-title"><?= htmlspecialchars($award['title']) ?></h3>
                                <p class="sv-award-org"><?= htmlspecialchars($award['organization']) ?></p>
                                <p class="sv-award-date">
                                    <span class="sv-award-date-icon">📅</span>
                                    <?= htmlspecialchars($award['date']) ?>
                                </p>
                                <div class="sv-award-tap-hint">
                                    <span>Tap for details</span>
                                    <i class="sv-tap-arrow">→</i>
                                </div>
                            </div>
                        </div>
                        <!-- Gold border glow (active state) -->
                        <div class="sv-card-glow-border"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Finger Swipe Gesture Hint -->
            <div class="sv-gesture-hint" id="svGestureHint" aria-hidden="true">
                <div class="sv-gesture-hand">
                    <!-- SVG Hand -->
                    <svg class="sv-hand-svg" viewBox="0 0 80 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Palm -->
                        <ellipse cx="40" cy="72" rx="22" ry="18" fill="rgba(255,220,100,0.92)"/>
                        <!-- Thumb -->
                        <path d="M18 68 Q10 58 14 50 Q18 44 24 50 L26 68 Z" fill="rgba(255,220,100,0.92)"/>
                        <!-- Index -->
                        <rect x="32" y="30" width="10" height="36" rx="5" fill="rgba(255,220,100,0.92)"/>
                        <!-- Middle -->
                        <rect x="44" y="24" width="10" height="42" rx="5" fill="rgba(255,220,100,0.92)"/>
                        <!-- Ring -->
                        <rect x="56" y="30" width="9" height="36" rx="4.5" fill="rgba(255,220,100,0.92)"/>
                        <!-- Pinky -->
                        <rect x="66" y="38" width="8" height="28" rx="4" fill="rgba(255,220,100,0.92)"/>
                        <!-- Shadow under hand -->
                        <ellipse cx="40" cy="92" rx="18" ry="4" fill="rgba(0,0,0,0.25)"/>
                    </svg>
                    <!-- Swipe trail lines -->
                    <div class="sv-swipe-trails">
                        <span class="sv-trail sv-trail-1"></span>
                        <span class="sv-trail sv-trail-2"></span>
                        <span class="sv-trail sv-trail-3"></span>
                    </div>
                </div>
                <p class="sv-gesture-label">Swipe to explore</p>
            </div>

            <!-- Nav Arrows -->
            <button class="sv-awards-arrow sv-awards-arrow--prev" id="svAwardPrev" aria-label="Previous award">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button class="sv-awards-arrow sv-awards-arrow--next" id="svAwardNext" aria-label="Next award">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>

        <!-- Dots / Progress -->
        <div class="sv-awards-dots" id="svAwardsDots" role="tablist">
            <?php foreach ($sv_awards as $idx => $award): ?>
            <button class="sv-awards-dot<?= $idx === 0 ? ' sv-awards-dot--active' : '' ?>"
                    data-index="<?= $idx ?>"
                    role="tab"
                    aria-label="Award <?= $idx + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <!-- Counter -->
        <div class="sv-awards-counter">
            <span class="sv-counter-current" id="svCounterCurrent">01</span>
            <span class="sv-counter-sep">—</span>
            <span class="sv-counter-total"><?= str_pad($sv_award_count, 2, '0', STR_PAD_LEFT) ?></span>
        </div>

    </div>
</section>

<!-- ── Lightbox Modal ── -->
<!-- ══════════════════════════════════════════════════════════
     LIGHTBOX  —  class namespace: sv-aw-lb-  (= award.css)
     IDs exactly match what award.js getElementById() calls
════════════════════════════════════════════════════════════ -->
<div class="sv-aw-lightbox sv-aw-section" id="svLightbox"
     role="dialog" aria-modal="true" aria-label="Award detail" hidden>

    <!-- Backdrop -->
    <div class="sv-aw-lb-backdrop" id="svLightboxBackdrop"></div>

    <!-- Panel -->
    <div class="sv-aw-lb-panel" id="svLightboxPanel">

        <!-- Close -->
        <button class="sv-aw-lb-close" id="svLightboxClose" aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6"  x2="6"  y2="18"></line>
                <line x1="6"  y1="6"  x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="sv-aw-lb-inner">

            <!-- Image side -->
            <div class="sv-aw-lb-img-side">
                <img id="svLightboxImg" src="" alt="" class="sv-aw-lb-img"
                     onerror="this.style.display='none'">
                <div class="sv-aw-lb-img-fade"></div>
            </div>

            <!-- Info side -->
            <div class="sv-aw-lb-info">

                <div  class="sv-aw-lb-cat"   id="svLightboxCat"></div>
                <h2   class="sv-aw-lb-title" id="svLightboxTitle"></h2>
                <p    class="sv-aw-lb-org"   id="svLightboxOrg">
                    <span id="svLightboxOrgText"></span>
                </p>
                <p    class="sv-aw-lb-date"  id="svLightboxDate">
                    <span id="svLightboxDateText"></span>
                </p>
                <p    class="sv-aw-lb-desc"  id="svLightboxDesc"></p>

                <div class="sv-aw-lb-nav">
                    <button class="sv-aw-lb-btn"                    id="svLightboxPrev">← Previous</button>
                    <button class="sv-aw-lb-btn sv-aw-lb-btn--next" id="svLightboxNext">Next →</button>
                </div>

            </div><!-- /sv-aw-lb-info -->
        </div><!-- /sv-aw-lb-inner -->
    </div><!-- /sv-aw-lb-panel -->
</div><!-- /sv-aw-lightbox -->

<script src="award.js"></script>