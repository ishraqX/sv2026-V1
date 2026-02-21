<?php
// ============================================================
// backedby.php — Sound Vision · Backed By / Partners Section
// RAF infinite scroll (right → left) · dark luxury aesthetic
// ============================================================

// ── Image Base Path ──────────────────────────────────────────
// Set to the folder where your logo files live.
// Only put the FILENAME in 'src' below.
$sv_bb_img_base = 'components/9.backedby/logo/';

// ── Partner / Backer Data ────────────────────────────────────
// Add as many as you like — the JS clones the set automatically.
// 'name' → accessible alt text & hover tooltip
// 'src'  → filename only (base path is prepended)
// 'url'  → optional: wraps logo in an <a> link ('' to skip)
$sv_backed_by = [
    ['name' => 'GRACE',           'src' => 'grace.png',           'url' => ''],
    ['name' => 'AI Lab',        'src' => 'ailab.png',        'url' => ''],
    ['name' => 'UIHP',           'src' => 'uihp.png',           'url' => ''],
    ['name' => 'AB',             'src' => 'gp.png',             'url' => ''],
    ['name' => 'GPA',            'src' => 'gpl.png',            'url' => ''],
    ['name' => 'DEIED',          'src' => 'deied.png',          'url' => ''],
    ['name' => 'KON',            'src' => 'kon.png',            'url' => ''],
    ['name' => 'ICTD',          'src' => 'ictd.png',          'url' => ''],
    ['name' => 'OCB',           'src' => 'ocb.png',          'url' => ''],
    ['name' => 'Idea',          'src' => 'idea.png',           'url' => ''],
    ['name' => 'DDIEXpo2026',   'src' => 'ddiexpo26.png',            'url' => ''],
   
];

// ── Helper ───────────────────────────────────────────────────
function sv_bb_src(string $base, string $file): string {
    if (str_starts_with($file, 'http') || str_starts_with($file, '/'))
        return htmlspecialchars($file);
    return htmlspecialchars(rtrim($base, '/') . '/' . ltrim($file, '/'));
}

$sv_bb_count = count($sv_backed_by);
?>
<link rel="stylesheet" href="components/backedby/backedby.css">

<section class="sv-bb-section" id="svBbSection" aria-label="Our Partners and Backers">

    <!-- Atmospheric background -->
    <div class="sv-bb-bg" aria-hidden="true">
        <div class="sv-bb-glow sv-bb-glow--l"></div>
        <div class="sv-bb-glow sv-bb-glow--r"></div>
        <div class="sv-bb-noise"></div>
    </div>

    <div class="sv-bb-container">

        <!-- Header -->
        <div class="sv-bb-header" data-bb-reveal>
            <div class="sv-bb-label">
                <span class="sv-bb-label-line"></span>
                <!--<span class="sv-bb-label-text">Backed by</span>-->
                <span class="sv-bb-label-line"></span>
            </div>
            <h2 class="sv-bb-title">
                <em class="sv-bb-title-em">Backed</em> by
            </h2>
            
        </div>

        <!-- Infinite logo strip -->
        <div class="sv-bb-strip-wrap" data-bb-reveal>

            <!-- Top rule -->
            <div class="sv-bb-rule" aria-hidden="true">
                <span class="sv-bb-rule-line"></span>
                <span class="sv-bb-rule-diamond"></span>
                <span class="sv-bb-rule-line"></span>
            </div>

            <!--
                svBbOuter: clips + applies side fade masks
                svBbTrack: JS clones logos here, drives translateX via RAF
            -->
            <div class="sv-bb-outer" id="svBbOuter">
                <div class="sv-bb-track" id="svBbTrack">

                    <?php foreach ($sv_backed_by as $i => $partner): ?>
                    <div class="sv-bb-logo-slot" data-index="<?= $i ?>">

                        <?php if (!empty($partner['url'])): ?>
                        <a class="sv-bb-logo-wrap"
                           href="<?= htmlspecialchars($partner['url']) ?>"
                           target="_blank" rel="noopener noreferrer"
                           aria-label="<?= htmlspecialchars($partner['name']) ?>"
                           title="<?= htmlspecialchars($partner['name']) ?>">
                        <?php else: ?>
                        <div class="sv-bb-logo-wrap"
                             aria-label="<?= htmlspecialchars($partner['name']) ?>"
                             title="<?= htmlspecialchars($partner['name']) ?>">
                        <?php endif; ?>

                            <img src="<?= sv_bb_src($sv_bb_img_base, $partner['src']) ?>"
                                 alt="<?= htmlspecialchars($partner['name']) ?>"
                                 class="sv-bb-logo-img"
                                 draggable="false"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <!-- Fallback: show company name if image fails -->
                            <span class="sv-bb-logo-fallback" style="display:none">
                                <?= htmlspecialchars($partner['name']) ?>
                            </span>

                        <?php if (!empty($partner['url'])): ?>
                        </a>
                        <?php else: ?>
                        </div>
                        <?php endif; ?>

                    </div><!-- /logo-slot -->
                    <?php endforeach; ?>

                </div><!-- /svBbTrack -->
            </div><!-- /svBbOuter -->

            <!-- Bottom rule -->
            <div class="sv-bb-rule" aria-hidden="true">
                <span class="sv-bb-rule-line"></span>
                <span class="sv-bb-rule-diamond"></span>
                <span class="sv-bb-rule-line"></span>
            </div>

        </div><!-- /strip-wrap -->
    
    </div><!-- /container -->
</section>

<script src="components/backedby/backedby.js"></script>
