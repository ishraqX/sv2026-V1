/**
 * SOUND VISION - FEATURES SECTION
 * Professional Animations & Auto-Hover Effects
 */

(function() {
    'use strict';

    // ========================================
    // CONFIGURATION
    // ========================================
    const CONFIG = {
        observerThreshold: 0.15,
        autoHoverInterval: 3000, // 3 seconds
        autoHoverDuration: 2000  // 2 seconds
    };

    let autoHoverTimer = null;
    let currentAutoHoverIndex = 0;

    // ========================================
    // SCROLL REVEAL ANIMATION
    // ========================================
    const initScrollReveal = () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.dataset.delay || 0;
                    
                    setTimeout(() => {
                        entry.target.classList.add('animated');
                    }, delay * 1000);
                    
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: CONFIG.observerThreshold,
            rootMargin: '0px 0px -50px 0px'
        });

        document.querySelectorAll('[data-animate]').forEach(el => {
            observer.observe(el);
        });
    };

    // ========================================
    // AUTO-HOVER ANIMATION SYSTEM
    // ========================================
    const initAutoHover = () => {
        const features = document.querySelectorAll('.sv-feature-item');
        
        if (features.length === 0) return;

        const activateAutoHover = (index) => {
            // Remove auto-hover from all items
            features.forEach(item => {
                item.classList.remove('auto-hover');
            });

            // Add auto-hover to current item
            if (features[index]) {
                features[index].classList.add('auto-hover');
                
                // Remove after duration
                setTimeout(() => {
                    features[index].classList.remove('auto-hover');
                }, CONFIG.autoHoverDuration);
            }

            // Move to next item
            currentAutoHoverIndex = (index + 1) % features.length;
        };

        // Start auto-hover cycle
        const startAutoHoverCycle = () => {
            activateAutoHover(currentAutoHoverIndex);
            
            autoHoverTimer = setInterval(() => {
                activateAutoHover(currentAutoHoverIndex);
            }, CONFIG.autoHoverInterval);
        };

        // Pause on user interaction
        features.forEach(feature => {
            feature.addEventListener('mouseenter', () => {
                clearInterval(autoHoverTimer);
                features.forEach(item => item.classList.remove('auto-hover'));
            });

            feature.addEventListener('mouseleave', () => {
                setTimeout(() => {
                    startAutoHoverCycle();
                }, 1000);
            });
        });

        // Start the cycle after page load
        setTimeout(() => {
            startAutoHoverCycle();
        }, 2000);
    };

    // ========================================
    // PARALLAX CENTER IMAGE
    // ========================================
    const initParallax = () => {
        const centerImage = document.querySelector('.sv-center-image');
        
        if (!centerImage || window.innerWidth < 768) return;

        let ticking = false;

        const updateParallax = (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;

            centerImage.style.transform = `translate(${x}px, ${y}px)`;
            
            ticking = false;
        };

        document.addEventListener('mousemove', (e) => {
            if (!ticking) {
                window.requestAnimationFrame(() => updateParallax(e));
                ticking = true;
            }
        });
    };

    // ========================================
    // DYNAMIC COLOR ASSIGNMENT
    // ========================================
    const assignColors = () => {
        document.querySelectorAll('.sv-feature-item').forEach(item => {
            const color = getComputedStyle(item).getPropertyValue('--feature-color').trim();
            
            if (color) {
                const rgb = hexToRgb(color);
                if (rgb) {
                    item.style.setProperty('--feature-color-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
                }
            }
        });
    };

    const hexToRgb = (hex) => {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    };

    // ========================================
    // ACCESSIBILITY ENHANCEMENTS
    // ========================================
    const enhanceAccessibility = () => {
        const features = document.querySelectorAll('.sv-feature-item');
        
        features.forEach(feature => {
            feature.setAttribute('tabindex', '0');
            feature.setAttribute('role', 'article');
            
            feature.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    feature.classList.add('auto-hover');
                    
                    setTimeout(() => {
                        feature.classList.remove('auto-hover');
                    }, CONFIG.autoHoverDuration);
                }
            });

            feature.addEventListener('focus', () => {
                clearInterval(autoHoverTimer);
            });

            feature.addEventListener('blur', () => {
                setTimeout(() => {
                    if (!document.querySelector('.sv-feature-item:focus')) {
                        initAutoHover();
                    }
                }, 100);
            });
        });
    };

    // ========================================
    // PERFORMANCE OPTIMIZATION
    // ========================================
    const optimizePerformance = () => {
        // Pause animations when tab is hidden
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(autoHoverTimer);
                document.querySelectorAll('.sv-bg-orb, .sv-image-container').forEach(el => {
                    el.style.animationPlayState = 'paused';
                });
            } else {
                initAutoHover();
                document.querySelectorAll('.sv-bg-orb, .sv-image-container').forEach(el => {
                    el.style.animationPlayState = 'running';
                });
            }
        });

        // Reduce animations on low-end devices
        if (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4) {
            document.body.classList.add('reduce-motion');
        }
    };

    // ========================================
    // SMOOTH SCROLL (if needed)
    // ========================================
    const initSmoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    };

    // ========================================
    // INTERSECTION OBSERVER FOR CENTER IMAGE
    // ========================================
    const observeCenterImage = () => {
        const centerImage = document.querySelector('.sv-center-image');
        
        if (!centerImage) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    centerImage.classList.add('in-view');
                } else {
                    centerImage.classList.remove('in-view');
                }
            });
        }, {
            threshold: 0.3
        });

        observer.observe(centerImage);
    };

    // ========================================
    // INITIALIZE ALL FEATURES
    // ========================================
    const init = () => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }

        console.log('🎨 Sound Vision Features: Initializing...');

        // Core features
        initScrollReveal();
        assignColors();
        
        // Enhanced features
        setTimeout(() => {
            initAutoHover();
        }, 1000);
        
        initParallax();
        enhanceAccessibility();
        optimizePerformance();
        initSmoothScroll();
        observeCenterImage();

        console.log('✅ Sound Vision Features: Ready');
    };

    // Start initialization
    init();

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        clearInterval(autoHoverTimer);
    });

})();