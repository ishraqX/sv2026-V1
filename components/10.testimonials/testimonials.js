/* ===== TESTIMONIALS.JS - Professional Carousel Functionality ===== */

class TestimonialsCarousel {
    constructor() {
        this.currentIndex = 0;
        this.autoPlayInterval = null;
        this.autoPlayDelay = 6000; // 6 seconds

        this.slider = document.querySelector('.testimonials-slider');
        this.cards = document.querySelectorAll('.testimonial-card');
        this.prevBtn = document.querySelector('.testimonial-prev');
        this.nextBtn = document.querySelector('.testimonial-next');
        this.wrapper = document.querySelector('.testimonials-slider-wrapper');

        if (!this.slider || this.cards.length === 0) return;

        this.init();
    }

    init() {
        // Setup event listeners
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.prevTestimonial());
        }
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.nextTestimonial());
        }

        // Show first testimonial
        this.showTestimonial(0);

        // Setup pause on hover
        if (this.wrapper) {
            this.wrapper.addEventListener('mouseenter', () => this.pauseAutoPlay());
            this.wrapper.addEventListener('mouseleave', () => this.startAutoPlay());
        }

        // Start auto-play
        this.startAutoPlay();
    }

    showTestimonial(index) {
        // Remove active class from all cards
        this.cards.forEach((card) => {
            card.classList.remove('active');
        });

        // Ensure index is within bounds
        if (index >= this.cards.length) {
            this.currentIndex = 0;
        } else if (index < 0) {
            this.currentIndex = this.cards.length - 1;
        } else {
            this.currentIndex = index;
        }

        // Add active class to current card
        this.cards[this.currentIndex].classList.add('active');
    }

    nextTestimonial() {
        this.showTestimonial(this.currentIndex + 1);
        this.resetAutoPlay();
    }

    prevTestimonial() {
        this.showTestimonial(this.currentIndex - 1);
        this.resetAutoPlay();
    }

    startAutoPlay() {
        if (this.autoPlayInterval) return;
        this.autoPlayInterval = setInterval(() => {
            this.showTestimonial(this.currentIndex + 1);
        }, this.autoPlayDelay);
    }

    pauseAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }

    resetAutoPlay() {
        this.pauseAutoPlay();
        this.startAutoPlay();
    }
}

// Initialize carousel when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new TestimonialsCarousel();
});
