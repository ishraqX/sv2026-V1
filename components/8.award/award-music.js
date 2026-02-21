/**
 * award-music.js — Sound Vision Awards Ambient Music
 * Simple and reliable - Plays only when award section is visible
 */
(function() {
    'use strict';

    /* ── Music Configuration ───────────────────────────────── */
    const AWARD_MUSIC_URL = 'components/8.award/award_music.mp3'; // Replace with your music URL
    const MAX_VOLUME = 0.3; // Comfortable background volume
    const FADE_SPEED = 0.05; // Volume change per frame for smooth fade

    let awardAudio = null;
    let musicActive = false;
    let currentVolume = 0;
    let targetVolume = 0;
    let fadeInterval = null;
    let isPlaying = false;
    let lastSectionVisible = false;

    /* ── Initialize Music System ── */
    function initAwardMusic() {
        const section = document.querySelector('.sv-awards-section, #svAwardsSection, .awards-section');
        if (!section) {
            console.warn('Award section not found. Music will not play.');
            return;
        }

        // Create audio element
        awardAudio = new Audio(AWARD_MUSIC_URL);
        awardAudio.loop = true;
        awardAudio.volume = 0;
        awardAudio.preload = 'auto';

        // Check visibility on scroll (throttled)
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    checkSectionVisibility(section);
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Check on load and resize
        window.addEventListener('load', () => checkSectionVisibility(section));
        window.addEventListener('resize', () => checkSectionVisibility(section));

        // Initial check
        setTimeout(() => checkSectionVisibility(section), 500);

        // Start fade loop
        startFadeLoop();

        // Handle user interaction for autoplay
        enableAudioOnUserInteraction(section);
    }

    /* ── Enable audio on first user interaction (browser policy) ── */
    function enableAudioOnUserInteraction(section) {
        const enableHandler = function() {
            if (awardAudio) {
                // Try to play if section is visible
                checkSectionVisibility(section);
            }
            // Remove listeners after first interaction
            document.removeEventListener('click', enableHandler);
            document.removeEventListener('touchstart', enableHandler);
            document.removeEventListener('keydown', enableHandler);
        };

        document.addEventListener('click', enableHandler, { once: true });
        document.addEventListener('touchstart', enableHandler, { once: true });
        document.addEventListener('keydown', enableHandler, { once: true });
    }

    /* ── Check if section is visible in viewport ── */
    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const windowWidth = window.innerWidth;

        // Element must have dimensions
        if (rect.width === 0 || rect.height === 0) return false;

        // Check if any part is visible
        const visible = (
            rect.top < windowHeight &&
            rect.bottom > 0 &&
            rect.left < windowWidth &&
            rect.right > 0
        );

        return visible;
    }

    /* ── Calculate how much of section is visible (0-1) ── */
    function getVisibilityRatio(el) {
        const rect = el.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        
        // If element is not in viewport at all
        if (rect.bottom < 0 || rect.top > windowHeight) return 0;
        
        // Calculate visible height
        const visibleTop = Math.max(0, rect.top);
        const visibleBottom = Math.min(windowHeight, rect.bottom);
        const visibleHeight = visibleBottom - visibleTop;
        
        // Ratio of visible height to element height
        const ratio = visibleHeight / rect.height;
        
        // Smooth curve - quick fade in/out
        return Math.min(1, Math.max(0, ratio));
    }

    /* ── Check section visibility and control music ── */
    function checkSectionVisibility(section) {
        if (!awardAudio) return;

        const isVisible = isElementInViewport(section);
        const visibilityRatio = getVisibilityRatio(section);
        
        // Calculate target volume based on visibility
        targetVolume = visibilityRatio * MAX_VOLUME;

        // Handle play/pause based on visibility
        if (isVisible && !isPlaying) {
            // Section became visible - start playing
            startMusic();
        } else if (!isVisible && isPlaying) {
            // Section left viewport - stop music
            stopMusic();
        }

        lastSectionVisible = isVisible;
    }

    /* ── Start playing music ── */
    function startMusic() {
        if (!awardAudio || isPlaying) return;

        // Reset to beginning
        awardAudio.currentTime = 0;
        
        // Start playing
        awardAudio.play()
            .then(() => {
                isPlaying = true;
                musicActive = true;
                targetVolume = MAX_VOLUME; // Fade in to max
                console.log('Award music started');
            })
            .catch(error => {
                console.log('Audio playback needs user interaction');
                isPlaying = false;
            });
    }

    /* ── Stop playing music ── */
    function stopMusic() {
        if (!awardAudio || !isPlaying) return;
        
        // Fade out by setting target to 0
        targetVolume = 0;
        musicActive = false;
        
        // Audio will pause when volume reaches 0 in fade loop
    }

    /* ── Continuous fade loop for smooth volume changes ── */
    function startFadeLoop() {
        if (fadeInterval) return;
        
        fadeInterval = setInterval(() => {
            if (!awardAudio) return;
            
            // Smoothly adjust volume toward target
            if (Math.abs(awardAudio.volume - targetVolume) > 0.001) {
                if (awardAudio.volume < targetVolume) {
                    awardAudio.volume = Math.min(targetVolume, awardAudio.volume + FADE_SPEED);
                } else {
                    awardAudio.volume = Math.max(targetVolume, awardAudio.volume - FADE_SPEED);
                }
            }
            
            // If volume reached 0 and we're not active, pause the audio
            if (awardAudio.volume === 0 && !musicActive && isPlaying) {
                awardAudio.pause();
                isPlaying = false;
                console.log('Award music paused');
            }
        }, 50); // 20fps for smooth fades
    }

    /* ── Public API ── */
    window.AwardMusic = {
        play: function() {
            const section = document.querySelector('.sv-awards-section, #svAwardsSection, .awards-section');
            if (section) {
                startMusic();
            }
        },
        
        pause: function() {
            stopMusic();
        },
        
        setVolume: function(vol) {
            targetVolume = Math.min(MAX_VOLUME, Math.max(0, vol));
        },
        
        isPlaying: function() {
            return isPlaying;
        }
    };

    /* ── Initialize when DOM is ready ── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAwardMusic);
    } else {
        initAwardMusic();
    }

})();