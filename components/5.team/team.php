<?php
// ============================================================
// team.php — Sound Vision  |  Team Section
// Namespace: sv-tm-  |  IDs: svTm  |  PHP vars: $tm_
// Fixed CEO card (left) + RAF infinite scroll (right)
// ============================================================

$tm_ceo = [
    'name'    => 'Ishraq Uddin Chowdhury',
    'role'    => 'Founder & Chief Executive Officer',
    'bio'     => 'Visionary founder and CEO with 7+ years driving innovation, creative excellence, and sustainable growth across global markets.',
    'img'     => 'components/5.team/teamPic/ceo.jpg',
    'skills'  => ['Strategy', 'Leadership', 'Innovation', 'Vision'],
    'link'    => 'ishraq.php',
    'socials' => [
        ['net'=>'facebook','url'=>'https://facebook.com'],
        ['net'=>'linkedin','url'=>'https://linkedin.com'],
        ['net'=>'github',  'url'=>'https://github.com'],
        ['net'=>'email',   'url'=>'ishraq@soundvision.com.bd'],
    ],
];

$tm_members = [
    ['name'=>'Imtiaz Uddin Chowdhury','role'=>'Chief Technology Officer (CTO)','bio'=>'Leading technological innovation and architecture, driving scalable solutions and technical excellence across all platforms.','img'=>'components/5.team/teamPic/imtiaz.png','skills'=>['System Architecture','Team Leadership','Cloud Computing','Innovation'],'link'=>'imtiaz.php','socials'=>[['net'=>'facebook','url'=>'https://facebook.com'],['net'=>'linkedin','url'=>'https://linkedin.com'],['net'=>'github','url'=>'https://github.com'],['net'=>'email','url'=>'mailto:imtiaz@soundvision.com']]],
    ['name'=>'Md Azhar Uddin','role'=>'Chief Operating Officer (COO)','bio'=>'Streamlining operations and optimizing workflows to ensure organizational efficiency and exceptional service delivery.','img'=>'components/5.team/teamPic/azhar.jpg','skills'=>['Operations','Process Optimization','Team Management','Strategy'],'link'=>'azhar.php','socials'=>[['net'=>'facebook','url'=>'https://facebook.com'],['net'=>'linkedin','url'=>'https://linkedin.com'],['net'=>'github','url'=>'https://github.com'],['net'=>'email','url'=>'mailto:azhar@soundvision.com']]],
    ['name'=>'Fahima Abida Chowdhury','role'=>'Head of Business Development','bio'=>'Driving strategic partnerships and business growth, identifying new opportunities and fostering long-term client relationships.','img'=>'components/5.team/teamPic/fahima.jpg','skills'=>['Business Strategy','Partnerships','Negotiation','Market Analysis'],'link'=>'fahima.php','socials'=>[['net'=>'facebook','url'=>'https://facebook.com'],['net'=>'linkedin','url'=>'https://linkedin.com'],['net'=>'github','url'=>'https://github.com'],['net'=>'email','url'=>'mailto:fahima@soundvision.com']]],
    ['name'=>'Sumaia Bintey Ismail','role'=>'Chief Marketing Officer (CMO)','bio'=>'Strategic marketing leader crafting compelling brand narratives and data-driven campaigns that drive engagement and growth.','img'=>'components/5.team/teamPic/sumaia.jpg','skills'=>['Marketing Strategy','Brand Management','Digital Marketing','Analytics'],'link'=>'sumaia.php','socials'=>[['net'=>'facebook','url'=>'https://facebook.com'],['net'=>'linkedin','url'=>'https://linkedin.com'],['net'=>'github','url'=>'https://github.com'],['net'=>'email','url'=>'mailto:sumaia@soundvision.com']]],
    ['name'=>'Abdul Momen Safin','role'=>'Software Quality Assurance (SQA)','bio'=>'Ensuring highest quality standards through rigorous testing methodologies and continuous improvement processes.','img'=>'components/5.team/teamPic/safin.jpg','skills'=>['Quality Assurance','Automation Testing','Performance Testing','CI/CD'],'link'=>'safin.php','socials'=>[['net'=>'facebook','url'=>'https://facebook.com'],['net'=>'linkedin','url'=>'https://linkedin.com'],['net'=>'github','url'=>'https://github.com'],['net'=>'email','url'=>'mailto:safin@soundvision.com']]],
    ['name'=>'Jisan Ahmed','role'=>'Customer Relations Manager','bio'=>'Building and nurturing strong client relationships, ensuring exceptional customer experience and satisfaction across all touchpoints.','img'=>'components/5.team/teamPic/jisan.jpg','skills'=>['Customer Service','Communication','Problem Solving','CRM'],'link'=>'jisan.php','socials'=>[['net'=>'facebook','url'=>'https://facebook.com'],['net'=>'linkedin','url'=>'https://linkedin.com'],['net'=>'github','url'=>'https://github.com'],['net'=>'email','url'=>'mailto:jisan@soundvision.com']]],
    ['name'=>'Safayet Hosain','role'=>'Advisor','bio'=>'Strategic advisor providing valuable insights and guidance to shape company vision and drive sustainable growth.','img'=>'components/5.team/teamPic/safayet.png','skills'=>['Strategic Planning','Mentorship','Industry Expertise','Governance'],'link'=>'safayet.php','socials'=>[['net'=>'facebook','url'=>'https://facebook.com'],['net'=>'linkedin','url'=>'https://linkedin.com'],['net'=>'github','url'=>'https://github.com'],['net'=>'email','url'=>'mailto:safayet@soundvision.com']]],
];

function tm_svg(string $net): string {
    $icons = [
        'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
        'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4V9h4v1.76A4 4 0 0116 8zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>',
        'github'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844a9.59 9.59 0 012.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>',
        'email'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8l10 7 10-7"/></svg>',
    ];
    return $icons[$net] ?? '';
}
?>
<link rel="stylesheet" href="team.css">

<section class="sv-tm-section" id="team">

    <div class="sv-tm-bg" aria-hidden="true">
        <div class="sv-tm-bg-glow sv-tm-bg-glow--a"></div>
        <div class="sv-tm-bg-glow sv-tm-bg-glow--b"></div>me
        <div class="sv-tm-bg-glow sv-tm-bg-glow--c"></div>
        <div class="sv-tm-bg-grid"></div>
        <div class="sv-tm-particles" id="svTmParticles"></div>
    </div>

    <div class="sv-tm-container">

        <header class="sv-tm-header" data-tm-reveal>
            <div class="sv-tm-eyebrow">
                <span class="sv-tm-eyebrow-line"></span>
                <span class="sv-tm-eyebrow-text">The People Behind The Vision</span>
                <span class="sv-tm-eyebrow-line"></span>
            </div>
            <h2 class="sv-tm-title">
                <span class="sv-tm-title-normal">Our </span><span class="sv-tm-title-accent">Amazing</span><span class="sv-tm-title-normal"> Team</span>
            </h2>
            <p class="sv-tm-subtitle">Talented professionals united by creativity, driven by purpose, and committed to bringing your vision to life with excellence.</p>
            <div class="sv-tm-stats-row">
                <div class="sv-tm-stat-item"><span class="sv-tm-stat-n">10</span><span class="sv-tm-stat-l">Team Members</span></div>
                <div class="sv-tm-stat-sep"></div>
                <div class="sv-tm-stat-item"><span class="sv-tm-stat-n">15+</span><span class="sv-tm-stat-l">Years Combined</span></div>
                <div class="sv-tm-stat-sep"></div>
                <div class="sv-tm-stat-item"><span class="sv-tm-stat-n">200+</span><span class="sv-tm-stat-l">Projects Done</span></div>
            </div>
        </header>

        <div class="sv-tm-layout" data-tm-reveal>

            <div class="sv-tm-ceo-col">
                <div class="sv-tm-ceo-frame">
                    
                    <a href="<?= htmlspecialchars($tm_ceo['link']) ?>" class="sv-tm-ceo-card" target="_blank">
                        <div class="sv-tm-crown" aria-hidden="true">
                        <span class="sv-tm-crown-icon"><!--👑 --></span>
                        <span class="sv-tm-crown-text">CEO &amp; Founder</span>
                    </div>
                        <span class="sv-tm-ceo-ring" aria-hidden="true"></span>
                        <div class="sv-tm-ceo-photo">
                            <img src="<?= htmlspecialchars($tm_ceo['img']) ?>" alt="<?= htmlspecialchars($tm_ceo['name']) ?>" class="sv-tm-ceo-img">
                            <div class="sv-tm-ceo-photo-placeholder">👤</div>
                            <div class="sv-tm-ceo-photo-overlay"></div>
                        </div>
                    </a>
                        
                    <div class="sv-tm-ceo-body">
                        <p class="sv-tm-ceo-role-label"><?= htmlspecialchars($tm_ceo['role']) ?></p>
                        <br>
                        <h3 class="sv-tm-ceo-name"><?= htmlspecialchars($tm_ceo['name']) ?></h3>
                        <p class="sv-tm-ceo-bio"><?= htmlspecialchars($tm_ceo['bio']) ?></p>
                        <br> 
                        <div class="sv-tm-chips">
                            <?php foreach ($tm_ceo['skills'] as $sk): ?>
                                <span class="sv-tm-chip"><?= htmlspecialchars($sk) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="sv-tm-ceo-socials">
                            <?php foreach ($tm_ceo['socials'] as $s): ?>
                                <a href="<?= htmlspecialchars($s['url']) ?>" class="sv-tm-soc-btn" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($s['net']) ?>" onclick="event.stopPropagation()"><?= tm_svg($s['net']) ?></a>
                            <?php endforeach; ?>
                        </div>
                        <div class="sv-tm-ceo-cta">View Full Profile <span class="sv-tm-cta-arr">→</span></div>
                    </div>
                </div>
            </div>

            <div class="sv-tm-scroll-col">
                <div class="sv-tm-scroll-outer" id="svTmOuter">
                    <div class="sv-tm-scroll-track" id="svTmTrack">
                        <?php foreach ($tm_members as $m): ?>
                        <article class="sv-tm-card">
                            <div class="sv-tm-card-photo">
                                <img src="<?= htmlspecialchars($m['img']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" class="sv-tm-card-img">
                                <div class="sv-tm-card-photo-placeholder">👤</div>
                                <div class="sv-tm-card-photo-overlay"></div>
                                <div class="sv-tm-card-role"><?= htmlspecialchars($m['role']) ?></div>
                            </div>
                            <div class="sv-tm-card-body">
                                <h3 class="sv-tm-card-name"><?= htmlspecialchars($m['name']) ?></h3>
                                <div class="sv-tm-chips sv-tm-chips--sm">
                                    <?php foreach ($m['skills'] as $sk): ?>
                                    <span class="sv-tm-chip sv-tm-chip--sm"><?= htmlspecialchars($sk) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="sv-tm-card-socials">
                                    <?php foreach ($m['socials'] as $s): ?>
                                    <a href="<?= htmlspecialchars($s['url']) ?>" class="sv-tm-soc-btn sv-tm-soc-btn--sm" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($s['net']) ?>" onclick="event.stopPropagation()"><?= tm_svg($s['net']) ?></a>
                                    <?php endforeach; ?>
                                </div>
                                <a href="<?= htmlspecialchars($m['link']) ?>" class="sv-tm-card-link-btn" target="_blank" rel="noopener">View Profile <span class="sv-tm-cta-arr">→</span></a>
                            </div>
                            <div class="sv-tm-card-tooltip" role="tooltip"><?= htmlspecialchars($m['bio']) ?></div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <p class="sv-tm-hint"><span class="sv-tm-hint-dot"></span>Hover to pause &nbsp;·&nbsp; Click to view profile</p>
            </div>

        </div>
    </div>
</section>
<script src="components/5.team/team.js"></script>