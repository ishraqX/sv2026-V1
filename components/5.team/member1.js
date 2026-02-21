/* ========================================
   SOUND VISION - MEMBER PROFILE INTERACTIONS
   Smooth Animations & Scroll Effects
======================================== */

class MemberProfile {
    constructor() {
        this.scrollTopBtn = document.getElementById('scrollTop');
        this.init();
    }
    
    init() {
        // Animate profile card on load
        this.animateOnLoad();
        
        // Setup scroll to top functionality
        this.setupScrollToTop();
        
        // Setup intersection observers
        this.setupIntersectionObserver();
        
        // Add smooth scroll behavior
        this.setupSmoothScroll();
        
        // Animate timeline items on scroll
        this.animateTimelineItems();
        
        // Add hover effects to achievement cards
        this.setupAchievementEffects();
    }
    
    animateOnLoad() {
        // Animate profile card
        setTimeout(() => {
            const profileCard = document.querySelector('.sv-profile-card');
            if (profileCard) {
                profileCard.classList.add('animated');
            }
        }, 200);
    }
    
    setupScrollToTop() {
        // Show/hide scroll to top button
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                this.scrollTopBtn.classList.add('visible');
            } else {
                this.scrollTopBtn.classList.remove('visible');
            }
        });
        
        // Smooth scroll to top
        this.scrollTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        // Observe all sections
        document.querySelectorAll('.sv-info-section').forEach(section => {
            observer.observe(section);
        });
    }
    
    setupSmoothScroll() {
        // Smooth scroll for all anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#' || targetId === '#top') {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } else {
                    const target = document.querySelector(targetId);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }
    
    animateTimelineItems() {
        const timelineItems = document.querySelectorAll('.sv-timeline-item');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateX(0)';
                    }, index * 100);
                }
            });
        }, {
            threshold: 0.2
        });
        
        timelineItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'all 0.6s cubic-bezier(0.22, 1, 0.36, 1)';
            observer.observe(item);
        });
    }
    
    setupAchievementEffects() {
        const achievementCards = document.querySelectorAll('.sv-achievement-card');
        
        achievementCards.forEach((card, index) => {
            // Stagger animation on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100);
                    }
                });
            }, {
                threshold: 0.2
            });
            
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s cubic-bezier(0.22, 1, 0.36, 1)';
            observer.observe(card);
        });
    }
}

// Skill tag interactions
document.addEventListener('DOMContentLoaded', () => {
    const skillTags = document.querySelectorAll('.sv-skill-tag');
    
    skillTags.forEach((tag, index) => {
        // Stagger animation
        setTimeout(() => {
            tag.style.opacity = '1';
            tag.style.transform = 'scale(1)';
        }, index * 50);
        
        // Initial state
        tag.style.opacity = '0';
        tag.style.transform = 'scale(0.9)';
        tag.style.transition = 'all 0.3s cubic-bezier(0.22, 1, 0.36, 1)';
    });
});

// Social card animations
document.addEventListener('DOMContentLoaded', () => {
    const socialCards = document.querySelectorAll('.sv-social-card');
    
    socialCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100 + 300);
        
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        card.style.transition = 'all 0.4s cubic-bezier(0.22, 1, 0.36, 1)';
    });
});

// Stat box animations
document.addEventListener('DOMContentLoaded', () => {
    const statBoxes = document.querySelectorAll('.sv-stat-box');
    
    statBoxes.forEach((box, index) => {
        setTimeout(() => {
            box.style.opacity = '1';
            box.style.transform = 'translateY(0)';
        }, index * 150 + 400);
        
        box.style.opacity = '0';
        box.style.transform = 'translateY(10px)';
        box.style.transition = 'all 0.5s cubic-bezier(0.22, 1, 0.36, 1)';
    });
});

// Add parallax effect to background elements
window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;
    const bgGradient = document.querySelector('.sv-bg-gradient');
    const spotlight = document.querySelector('.sv-spotlight');
    
    if (bgGradient) {
        bgGradient.style.transform = `translate(0, ${scrollY * 0.3}px)`;
    }
    
    if (spotlight) {
        spotlight.style.transform = `translate(0, ${-scrollY * 0.2}px)`;
    }
});

// Initialize profile
document.addEventListener('DOMContentLoaded', () => {
    new MemberProfile();
});

// Add ripple effect to CTA button
document.addEventListener('DOMContentLoaded', () => {
    const ctaButton = document.querySelector('.sv-cta-button');
    
    if (ctaButton) {
        ctaButton.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            // Add ripple styles
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(0, 0, 0, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple-effect 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
        
        // Add keyframe animation
        if (!document.querySelector('#ripple-animation')) {
            const style = document.createElement('style');
            style.id = 'ripple-animation';
            style.textContent = `
                @keyframes ripple-effect {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }
});

// Add loading animation for profile image
document.addEventListener('DOMContentLoaded', () => {
    const profileImage = document.querySelector('.sv-profile-image');
    
    if (profileImage) {
        profileImage.style.opacity = '0';
        profileImage.style.transform = 'scale(0.95)';
        
        profileImage.addEventListener('load', () => {
            setTimeout(() => {
                profileImage.style.transition = 'all 0.8s cubic-bezier(0.22, 1, 0.36, 1)';
                profileImage.style.opacity = '1';
                profileImage.style.transform = 'scale(1)';
            }, 100);
        });
        
        // Trigger if already loaded
        if (profileImage.complete) {
            profileImage.dispatchEvent(new Event('load'));
        }
    }
});

// Console message
console.log('%c Sound Vision - Team Member Profile ', 'background: #fbbf24; color: #000; font-weight: bold; padding: 5px 10px; border-radius: 5px;');
console.log('%c Designed with precision and passion ', 'color: #9ca3af; font-style: italic;');
