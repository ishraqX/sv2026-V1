<?php
$sdg_data = [
    [
        'no'    => '3',
        'full'  => 'Good Health & Well-being',
        'color' => '#4CAF50',
        'img'   => 'https://upload.wikimedia.org/wikipedia/commons/c/c4/Sustainable_Development_Goal_3.png',
        'align' => 'Sound-based diagnostics enabling safer, more accessible healthcare for all.'
    ],
    [
        'no'    => '4',
        'full'  => 'Quality Education',
        'color' => '#C5192D',
        'img'   => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Sustainable_Development_Goal_04QualityEducation.svg/1280px-Sustainable_Development_Goal_04QualityEducation.svg.png',
        'align' => 'AI audio tools that break barriers and open inclusive learning to everyone.'
    ],
    [
        'no'    => '11',
        'full'  => 'Sustainable Cities',
        'color' => '#FD9D24',
        'img'   => 'https://upload.wikimedia.org/wikipedia/commons/8/81/Sustainable_Development_Goal_11.png',
        'align' => 'Smart sound navigation empowering safe, independent urban mobility.'
    ]
];
?>

<section class="sdg-section" id="sdg">

    <!-- Warm gradient mesh — matches hero exactly -->
    <div class="sdg-bg" aria-hidden="true">
        <div class="sdg-blob sdg-blob--a"></div>
        <div class="sdg-blob sdg-blob--b"></div>
    </div>

    <div class="sdg-wrap">

        <!-- Section header -->
        <div class="sdg-header">
            <div class="sdg-kicker">
                <span class="sdg-kicker-dot" aria-hidden="true"></span>
                UN Sustainable Development Goals
            </div>
            <h2 class="sdg-title">Our Global <em>Impact</em></h2>
            <p class="sdg-sub">Sound Vision directly advances three UN SDGs — amplifying independence, inclusion, and health worldwide.</p>
        </div>

        <!-- 3 SDG cards -->
        <div class="sdg-grid">
            <?php foreach ($sdg_data as $i => $item): ?>
            <div class="sdg-card" style="--c: <?php echo $item['color']; ?>;" aria-label="SDG <?php echo $item['no']; ?> — <?php echo $item['full']; ?>">

                <!-- Coloured top accent line -->
                <div class="sdg-card-accent" aria-hidden="true"></div>

                <!-- Goal number badge -->
                <span class="sdg-badge">Goal <?php echo $item['no']; ?></span>

                <!-- SDG official icon -->
                <div class="sdg-icon-wrap">
                    <img src="<?php echo $item['img']; ?>"
                         alt="SDG <?php echo $item['no']; ?>"
                         >
                </div>

                <!-- Text -->
                <div class="sdg-card-text-block">
                    <h3 class="sdg-card-title"><?php echo $item['full']; ?></h3>
                    <p  class="sdg-card-desc"><?php echo $item['align']; ?></p>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

    </div><!-- /sdg-wrap -->
</section>