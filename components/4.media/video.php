<?php
$videos = [
    [
        'id' => 'video-1',
        'title' => 'National Innovation Expo 2024',
        'description' => 'Sound Vision showcased at the biggest innovation expo, demonstrating how our smart glasses transform lives.',
        'thumbnail' => 'assets/videos/thumbnails/expo-2024.jpg',
        'video_url' => 'assets/videos/expo-2024.mp4',
        'youtube_id' => 'yz2qyNxIUmk',
        'date' => 'March 2024',
        'location' => 'Dhaka, Bangladesh',
        'category' => 'Expo',
        'duration' => '3:45'
    ],
    [
        'id' => 'video-2',
        'title' => 'Channel 24 News Feature',
        'description' => 'Exclusive coverage of Sound Vision on Bangladesh\'s leading news channel, highlighting our mission.',
        'thumbnail' => 'assets/videos/thumbnails/channel24.jpg',
        'video_url' => 'assets/videos/channel24.mp4',
        'youtube_id' => 'T6fMr8wyK3A',
        'date' => 'February 2024',
        'location' => 'National News',
        'category' => 'News',
        'duration' => '5:20'
    ],
    [
        'id' => 'video-3',
        'title' => 'ATN Bangla Interview',
        'description' => 'In-depth interview with our founder discussing the technology behind Sound Vision smart glasses.',
        'thumbnail' => 'assets/videos/thumbnails/atn-bangla.jpg',
        'video_url' => 'assets/videos/atn-bangla.mp4',
        'youtube_id' => 'dQw4w9WgXcQ',
        'date' => 'January 2024',
        'location' => 'ATN Studio',
        'category' => 'Interview',
        'duration' => '8:15'
    ],
    [
        'id' => 'video-4',
        'title' => 'Tech Innovation Summit',
        'description' => 'Live demonstration at the Tech Innovation Summit, showing real-time object detection and voice guidance.',
        'thumbnail' => 'assets/videos/thumbnails/tech-summit.jpg',
        'video_url' => 'assets/videos/tech-summit.mp4',
        'youtube_id' => 'x7YtT6gQm7k',
        'date' => 'December 2023',
        'location' => 'Dhaka Convention Center',
        'category' => 'Demo',
        'duration' => '4:30'
    ],
    [
        'id' => 'video-5',
        'title' => 'User Testimonial - Real Impact',
        'description' => 'Heartwarming story of how Sound Vision changed the life of our first user, featuring their daily experience.',
        'thumbnail' => 'assets/videos/thumbnails/testimonial.jpg',
        'video_url' => 'assets/videos/testimonial.mp4',
        'youtube_id' => 'n9Y9Zc8KJvM',
        'date' => 'November 2023',
        'location' => 'Customer Story',
        'category' => 'Testimonial',
        'duration' => '6:40'
    ],
    [
        'id' => 'video-6',
        'title' => 'Jamuna TV Feature',
        'description' => 'Special segment on Jamuna Television showcasing the social impact of assistive technology.',
        'thumbnail' => 'assets/videos/thumbnails/jamuna-tv.jpg',
        'video_url' => 'assets/videos/jamuna-tv.mp4',
        'youtube_id' => 'p8Q9qL3vBwE',
        'date' => 'October 2023',
        'location' => 'National Coverage',
        'category' => 'News',
        'duration' => '7:10'
    ]
];

$categories = array_unique(array_column($videos, 'category'));

function getGradientBackground($index) {
    $gradients = [
        'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);',
        'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);',
        'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);',
        'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);',
        'background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);',
        'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);'
    ];
    return $gradients[$index % count($gradients)];
}
?>
  <link rel="stylesheet" href="video.css">
<!-- ========================================
     VIDEO SLIDER SECTION
======================================== -->
<section class="sv-video-slider-section" id="media">
    <div class="sv-video-container">
        
        <!-- Section Header - CENTERED -->
        <div class="sv-slider-header sv-center-header">
            <div class="sv-header-content">
                <span class="sv-section-label">Video Gallery</span>
                <h2 class="sv-section-title">
                    Watch Sound Vision <span class="sv-highlight-text">In Action</span>
                </h2>
                <p class="sv-section-subtitle">
                    Explore our video collection. Click arrows or drag to navigate.
                </p>
            </div>
            
        
        <!-- Main Video Carousel -->
        <div class="sv-video-carousel-container">
            <!-- Navigation Arrows -->
            <button class="sv-carousel-nav sv-prev-btn" aria-label="Previous video">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </button>
            
            <button class="sv-carousel-nav sv-next-btn" aria-label="Next video">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </button>

            <!-- Carousel Track -->
            <div class="sv-carousel-track-container">
                <div class="sv-carousel-track" id="videoCarouselTrack">
                    <?php foreach ($videos as $index => $video): ?>
                        <div class="sv-video-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                             data-video-index="<?php echo $index; ?>"
                             data-video-id="<?php echo $video['id']; ?>"
                             data-video-url="<?php echo $video['video_url']; ?>"
                             data-youtube-id="<?php echo $video['youtube_id']; ?>">
                            
                            <!-- Video Player Container -->
                            <div class="sv-slide-video-container">
                                <div class="sv-video-wrapper">
                                    <!-- Placeholder -->
                                    <div class="sv-video-placeholder" style="<?php echo getGradientBackground($index); ?>">
                                        <div class="sv-play-overlay">
                                            <div class="sv-play-button-large">
                                                <svg viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                                <span class="sv-play-text">Play Video</span>
                                            </div>
                                        </div>
                                        <div class="sv-video-info-overlay">
                                            <span class="sv-video-category"><?php echo $video['category']; ?></span>
                                            <span class="sv-video-duration"><?php echo $video['duration']; ?></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Video Element -->
                                    <div class="sv-video-element" id="video-<?php echo $video['id']; ?>">
                                        <!-- Video will be loaded here -->
                                    </div>
                                </div>
                                
                                <!-- Video Controls -->
                                <div class="sv-video-controls">
                                    <button class="sv-control-btn sv-play-pause-btn" aria-label="Play/Pause">
                                        <svg class="sv-play-icon" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        <svg class="sv-pause-icon" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                        </svg>
                                    </button>
                                    
                                    <div class="sv-progress-container">
                                        <div class="sv-progress-bar">
                                            <div class="sv-progress-fill"></div>
                                        </div>
                                        <div class="sv-time-display">
                                            <span class="sv-current-time">0:00</span>
                                            <span class="sv-duration"><?php echo $video['duration']; ?></span>
                                        </div>
                                    </div>
                                    
                                    <button class="sv-control-btn sv-fullscreen-btn" aria-label="Fullscreen">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Video Info -->
                            <div class="sv-slide-info">
                                <div class="sv-info-header">
                                    <h3 class="sv-video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                                    <div class="sv-video-meta">
                                        <span class="sv-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" y1="2" x2="16" y2="6"/>
                                                <line x1="8" y1="2" x2="8" y2="6"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                            <?php echo $video['date']; ?>
                                        </span>
                                        <span class="sv-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                <circle cx="12" cy="10" r="3"/>
                                            </svg>
                                            <?php echo $video['location']; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="sv-video-description">
                                    <?php echo htmlspecialchars($video['description']); ?>
                                </p>
                                
                                <div class="sv-action-buttons">
                                    <button class="sv-action-btn sv-share-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/>
                                            <polyline points="16 6 12 2 8 6"/>
                                            <line x1="12" y1="2" x2="12" y2="15"/>
                                        </svg>
                                        Share
                                    </button>
                                    <button class="sv-action-btn sv-save-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                                        </svg>
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Progress Dots -->
            <div class="sv-progress-dots">
                <?php for ($i = 0; $i < count($videos); $i++): ?>
                    <button class="sv-progress-dot <?php echo $i === 0 ? 'active' : ''; ?>" 
                            data-slide-index="<?php echo $i; ?>"
                            aria-label="Go to video <?php echo $i + 1; ?>">
                        <span class="sv-dot-inner"></span>
                    </button>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Thumbnail Carousel -->
        <div class="sv-thumbnail-carousel">
            <div class="sv-thumbnails-track" id="thumbnailsTrack">
                <?php foreach ($videos as $index => $video): ?>
                    <button class="sv-thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-video-index="<?php echo $index; ?>"
                            aria-label="Play video: <?php echo htmlspecialchars($video['title']); ?>">
                        
                        <div class="sv-thumbnail-image" style="<?php echo getGradientBackground($index); ?>">
                            <div class="sv-thumbnail-overlay">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                            <span class="sv-thumbnail-duration"><?php echo $video['duration']; ?></span>
                        </div>
                        
                        <div class="sv-thumbnail-info">
                            <h4 class="sv-thumbnail-title"><?php echo htmlspecialchars($video['title']); ?></h4>
                            <span class="sv-thumbnail-category"><?php echo $video['category']; ?></span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Thumbnail Navigation -->
            <button class="sv-thumb-nav sv-thumb-prev" aria-label="Scroll thumbnails left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </button>
            <button class="sv-thumb-nav sv-thumb-next" aria-label="Scroll thumbnails right">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </button>
        </div>

        

    </div>
</section>

<script src="components/4.media/video.js"></script>