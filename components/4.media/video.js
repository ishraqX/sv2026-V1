/**
 * Sound Vision Video Slider
 * Interactive video carousel with drag and keyboard support
 */

document.addEventListener('DOMContentLoaded', function() {
    class VideoSlider {
        constructor() {
            // Elements
            this.videoSlides = document.querySelectorAll('.sv-video-slide');
            this.thumbnails = document.querySelectorAll('.sv-thumbnail-item');
            this.dots = document.querySelectorAll('.sv-progress-dot');
            this.prevBtn = document.querySelector('.sv-prev-btn');
            this.nextBtn = document.querySelector('.sv-next-btn');
            this.currentVideoElement = document.getElementById('currentVideo');
            this.carouselTrack = document.querySelector('.sv-carousel-track-container');
            this.thumbnailsTrack = document.getElementById('thumbnailsTrack');
            this.thumbPrevBtn = document.querySelector('.sv-thumb-prev');
            this.thumbNextBtn = document.querySelector('.sv-thumb-next');
            
            // State
            this.currentSlide = 0;
            this.totalSlides = this.videoSlides.length;
            this.isAnimating = false;
            this.videoPlayers = {};
            this.isPlaying = false;
            this.touchStartX = 0;
            this.touchEndX = 0;
            this.dragStartX = 0;
            this.isDragging = false;
            
            // Initialize
            this.init();
        }
        
        init() {
            console.log('🎬 Video Slider: Initializing...');
            
            // Setup navigation
            this.setupNavigation();
            
            // Setup video players
            this.setupVideoPlayers();
            
            // Setup touch/swipe
            this.setupTouchEvents();
            
            // Setup thumbnail scrolling
            this.setupThumbnailScrolling();
            
            // Setup keyboard navigation
            this.setupKeyboardNavigation();
            
            // Load first video
            this.goToSlide(0);
            
            console.log('✅ Video Slider: Ready');
        }
        
        setupNavigation() {
            // Previous button
            this.prevBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                this.prevSlide();
            });
            
            // Next button
            this.nextBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                this.nextSlide();
            });
            
            // Thumbnail clicks
            this.thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.goToSlide(index);
                });
            });
            
            // Dot clicks
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.goToSlide(index);
                });
            });
        }
        
        setupVideoPlayers() {
            this.videoSlides.forEach((slide, index) => {
                const videoId = slide.dataset.videoId;
                const youtubeId = slide.dataset.youtubeId;
                const videoUrl = slide.dataset.videoUrl;
                const videoElement = slide.querySelector('.sv-video-element');
                const playOverlay = slide.querySelector('.sv-play-overlay');
                const playBtn = slide.querySelector('.sv-play-pause-btn');
                const playIcon = slide.querySelector('.sv-play-icon');
                const pauseIcon = slide.querySelector('.sv-pause-icon');
                const progressFill = slide.querySelector('.sv-progress-fill');
                const currentTimeElement = slide.querySelector('.sv-current-time');
                const durationElement = slide.querySelector('.sv-duration');
                const fullscreenBtn = slide.querySelector('.sv-fullscreen-btn');
                const progressBar = slide.querySelector('.sv-progress-bar');
                
                // Create video element
                let video;
                if (youtubeId) {
                    // YouTube iframe
                    video = document.createElement('iframe');
                    video.src = `https://www.youtube.com/embed/${youtubeId}?rel=0&modestbranding=1`;
                    video.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                    video.allowFullscreen = true;
                    video.style.width = '100%';
                    video.style.height = '100%';
                    video.style.border = 'none';
                } else {
                    // HTML5 video
                    video = document.createElement('video');
                    video.src = videoUrl;
                    video.controls = false;
                    video.style.width = '100%';
                    video.style.height = '100%';
                    
                    // Add event listeners for HTML5 video
                    video.addEventListener('timeupdate', () => {
                        if (progressFill && currentTimeElement) {
                            const progress = (video.currentTime / video.duration) * 100;
                            progressFill.style.width = `${progress}%`;
                            
                            // Update time display
                            currentTimeElement.textContent = this.formatTime(video.currentTime);
                        }
                    });
                    
                    video.addEventListener('loadedmetadata', () => {
                        if (durationElement) {
                            durationElement.textContent = this.formatTime(video.duration);
                        }
                    });
                    
                    video.addEventListener('play', () => {
                        this.isPlaying = true;
                        if (playIcon && pauseIcon) {
                            playIcon.style.display = 'none';
                            pauseIcon.style.display = 'block';
                        }
                    });
                    
                    video.addEventListener('pause', () => {
                        this.isPlaying = false;
                        if (playIcon && pauseIcon) {
                            playIcon.style.display = 'block';
                            pauseIcon.style.display = 'none';
                        }
                    });
                }
                
                // Store video reference
                this.videoPlayers[index] = {
                    element: video,
                    isYouTube: !!youtubeId
                };
                
                // Add video to container
                videoElement.appendChild(video);
                
                // Play overlay click
                playOverlay?.addEventListener('click', () => {
                    this.playCurrentVideo();
                });
                
                // Play/pause button
                playBtn?.addEventListener('click', () => {
                    if (this.currentSlide === index) {
                        this.togglePlayPause();
                    }
                });
                
                // Progress bar click
                progressBar?.addEventListener('click', (e) => {
                    if (!this.videoPlayers[index].isYouTube) {
                        const rect = progressBar.getBoundingClientRect();
                        const pos = (e.clientX - rect.left) / rect.width;
                        video.currentTime = pos * video.duration;
                    }
                });
                
                // Fullscreen button
                fullscreenBtn?.addEventListener('click', () => {
                    this.toggleFullscreen(videoElement);
                });
            });
        }
        
        setupTouchEvents() {
            // Touch events for mobile swipe
            this.carouselTrack?.addEventListener('touchstart', (e) => {
                this.touchStartX = e.changedTouches[0].screenX;
                this.isDragging = false;
            });
            
            this.carouselTrack?.addEventListener('touchmove', (e) => {
                this.isDragging = true;
            });
            
            this.carouselTrack?.addEventListener('touchend', (e) => {
                if (!this.isDragging) return;
                
                this.touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe();
                this.isDragging = false;
            });
            
            // Mouse drag for desktop
            this.carouselTrack?.addEventListener('mousedown', (e) => {
                this.dragStartX = e.clientX;
                this.isDragging = true;
                e.preventDefault();
            });
            
            document.addEventListener('mousemove', (e) => {
                if (!this.isDragging) return;
                
                // Optional: Add visual drag feedback
            });
            
            document.addEventListener('mouseup', (e) => {
                if (!this.isDragging) return;
                
                this.dragEndX = e.clientX;
                this.handleDrag();
                this.isDragging = false;
            });
        }
        
        setupThumbnailScrolling() {
            if (!this.thumbnailsTrack || !this.thumbPrevBtn || !this.thumbNextBtn) return;
            
            const thumbScrollAmount = 220; // Width of thumbnail + gap
            
            this.thumbPrevBtn.addEventListener('click', () => {
                this.thumbnailsTrack.scrollBy({
                    left: -thumbScrollAmount,
                    behavior: 'smooth'
                });
            });
            
            this.thumbNextBtn.addEventListener('click', () => {
                this.thumbnailsTrack.scrollBy({
                    left: thumbScrollAmount,
                    behavior: 'smooth'
                });
            });
            
            // Auto-hide nav buttons when at edges
            this.updateThumbnailNavButtons();
            this.thumbnailsTrack.addEventListener('scroll', () => {
                this.updateThumbnailNavButtons();
            });
        }
        
        setupKeyboardNavigation() {
            document.addEventListener('keydown', (e) => {
                // Only if focus is within video slider
                if (!document.querySelector('.sv-video-slider-section:focus-within') && 
                    !e.target.closest('.sv-video-slider-section')) {
                    return;
                }
                
                switch(e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        this.prevSlide();
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        this.nextSlide();
                        break;
                    case ' ':
                    case 'Spacebar':
                        e.preventDefault();
                        this.togglePlayPause();
                        break;
                    case 'f':
                    case 'F':
                        if (e.altKey || e.ctrlKey) {
                            e.preventDefault();
                            const currentVideo = this.videoSlides[this.currentSlide];
                            const videoElement = currentVideo.querySelector('.sv-video-element');
                            this.toggleFullscreen(videoElement);
                        }
                        break;
                }
            });
        }
        
        goToSlide(index) {
            if (this.isAnimating || index < 0 || index >= this.totalSlides || index === this.currentSlide) {
                return;
            }
            
            this.isAnimating = true;
            
            // Pause current video
            this.pauseCurrentVideo();
            
            // Calculate direction for animation
            const direction = index > this.currentSlide ? 'next' : 'prev';
            
            // Hide current slide
            this.videoSlides[this.currentSlide].classList.remove('active');
            this.videoSlides[this.currentSlide].style.display = 'none';
            
            // Update current slide
            this.currentSlide = index;
            
            // Show new slide
            this.videoSlides[this.currentSlide].classList.add('active');
            this.videoSlides[this.currentSlide].style.display = 'block';
            
            // Update UI
            this.updateUI();
            
            // Center thumbnail
            this.centerThumbnail();
            
            // Reset animation flag
            setTimeout(() => {
                this.isAnimating = false;
            }, 300);
        }
        
        nextSlide() {
            const nextIndex = (this.currentSlide + 1) % this.totalSlides;
            this.goToSlide(nextIndex);
        }
        
        prevSlide() {
            const prevIndex = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
            this.goToSlide(prevIndex);
        }
        
        updateUI() {
            // Update counter
            if (this.currentVideoElement) {
                this.currentVideoElement.textContent = this.currentSlide + 1;
            }
            
            // Update thumbnails
            this.thumbnails.forEach((thumb, index) => {
                thumb.classList.toggle('active', index === this.currentSlide);
            });
            
            // Update dots
            this.dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === this.currentSlide);
            });
        }
        
        centerThumbnail() {
            if (!this.thumbnails[this.currentSlide] || !this.thumbnailsTrack) return;
            
            const thumbnail = this.thumbnails[this.currentSlide];
            const thumbTrackRect = this.thumbnailsTrack.getBoundingClientRect();
            const thumbRect = thumbnail.getBoundingClientRect();
            
            const scrollLeft = thumbnail.offsetLeft - (thumbTrackRect.width / 2) + (thumbRect.width / 2);
            
            this.thumbnailsTrack.scrollTo({
                left: scrollLeft,
                behavior: 'smooth'
            });
        }
        
        updateThumbnailNavButtons() {
            if (!this.thumbnailsTrack || !this.thumbPrevBtn || !this.thumbNextBtn) return;
            
            const scrollLeft = this.thumbnailsTrack.scrollLeft;
            const scrollWidth = this.thumbnailsTrack.scrollWidth;
            const clientWidth = this.thumbnailsTrack.clientWidth;
            
            // Show/hide previous button
            if (scrollLeft <= 10) {
                this.thumbPrevBtn.style.opacity = '0.3';
                this.thumbPrevBtn.style.pointerEvents = 'none';
            } else {
                this.thumbPrevBtn.style.opacity = '1';
                this.thumbPrevBtn.style.pointerEvents = 'auto';
            }
            
            // Show/hide next button
            if (scrollLeft >= scrollWidth - clientWidth - 10) {
                this.thumbNextBtn.style.opacity = '0.3';
                this.thumbNextBtn.style.pointerEvents = 'none';
            } else {
                this.thumbNextBtn.style.opacity = '1';
                this.thumbNextBtn.style.pointerEvents = 'auto';
            }
        }
        
        handleSwipe() {
            const swipeThreshold = 50;
            const swipeDistance = this.touchStartX - this.touchEndX;
            
            if (Math.abs(swipeDistance) < swipeThreshold) return;
            
            if (swipeDistance > 0) {
                // Swipe left - next
                this.nextSlide();
            } else {
                // Swipe right - previous
                this.prevSlide();
            }
        }
        
        handleDrag() {
            const dragThreshold = 50;
            const dragDistance = this.dragStartX - this.dragEndX;
            
            if (Math.abs(dragDistance) < dragThreshold) return;
            
            if (dragDistance > 0) {
                // Drag left - next
                this.nextSlide();
            } else {
                // Drag right - previous
                this.prevSlide();
            }
        }
        
        playCurrentVideo() {
            const currentVideo = this.videoPlayers[this.currentSlide];
            if (!currentVideo) return;
            
            if (currentVideo.isYouTube) {
                // For YouTube, we need to reload with autoplay
                const slide = this.videoSlides[this.currentSlide];
                const youtubeId = slide.dataset.youtubeId;
                const videoElement = slide.querySelector('.sv-video-element');
                
                // Replace iframe with autoplay
                videoElement.innerHTML = '';
                const iframe = document.createElement('iframe');
                iframe.src = `https://www.youtube.com/embed/${youtubeId}?autoplay=1&rel=0&modestbranding=1`;
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                iframe.allowFullscreen = true;
                iframe.style.width = '100%';
                iframe.style.height = '100%';
                iframe.style.border = 'none';
                videoElement.appendChild(iframe);
                
                // Hide play overlay
                const playOverlay = slide.querySelector('.sv-play-overlay');
                if (playOverlay) {
                    playOverlay.style.opacity = '0';
                    playOverlay.style.pointerEvents = 'none';
                }
                
                // Update play button state
                const playIcon = slide.querySelector('.sv-play-icon');
                const pauseIcon = slide.querySelector('.sv-pause-icon');
                if (playIcon && pauseIcon) {
                    playIcon.style.display = 'none';
                    pauseIcon.style.display = 'block';
                }
                
                this.isPlaying = true;
            } else {
                // HTML5 video
                const video = currentVideo.element;
                video.play().catch(e => {
                    console.error('Error playing video:', e);
                });
            }
        }
        
        pauseCurrentVideo() {
            const currentVideo = this.videoPlayers[this.currentSlide];
            if (!currentVideo || !this.isPlaying) return;
            
            if (currentVideo.isYouTube) {
                // Can't pause YouTube without API
                this.isPlaying = false;
                
                // Update play button state
                const slide = this.videoSlides[this.currentSlide];
                const playIcon = slide.querySelector('.sv-play-icon');
                const pauseIcon = slide.querySelector('.sv-pause-icon');
                if (playIcon && pauseIcon) {
                    playIcon.style.display = 'block';
                    pauseIcon.style.display = 'none';
                }
            } else {
                currentVideo.element.pause();
            }
        }
        
        togglePlayPause() {
            const currentVideo = this.videoPlayers[this.currentSlide];
            if (!currentVideo) return;
            
            if (currentVideo.isYouTube) {
                // Toggle between play and reload
                if (this.isPlaying) {
                    // Can't pause, so reload without autoplay
                    const slide = this.videoSlides[this.currentSlide];
                    const youtubeId = slide.dataset.youtubeId;
                    const videoElement = slide.querySelector('.sv-video-element');
                    
                    videoElement.innerHTML = '';
                    const iframe = document.createElement('iframe');
                    iframe.src = `https://www.youtube.com/embed/${youtubeId}?rel=0&modestbranding=1`;
                    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                    iframe.allowFullscreen = true;
                    iframe.style.width = '100%';
                    iframe.style.height = '100%';
                    iframe.style.border = 'none';
                    videoElement.appendChild(iframe);
                    
                    // Show play overlay
                    const playOverlay = slide.querySelector('.sv-play-overlay');
                    if (playOverlay) {
                        playOverlay.style.opacity = '1';
                        playOverlay.style.pointerEvents = 'auto';
                    }
                    
                    this.isPlaying = false;
                } else {
                    this.playCurrentVideo();
                }
            } else {
                // HTML5 video
                if (currentVideo.element.paused) {
                    currentVideo.element.play();
                } else {
                    currentVideo.element.pause();
                }
            }
        }
        
        toggleFullscreen(element) {
            if (!document.fullscreenElement) {
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }
        
        formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
    }
    
    // Initialize the slider
    const videoSlider = new VideoSlider();
    
    // Make slider available globally for debugging
    window.videoSlider = videoSlider;
});