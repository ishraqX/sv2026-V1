<?php
$left_features = [
    [
        'id' => 'describe',
        'icon' => '👁️',
        'title' => 'Describe Surroundings',
        'description' => 'Advanced AI vision that understands and narrates your environment in real-time with remarkable accuracy.',
        'color' => '#6366f1',
        'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
    ],
    [
        'id' => 'voice',
        'icon' => '🎤',
        'title' => 'Voice Assist',
        'description' => 'Natural voice commands in Bangla and English for seamless, hands-free interaction with your world.',
        'color' => '#ec4899',
        'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
    ],
    [
        'id' => 'audiobook',
        'icon' => '📚',
        'title' => 'Unlimited Audiobooks',
        'description' => 'Access to thousands of audiobooks and documents, read aloud with natural-sounding voices.',
        'color' => '#10b981',
        'gradient' => 'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)'
    ]
];

$right_features = [
    [
        'id' => 'handsfree',
        'icon' => '✋',
        'title' => 'Hands-Free Operation',
        'description' => 'Complete control through voice and intuitive gestures. Your hands stay free for what matters.',
        'color' => '#f59e0b',
        'gradient' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'
    ],
    [
        'id' => 'lightweight',
        'icon' => '⚡',
        'title' => 'Lightweight & Stylish',
        'description' => 'Engineered for all-day comfort with a modern design that looks natural and professional.',
        'color' => '#06b6d4',
        'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
    ],
    [
        'id' => 'privacy',
        'icon' => '🔒',
        'title' => 'Privacy Mode',
        'description' => '100% offline processing. Your data never leaves your device. Complete privacy, guaranteed.',
        'color' => '#8b5cf6',
        'gradient' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sound Vision Smart Glasses - Revolutionary features for the visually impaired">
    <title>Sound Vision Features - Smart Glasses Technology</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="features.css">
</head>
<body>

<!-- ========================================
     FEATURES SECTION
======================================== -->
<section class="sv-features-section" id="features">
    <div class="sv-features-container">
        
        <!-- Section Header -->
        <div class="sv-section-header" data-animate="fade-up">
            <span class="sv-section-label">Revolutionary Technology</span>
            <h2 class="sv-section-title">
                Experience the <span class="sv-gradient-text">Future</span> of Vision
            </h2>
            <p class="sv-section-subtitle">
                Six powerful features working in perfect harmony to transform your daily life
            </p>
        </div>

        <!-- Features Layout -->
        <div class="sv-features-layout">
            
            <!-- Left Features -->
            <div class="sv-features-column sv-left-column">
                <?php foreach ($left_features as $index => $feature): ?>
                    <article class="sv-feature-item sv-left-item" 
                             data-animate="slide-right"
                             data-delay="<?php echo ($index * 0.15); ?>"
                             style="--feature-color: <?php echo $feature['color']; ?>; --feature-gradient: <?php echo $feature['gradient']; ?>">
                        
                        <div class="sv-feature-content">
                            <div class="sv-feature-icon-wrapper">
                                <span class="sv-feature-icon"><?php echo $feature['icon']; ?></span>
                                <div class="sv-icon-glow"></div>
                            </div>
                            
                            <div class="sv-feature-text">
                                <h3 class="sv-feature-title"><?php echo htmlspecialchars($feature['title']); ?></h3>
                                <p class="sv-feature-description"><?php echo htmlspecialchars($feature['description']); ?></p>
                            </div>
                        </div>
                        
                        <div class="sv-feature-line"></div>
                        <div class="sv-feature-dot"></div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Center Image (Floating) -->
            <div class="sv-center-image" data-animate="zoom-in">
                <div class="sv-image-container">
                    <div class="sv-image-wrapper">
                        <!-- Replace with actual image -->
                        <div class="sv-placeholder-image">
                            <!--<span class="sv-glasses-icon">👓</span> -->
                            <img src="assets/hero/hero7.png" alt="Sound Vision Smart Glasses" class="sv-glasses-image">
                            <div class="sv-pulse-ring"></div>
                            <div class="sv-pulse-ring" style="animation-delay: 0.5s;"></div>
                            <div class="sv-pulse-ring" style="animation-delay: 1s;"></div>
                        </div>
                        <!-- Uncomment when you have image -->
                            
                    </div>
                    
                    <!-- Floating Particles -->
                    <div class="sv-floating-particles">
                        <div class="sv-particle" style="--delay: 0s; --x: -50px; --y: -60px;"></div>
                        <div class="sv-particle" style="--delay: 0.5s; --x: 60px; --y: -40px;"></div>
                        <div class="sv-particle" style="--delay: 1s; --x: -40px; --y: 50px;"></div>
                        <div class="sv-particle" style="--delay: 1.5s; --x: 70px; --y: 60px;"></div>
                        <div class="sv-particle" style="--delay: 2s; --x: 0px; --y: -80px;"></div>
                    </div>
                </div>
            </div>

            <!-- Right Features -->
            <div class="sv-features-column sv-right-column">
                <?php foreach ($right_features as $index => $feature): ?>
                    <article class="sv-feature-item sv-right-item" 
                             data-animate="slide-left"
                             data-delay="<?php echo ($index * 0.15); ?>"
                             style="--feature-color: <?php echo $feature['color']; ?>; --feature-gradient: <?php echo $feature['gradient']; ?>">
                        
                        <div class="sv-feature-content">
                            <div class="sv-feature-icon-wrapper">
                                <span class="sv-feature-icon"><?php echo $feature['icon']; ?></span>
                                <div class="sv-icon-glow"></div>
                            </div>
                            
                            <div class="sv-feature-text">
                                <h3 class="sv-feature-title"><?php echo htmlspecialchars($feature['title']); ?></h3>
                                <p class="sv-feature-description"><?php echo htmlspecialchars($feature['description']); ?></p>
                            </div>
                        </div>
                        
                        <div class="sv-feature-line"></div>
                        <div class="sv-feature-dot"></div>
                    </article>
                <?php endforeach; ?>
            </div>

        </div>

    </div>

    <!-- Background Elements -->
    <div class="sv-bg-elements" aria-hidden="true">
        <div class="sv-bg-gradient"></div>
        <div class="sv-bg-grid"></div>
        <div class="sv-bg-orb sv-orb-1"></div>
        <div class="sv-bg-orb sv-orb-2"></div>
    </div>
</section>

<!-- JavaScript -->
<script src="components/3.feature/features.js"></script>

</body>
</html>