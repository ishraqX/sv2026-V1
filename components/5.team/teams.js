/* ========================================
   SOUND VISION - TEAM CAROUSEL
   Continuous Smooth Sliding with Hover Pause & Drag Support
======================================== */

class TeamCarousel {
    constructor() {
        this.track = document.getElementById('teamCarousel');
        this.cards = Array.from(document.querySelectorAll('.sv-team-card:not(.sv-ceo-card)'));
        this.currentSlide = document.querySelector('.sv-current-slide');
        this.statusDot = document.querySelector('.sv-status-dot');
        
        this.currentIndex = 0;
        this.totalCards = this.cards.length;
        this.cardsToShow = this.getCardsToShow();
        this.isAnimating = false;
        this.isPaused = false;
        this.autoPlayInterval = null;
        this.autoPlayDelay = 3000; // 3 seconds between slides
        
        // Drag/Swipe properties
        this.isDragging = false;
        this.startPos = 0;
        this.currentTranslate = 0;
        this.prevTranslate = 0;
        this.animationID = 0;
        this.startTime = 0;
        this.dragThreshold = 50; // Minimum drag distance to trigger slide
        
        this.init();
    }
    
    init() {
        // Clone cards for infinite loop
        this.cloneCards();
        
        // Set initial position
        this.updateCarousel(false);
        
        // Start autoplay
        this.startAutoPlay();
        
        // Add event listeners
        this.addEventListeners();
        
        // Add drag/swipe listeners
        this.addDragListeners();
        
        // Handle resize
        window.addEventListener('resize', () => this.handleResize());
        
        // Animate on load
        this.animateOnScroll();
    }
    
    getCardsToShow() {
        const width = window.innerWidth;
        if (width >= 1200) return 3; // Show 3 cards on desktop
        if (width >= 769) return 2;   // Show 2 cards on tablet
        return 1;                      // Show 1 card on mobile
    }
    
    cloneCards() {
        // Clone enough cards to create seamless loop
        const clonesToAdd = this.cardsToShow * 2;
        
        // Clone cards at the beginning
        for (let i = this.totalCards - 1; i >= this.totalCards - clonesToAdd; i--) {
            const clone = this.cards[i].cloneNode(true);
            clone.classList.add('clone');
            this.track.insertBefore(clone, this.track.firstChild);
        }
        
        // Clone cards at the end
        for (let i = 0; i < clonesToAdd; i++) {
            const clone = this.cards[i].cloneNode(true);
            clone.classList.add('clone');
            this.track.appendChild(clone);
        }
        
        // Update cards array to include clones
        this.allCards = Array.from(this.track.querySelectorAll('.sv-team-card'));
        
        // Set initial index to account for prepended clones
        this.currentIndex = clonesToAdd;
    }
    
    getPositionX(event) {
        return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
    }
    
    updateCarousel(animate = true) {
        const cardWidth = this.allCards[0].offsetWidth;
        const gap = parseFloat(getComputedStyle(this.track).gap) || 0;
        const offset = -(this.currentIndex * (cardWidth + gap));
        
        if (animate) {
            this.track.style.transition = 'transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        } else {
            this.track.style.transition = 'none';
        }
        
        this.track.style.transform = `translateX(${offset}px)`;
        this.prevTranslate = offset;
        this.currentTranslate = offset;
        
        // Update counter (showing actual card number, not including clones)
        const actualIndex = ((this.currentIndex - this.cardsToShow * 2) % this.totalCards + this.totalCards) % this.totalCards;
        this.currentSlide.textContent = actualIndex + 1;
    }
    
    next() {
        if (this.isAnimating) return;
        
        this.isAnimating = true;
        this.currentIndex++;
        this.updateCarousel(true);
        
        // Check if we need to reset position (infinite loop)
        setTimeout(() => {
            const clonesToAdd = this.cardsToShow * 2;
            if (this.currentIndex >= this.totalCards + clonesToAdd) {
                this.currentIndex = clonesToAdd;
                this.updateCarousel(false);
            }
            this.isAnimating = false;
        }, 800);
    }
    
    prev() {
        if (this.isAnimating) return;
        
        this.isAnimating = true;
        this.currentIndex--;
        this.updateCarousel(true);
        
        // Check if we need to reset position (infinite loop)
        setTimeout(() => {
            const clonesToAdd = this.cardsToShow * 2;
            if (this.currentIndex < clonesToAdd) {
                this.currentIndex = this.totalCards + clonesToAdd - 1;
                this.updateCarousel(false);
            }
            this.isAnimating = false;
        }, 800);
    }
    
    addDragListeners() {
        const carousel = document.querySelector('.sv-carousel-wrapper');
        
        // Mouse events
        carousel.addEventListener('mousedown', this.dragStart.bind(this));
        carousel.addEventListener('mousemove', this.drag.bind(this));
        carousel.addEventListener('mouseup', this.dragEnd.bind(this));
        carousel.addEventListener('mouseleave', this.dragEnd.bind(this));
        
        // Touch events
        carousel.addEventListener('touchstart', this.dragStart.bind(this), { passive: true });
        carousel.addEventListener('touchmove', this.drag.bind(this), { passive: true });
        carousel.addEventListener('touchend', this.dragEnd.bind(this));
        
        // Prevent context menu on long press
        carousel.addEventListener('contextmenu', (e) => {
            if (this.isDragging) {
                e.preventDefault();
            }
        });
        
        // Add cursor style
        carousel.style.cursor = 'grab';
    }
    
    dragStart(event) {
        this.isDragging = true;
        this.startPos = this.getPositionX(event);
        this.startTime = Date.now();
        this.animationID = requestAnimationFrame(this.animation.bind(this));
        
        this.track.style.cursor = 'grabbing';
        document.querySelector('.sv-carousel-wrapper').style.cursor = 'grabbing';
        
        // Pause autoplay during drag
        this.pauseAutoPlay();
    }
    
    drag(event) {
        if (!this.isDragging) return;
        
        const currentPosition = this.getPositionX(event);
        this.currentTranslate = this.prevTranslate + currentPosition - this.startPos;
    }
    
    dragEnd(event) {
        if (!this.isDragging) return;
        
        this.isDragging = false;
        cancelAnimationFrame(this.animationID);
        
        const movedBy = this.currentTranslate - this.prevTranslate;
        const dragDuration = Date.now() - this.startTime;
        const velocity = Math.abs(movedBy / dragDuration);
        
        // Check if drag was significant enough
        if (Math.abs(movedBy) > this.dragThreshold || velocity > 0.3) {
            if (movedBy < 0) {
                // Dragged left - go to next
                this.next();
            } else {
                // Dragged right - go to previous
                this.prev();
            }
        } else {
            // Snap back to current position
            this.updateCarousel(true);
        }
        
        this.track.style.cursor = 'grab';
        document.querySelector('.sv-carousel-wrapper').style.cursor = 'grab';
        
        // Resume autoplay after a delay
        setTimeout(() => {
            this.resumeAutoPlay();
        }, 500);
    }
    
    animation() {
        if (this.isDragging) {
            this.track.style.transition = 'none';
            this.track.style.transform = `translateX(${this.currentTranslate}px)`;
            requestAnimationFrame(this.animation.bind(this));
        }
    }
    
    startAutoPlay() {
        this.stopAutoPlay();
        this.autoPlayInterval = setInterval(() => {
            if (!this.isPaused && !this.isDragging) {
                this.next();
            }
        }, this.autoPlayDelay);
        
        this.statusDot.classList.add('active');
    }
    
    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
        this.statusDot.classList.remove('active');
    }
    
    pauseAutoPlay() {
        this.isPaused = true;
        this.statusDot.classList.remove('active');
    }
    
    resumeAutoPlay() {
        this.isPaused = false;
        this.statusDot.classList.add('active');
    }
    
    addEventListeners() {
        // Pause on hover for each card
        this.allCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                if (!this.isDragging) {
                    this.pauseAutoPlay();
                    card.classList.add('paused');
                }
            });
            
            card.addEventListener('mouseleave', () => {
                if (!this.isDragging) {
                    this.resumeAutoPlay();
                    card.classList.remove('paused');
                }
            });
        });
        
        // Pause when hovering over social links to prevent accidental clicks
        const socialLinks = document.querySelectorAll('.sv-social-icon');
        socialLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });
        
        // Page visibility - pause when tab is hidden
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseAutoPlay();
            } else if (!this.isDragging) {
                this.resumeAutoPlay();
            }
        });
    }
    
    handleResize() {
        const newCardsToShow = this.getCardsToShow();
        
        if (newCardsToShow !== this.cardsToShow) {
            this.cardsToShow = newCardsToShow;
            
            // Remove old clones
            const clones = this.track.querySelectorAll('.clone');
            clones.forEach(clone => clone.remove());
            
            // Re-initialize
            this.cards = Array.from(document.querySelectorAll('.sv-team-card:not(.clone)'));
            this.currentIndex = 0;
            this.cloneCards();
            this.updateCarousel(false);
        } else {
            this.updateCarousel(false);
        }
    }
    
    animateOnScroll() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, {
            threshold: 0.1
        });
        
        document.querySelectorAll('[data-animate]').forEach(el => {
            observer.observe(el);
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new TeamCarousel();
    
    // Add loaded class to section for fade-in
    setTimeout(() => {
        document.querySelector('.sv-team-section').classList.add('loaded');
    }, 100);
});

// Prevent card links from opening when clicking social icons or dragging
document.addEventListener('click', (e) => {
    if (e.target.closest('.sv-social-icon')) {
        e.stopPropagation();
    }
}, true);

// Prevent text selection during drag
document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('.sv-carousel-wrapper');
    if (carousel) {
        carousel.addEventListener('selectstart', (e) => {
            e.preventDefault();
        });
    }
});