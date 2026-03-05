// ===== MOBILE NAVIGATION =====
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');

if (navToggle) {
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        
        // Animate hamburger
        const spans = navToggle.querySelectorAll('span');
        if (navMenu.classList.contains('active')) {
            spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
            spans[1].style.opacity = '0';
            spans[2].style.transform = 'rotate(-45deg) translate(7px, -7px)';
        } else {
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        }
    });
    
    // Close menu when clicking on links
    const navLinks = navMenu.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            const spans = navToggle.querySelectorAll('span');
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        });
    });
}

// ===== HERO SLIDER =====
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const indicators = document.querySelectorAll('.indicator');
const heroNext = document.querySelector('.hero-next');
const heroPrev = document.querySelector('.hero-prev');

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        indicators[i].classList.remove('active');
        
        if (i === index) {
            slide.classList.add('active');
            indicators[i].classList.add('active');
        }
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
}

// Auto slide
let autoSlideInterval = setInterval(nextSlide, 5000);

// Manual controls
if (heroNext) {
    heroNext.addEventListener('click', () => {
        nextSlide();
        clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(nextSlide, 5000);
    });
}

if (heroPrev) {
    heroPrev.addEventListener('click', () => {
        prevSlide();
        clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(nextSlide, 5000);
    });
}

// Indicator controls
indicators.forEach((indicator, index) => {
    indicator.addEventListener('click', () => {
        currentSlide = index;
        showSlide(currentSlide);
        clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(nextSlide, 5000);
    });
});

// ===== TESTIMONIALS SLIDER =====
let currentTestimonial = 0;
const testimonialCards = document.querySelectorAll('.testimonial-card');
const testimonialNext = document.querySelector('.testimonial-next');
const testimonialPrev = document.querySelector('.testimonial-prev');

function showTestimonial(index) {
    testimonialCards.forEach((card, i) => {
        card.style.display = i === index ? 'block' : 'none';
    });
}

function nextTestimonial() {
    currentTestimonial = (currentTestimonial + 1) % testimonialCards.length;
    showTestimonial(currentTestimonial);
}

function prevTestimonial() {
    currentTestimonial = (currentTestimonial - 1 + testimonialCards.length) % testimonialCards.length;
    showTestimonial(currentTestimonial);
}

// Initialize testimonials
if (testimonialCards.length > 0) {
    showTestimonial(0);
}

if (testimonialNext) {
    testimonialNext.addEventListener('click', nextTestimonial);
}

if (testimonialPrev) {
    testimonialPrev.addEventListener('click', prevTestimonial);
}

// Auto rotate testimonials
let testimonialInterval = setInterval(nextTestimonial, 7000);

// Pause on hover
const testimonialsSlider = document.querySelector('.testimonials-slider');
if (testimonialsSlider) {
    testimonialsSlider.addEventListener('mouseenter', () => {
        clearInterval(testimonialInterval);
    });
    
    testimonialsSlider.addEventListener('mouseleave', () => {
        testimonialInterval = setInterval(nextTestimonial, 7000);
    });
}

// ===== SCROLL ANIMATIONS =====
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for scroll animations
const animateElements = document.querySelectorAll('.feature-card, .sdg-card, .award-card, .value-item');
animateElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// ===== BACK TO TOP BUTTON =====
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        backToTop.classList.add('visible');
    } else {
        backToTop.classList.remove('visible');
    }
});

if (backToTop) {
    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===== NAVBAR SCROLL EFFECT =====
const navbar = document.querySelector('.navbar');
let lastScroll = 0;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
        navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
        navbar.style.padding = '0.5rem 0';
    } else {
        navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
        navbar.style.padding = '1rem 0';
    }
    
    lastScroll = currentScroll;
});

// ===== COUNTER ANIMATION =====
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 20);
}

const counterElement = document.getElementById('visitorCount');
if (counterElement) {
    const targetCount = parseInt(counterElement.textContent.replace(/,/g, ''));
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                counterElement.textContent = '0';
                animateCounter(counterElement, targetCount);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    counterObserver.observe(counterElement);
}

// ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const offsetTop = target.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        }
    });
});

// ===== LAZY LOADING IMAGES =====
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}

// ===== FORM VALIDATION (if contact form exists) =====
const forms = document.querySelectorAll('form');
forms.forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Add your form validation logic here
        const formData = new FormData(form);
        
        // Example: Send data via fetch
        // fetch('/submit-form.php', {
        //     method: 'POST',
        //     body: formData
        // })
        // .then(response => response.json())
        // .then(data => console.log(data))
        // .catch(error => console.error('Error:', error));
        
        alert('Form submitted! (Add your own submission logic)');
    });
});

// ===== CONSOLE MESSAGE =====
console.log('%cSound Vision', 'color: #2563eb; font-size: 24px; font-weight: bold;');
console.log('%cWebsite by Sound Vision Team', 'color: #7c3aed; font-size: 14px;');
console.log('%cTransforming lives through innovation', 'color: #6b7280; font-size: 12px;');

// ===== PERFORMANCE MONITORING =====
window.addEventListener('load', () => {
    const loadTime = performance.now();
    console.log(`Page loaded in ${loadTime.toFixed(2)}ms`);
});
/**
 * team.js  ·  Sound Vision — Team Section  v5
 * ─────────────────────────────────────────────
 * RAF-based infinite scroll — zero CSS animation, zero end-jump.
 * All globals prefixed svTm to avoid collision with award.js
 *
 * v5 additions:
 *   • Left / Right arrow buttons — click to nudge by one card step
 *   • Touch / mouse drag on the track — swipe to scroll manually
 *   • Both integrate cleanly with the existing RAF loop:
 *       – Manual input writes directly to `offset` (same variable RAF uses)
 *       – Auto-scroll pauses during interaction, resumes after idle
 *       – No second animation system — one RAF loop rules everything
 */
(function () {
    'use strict';

    /* ── Auto-scroll speed (px/sec) ── */
    var SVT_PPS = 55;

    /* ── How long (ms) after last manual input before auto resumes ── */
    var SVT_RESUME_DELAY = 1800;

    document.addEventListener('DOMContentLoaded', svTmInit);

    function svTmInit() {
        svTmParticles();
        svTmReveal();
        svTmScroll();
    }

    /* ════════════════════════════════════════════
       FLOATING PARTICLES  (unchanged)
    ════════════════════════════════════════════ */
    function svTmParticles() {
        var wrap = document.getElementById('svTmParticles');
        if (!wrap) return;

        var n    = window.innerWidth < 768 ? 20 : 38;
        var frag = document.createDocumentFragment();
        var cols = ['#00C9AE', '#4488FF', '#00C9AE', '#D4AF37'];

        for (var i = 0; i < n; i++) {
            var el  = document.createElement('span');
            el.className = 'sv-tm-ptcl';

            var sz  = (Math.random() * 3 + 1).toFixed(2);
            var lft = (Math.random() * 100).toFixed(2);
            var dur = (Math.random() * 14 + 10).toFixed(2);
            var del = (Math.random() * 18).toFixed(2);
            var dx  = ((Math.random() - 0.5) * 130).toFixed(1);
            var clr = cols[Math.floor(Math.random() * cols.length)];

            el.style.cssText = [
                'width:'              + sz  + 'px',
                'height:'             + sz  + 'px',
                'left:'               + lft + '%',
                'bottom:0',
                'background:'         + clr,
                'animation-duration:' + dur + 's',
                'animation-delay:'    + del + 's',
            ].join(';');
            el.style.setProperty('--dx', dx + 'px');

            frag.appendChild(el);
        }
        wrap.appendChild(frag);
    }

    /* ════════════════════════════════════════════
       SCROLL REVEAL  (unchanged)
    ════════════════════════════════════════════ */
    function svTmReveal() {
        var els = document.querySelectorAll('[data-tm-reveal]');
        if (!els.length) return;

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e, i) {
                if (!e.isIntersecting) return;
                setTimeout(function () {
                    e.target.classList.add('sv-tm-in');
                }, i * 170);
                io.unobserve(e.target);
            });
        }, { threshold: 0.1 });

        els.forEach(function (el) { io.observe(el); });
    }

    /* ════════════════════════════════════════════
       INFINITE RAF SCROLL  +  MANUAL CONTROLS
       ─────────────────────────────────────────
       One shared `offset` variable. Every source
       (RAF auto-scroll, arrow click, drag/swipe)
       writes to the same `offset`. The tick loop
       reads it and renders. Clean, no fighting.

       Arrow buttons are injected by JS so the PHP
       doesn't need changing. They appear outside
       the scroll track, flanking the outer wrapper.
    ════════════════════════════════════════════ */
    function svTmScroll() {
        var outer = document.getElementById('svTmOuter');
        var track = document.getElementById('svTmTrack');
        if (!outer || !track) return;

        /* ── Clone cards → infinite loop ── */
        var origCards = Array.from(track.querySelectorAll('.sv-tm-card'));
        if (!origCards.length) return;

        origCards.forEach(function (card) {
            var clone = card.cloneNode(true);
            clone.setAttribute('aria-hidden', 'true');
            track.appendChild(clone);
        });

        /* ── Core state ── */
        var setW         = 0;
        var offset       = 0;
        var currentSpeed = SVT_PPS;
        var targetSpeed  = SVT_PPS;
        var lastTs       = null;

        /* ── Manual-control state ── */
        var manualPaused = false;   /* true while user is interacting     */
        var resumeTimer  = null;    /* setTimeout handle for auto-resume  */

        /* nudge animation — smooth glide to target after arrow click */
        var nudgeTarget  = null;    /* destination offset (px), or null   */
        var nudgeSpeed   = 0;       /* current nudge velocity (px/frame)  */
        var nudgeActive  = false;   /* prevent conflicting nudges         */

        /* drag state */
        var dragActive   = false;
        var dragStartX   = 0;
        var dragLastX    = 0;
        var dragVelocity = 0;       /* exponential moving average         */

        /* ── Measure one set width ── */
        function measure() {
            var gap = parseFloat(getComputedStyle(track).gap) || 24;
            var w   = 0;
            origCards.forEach(function (c) { w += c.offsetWidth + gap; });
            setW = w;
        }

        /* ── Wrap offset to [0, setW) ── */
        function wrapOffset(v) {
            if (setW <= 0) return v;
            v = v % setW;
            if (v < 0) v += setW;
            return v;
        }

        /* ── Pause auto-scroll; restart after idle ── */
        function pauseAuto() {
            manualPaused = true;
            targetSpeed  = 0;
            clearTimeout(resumeTimer);
        }

        function scheduleResume() {
            clearTimeout(resumeTimer);
            resumeTimer = setTimeout(function () {
                manualPaused = false;
                /* Only resume if hover isn't still holding it paused */
                if (!hoverPaused && !touchTogglePaused) {
                    targetSpeed = SVT_PPS;
                }
            }, SVT_RESUME_DELAY);
        }

        /* ── Hover pause state ── */
        var hoverPaused       = false;
        var touchTogglePaused = false;

        /* ── Step size: one card width + gap ── */
        function cardStep() {
            var gap = parseFloat(getComputedStyle(track).gap) || 24;
            return (origCards[0] ? origCards[0].offsetWidth + gap : 300);
        }

        /* ══════════════════════════════════════════
           RAF TICK
           Handles auto-scroll + nudge animation.
           Drag writes directly to offset so tick
           just needs to clamp + render.
        ══════════════════════════════════════════ */
        function tick(ts) {
            /* 1. Ease auto-scroll speed */
            currentSpeed += (targetSpeed - currentSpeed) * 0.07;

            if (setW > 0) {
                if (!dragActive) {
                    /* 2a. Nudge animation (arrow click smooth glide) */
                    if (nudgeTarget !== null) {
                        var remaining = nudgeTarget - offset;

                        /* Normalise remaining across the wrap boundary */
                        if (remaining > setW / 2)  remaining -= setW;
                        if (remaining < -setW / 2) remaining += setW;

                        /* Spring toward target — stronger damping to prevent oscillation
                           0.18 accel + 0.88 damping → smooth, stable settle */
                        nudgeSpeed += remaining * 0.18;
                        nudgeSpeed *= 0.88;          /* stronger damping — prevents jitter */
                        offset     += nudgeSpeed;

                        /* Only wrap offset when animation is complete to prevent jumps */
                        if (Math.abs(remaining) < 0.5 && Math.abs(nudgeSpeed) < 0.15) {
                            offset      = wrapOffset(nudgeTarget);
                            nudgeTarget = null;
                            nudgeSpeed  = 0;
                            nudgeActive = false;
                            scheduleResume();         /* resume auto after glide */
                        }

                        track.style.transform = 'translateX(-' + offset.toFixed(2) + 'px)';
                        lastTs = null;                /* don't auto-advance while nudging */

                    /* 2b. Auto-scroll */
                    } else if (currentSpeed > 0.4 || targetSpeed > 0) {
                        if (lastTs !== null) {
                            var delta = Math.min((ts - lastTs) / 1000, 0.05);
                            offset += currentSpeed * delta;
                            if (offset >= setW) offset -= setW;
                            track.style.transform = 'translateX(-' + offset.toFixed(2) + 'px)';
                        }
                        lastTs = ts;
                    } else {
                        lastTs = null;
                    }
                }
                /* drag: offset already updated in pointermove/touchmove */
            }

            requestAnimationFrame(tick);
        }

        /* ══════════════════════════════════════════
           ARROW BUTTONS
           Injected into the DOM around the outer wrapper.
           Each click nudges offset by one card step.
        ══════════════════════════════════════════ */
        function buildArrows() {
            /* Wrap outer in a relative container so arrows can be positioned */
            var col = outer.closest('.sv-tm-scroll-col') || outer.parentElement;
            if (!col) return;

            /* Make the col the positioning parent */
            col.style.position = 'relative';

            function makeBtn(dir) {
                var btn = document.createElement('button');
                btn.className    = 'sv-tm-arrow sv-tm-arrow--' + dir;
                btn.setAttribute('aria-label', dir === 'prev' ? 'Scroll left' : 'Scroll right');
                btn.innerHTML    = dir === 'prev'
                    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>'
                    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';
                return btn;
            }

            var prevBtn = makeBtn('prev');
            var nextBtn = makeBtn('next');

            col.appendChild(prevBtn);
            col.appendChild(nextBtn);

            /* Click handlers — nudge offset by ±cardStep */
            prevBtn.addEventListener('click', function () {
                if (setW <= 0 || nudgeActive) return;  /* prevent overlapping nudges */
                pauseAuto();
                nudgeSpeed  = 0;
                nudgeTarget = wrapOffset(offset - cardStep());
                nudgeActive = true;
            });

            nextBtn.addEventListener('click', function () {
                if (setW <= 0 || nudgeActive) return;  /* prevent overlapping nudges */
                pauseAuto();
                nudgeSpeed  = 0;
                nudgeTarget = wrapOffset(offset + cardStep());
                nudgeActive = true;
            });
        }

        /* ══════════════════════════════════════════
           DRAG / SWIPE  (mouse + touch)
           Pointer events — one handler for both.
           Falls back to individual mouse/touch events
           if PointerEvent not supported.
        ══════════════════════════════════════════ */
        var supportsPointer = (typeof PointerEvent !== 'undefined');

        function onDragStart(clientX) {
            dragActive   = true;
            dragStartX   = clientX;
            dragLastX    = clientX;
            dragVelocity = 0;
            nudgeTarget  = null;   /* cancel any in-progress nudge */
            nudgeSpeed   = 0;
            nudgeActive  = false;  /* reset nudge state */
            pauseAuto();
            outer.classList.add('sv-tm-dragging');
        }

        function onDragMove(clientX) {
            if (!dragActive) return;
            var dx = dragLastX - clientX;          /* positive = scroll right */
            dragVelocity = dragVelocity * 0.8 + dx * 0.2; /* EMA — smoother, less noisy, stronger smoothing */
            dragLastX    = clientX;
            offset       = wrapOffset(offset + dx);
            track.style.transform = 'translateX(-' + offset.toFixed(2) + 'px)';
        }

        function onDragEnd() {
            if (!dragActive) return;
            dragActive = false;
            outer.classList.remove('sv-tm-dragging');

            /* Momentum coast: apply remaining velocity as a nudge target */
            if (Math.abs(dragVelocity) > 1.5 && !nudgeActive) {  /* don't start nudge if one is active */
                var coast = dragVelocity * 10;      /* coast distance in px — reduced for stability */
                nudgeTarget = wrapOffset(offset + coast);
                nudgeSpeed  = dragVelocity * 0.4;   /* reduced momentum for stability */
                nudgeActive = true;
            } else {
                scheduleResume();
            }
        }

        if (supportsPointer) {
            outer.addEventListener('pointerdown', function (e) {
                if (e.button !== 0) return;        /* left button only */
                outer.setPointerCapture(e.pointerId);
                onDragStart(e.clientX);
            });
            outer.addEventListener('pointermove', function (e) {
                if (!dragActive) return;
                e.preventDefault();
                onDragMove(e.clientX);
            }, { passive: false });
            outer.addEventListener('pointerup',     function (e) { onDragEnd(); });
            outer.addEventListener('pointercancel', function (e) { onDragEnd(); });
        } else {
            /* Mouse fallback */
            outer.addEventListener('mousedown', function (e) {
                if (e.button !== 0) return;
                onDragStart(e.clientX);
            });
            window.addEventListener('mousemove', function (e) { onDragMove(e.clientX); });
            window.addEventListener('mouseup',   function ()  { onDragEnd(); });

            /* Touch fallback */
            outer.addEventListener('touchstart', function (e) {
                onDragStart(e.touches[0].clientX);
            }, { passive: true });
            outer.addEventListener('touchmove', function (e) {
                e.preventDefault();
                onDragMove(e.touches[0].clientX);
            }, { passive: false });
            outer.addEventListener('touchend', function () { onDragEnd(); });
        }

        /* ══════════════════════════════════════════
           EXISTING HOVER PAUSE  (kept intact)
        ══════════════════════════════════════════ */
        outer.addEventListener('mouseenter', function () {
            hoverPaused = true;
            targetSpeed = 0;
        });
        outer.addEventListener('mouseleave', function () {
            hoverPaused = false;
            if (!manualPaused && !touchTogglePaused) targetSpeed = SVT_PPS;
        });

        /* ── Visibility API ── */
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible' && !touchTogglePaused && !manualPaused) {
                targetSpeed = SVT_PPS;
                lastTs      = null;
            }
        });

        /* ── Resize ── */
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                measure();
                if (setW > 0 && offset >= setW) offset = offset % setW;
            }, 220);
        });

        /* ── Build arrows then start ── */
        buildArrows();

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                measure();
                requestAnimationFrame(tick);
            });
        });
    }

})();

/**
 * award.js — Sound Vision Awards Carousel
 * FIX 1: Premium smooth transition on button/dot click via CSS cubic-bezier
 * FIX 2: Lightbox populates correctly — class names match sv-aw-lb- CSS
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', init);

    function init() {
        spawnParticles();
        initScrollReveal();
        initCarousel();
        initLightbox();
    }

    /* ════════════════════════════════════════════════════════
       PARTICLES
    ════════════════════════════════════════════════════════ */
    function spawnParticles() {
        const container = document.getElementById('svAwardParticles');
        if (!container) return;
        const count = window.innerWidth < 768 ? 14 : 28;
        for (let i = 0; i < count; i++) {
            const p    = document.createElement('span');
            p.className = 'sv-aw-ptcl';
            const size  = Math.random() * 3 + 1;
            const left  = Math.random() * 100;
            const delay = Math.random() * 12;
            const dur   = Math.random() * 10 + 10;
            const dx    = (Math.random() - 0.5) * 80;
            Object.assign(p.style, {
                width:             size + 'px',
                height:            size + 'px',
                left:              left + '%',
                animationDelay:    delay + 's',
                animationDuration: dur   + 's',
                '--dx':            dx    + 'px',
            });
            container.appendChild(p);
        }
    }

    /* ════════════════════════════════════════════════════════
       SCROLL REVEAL
    ════════════════════════════════════════════════════════ */
    function initScrollReveal() {
        const els = document.querySelectorAll('[data-aw-reveal]');
        if (!els.length) return;
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('sv-aw-in'); obs.unobserve(e.target); }
            });
        }, { threshold: 0.15 });
        els.forEach(el => obs.observe(el));
    }

    /* ════════════════════════════════════════════════════════
       CAROUSEL
    ════════════════════════════════════════════════════════ */
    function initCarousel() {
        const track      = document.getElementById('svAwardsTrack');
        const dotsWrap   = document.getElementById('svAwardsDots');
        const prevBtn    = document.getElementById('svAwardPrev');
        const nextBtn    = document.getElementById('svAwardNext');
        const counterEl  = document.getElementById('svCounterCurrent');

        if (!track) return;

        const cards = Array.from(track.querySelectorAll('.sv-aw-card'));
        const total = cards.length;
        const dots  = dotsWrap ? Array.from(dotsWrap.querySelectorAll('.sv-aw-dot')) : [];

        let current       = 0;
        let isDragging    = false;
        let dragStartX    = 0;
        let dragDeltaX    = 0;
        let lastX         = 0;
        let velocity      = 0;
        let autoplayTimer = null;
        let rafId         = null;

        /* ── Card dimensions ── */
        const CARD_W = () => cards[0]?.offsetWidth || 300;
        const GAP    = () => parseInt(getComputedStyle(track).gap) || 24;
        const STEP   = () => CARD_W() + GAP();

        /* ── Target translateX for a given index (centres card) ── */
        function targetX(idx) {
            const outerW      = track.parentElement?.offsetWidth || window.innerWidth;
            const centerOffset = (outerW - CARD_W()) / 2;
            return centerOffset - idx * STEP();
        }

        /* ── Apply transform with optional instant snap ──
           FIX 1: For button/dot clicks we KEEP the CSS transition
           (transition: transform 0.52s cubic-bezier(0.22,1,0.36,1))
           which gives the premium smooth slide feel.
           During drag we add .sv-aw-dragging which sets transition:none
           so RAF updates are instant (no jank).
        ── */
        function setTransform(tx, instant) {
            if (instant) {
                track.classList.add('sv-aw-dragging');
                track.style.transform = `translateX(${tx}px)`;
                void track.offsetWidth; // force reflow
                track.classList.remove('sv-aw-dragging');
            } else {
                // CSS handles the smooth easing — just set the value
                track.style.transform = `translateX(${tx}px)`;
            }
        }

        /* ── Update active card + dots + counter ── */
        function applyState(idx, instant) {
            idx = Math.max(0, Math.min(total - 1, idx));

            cards.forEach((c, i) => {
                c.classList.toggle('sv-aw-active',    i === idx);
                c.classList.toggle('sv-aw-adjacent',  Math.abs(i - idx) === 1);
            });

            setTransform(targetX(idx), instant);

            dots.forEach((d, i) => d.classList.toggle('sv-aw-dot--active', i === idx));

            if (counterEl) {
                counterEl.textContent  = String(idx + 1).padStart(2, '0');
                counterEl.style.transform = 'scale(1.35)';
                counterEl.style.color     = 'var(--gold-lt)';
                setTimeout(() => {
                    counterEl.style.transform = '';
                    counterEl.style.color     = '';
                }, 260);
            }
        }

        function goTo(idx) {
            current = ((idx % total) + total) % total;
            applyState(current, false); // false = let CSS transition run → smooth
            resetAutoplay();
        }

        /* ── Autoplay ── */
        function startAutoplay() {
            clearInterval(autoplayTimer);
            autoplayTimer = setInterval(() => {
                if (!isDragging) goTo((current + 1) % total);
            }, 4200);
        }
        function stopAutoplay()  { clearInterval(autoplayTimer); }
        function resetAutoplay() { stopAutoplay(); startAutoplay(); }

        /* ── Init ── */
        applyState(0, true);
        startAutoplay();

        /* ── Arrow buttons ── */
        if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1 < 0 ? total - 1 : current - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => goTo((current + 1) % total));

        /* ── Dots ── */
        dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));

        /* ── Card click → open lightbox or navigate ── */
        cards.forEach((card, i) => {
            card.addEventListener('click', () => {
                if (Math.abs(dragDeltaX) > 8) return; // was a drag, not a tap
                if (i === current) openLightbox(card);
                else goTo(i);
            });
            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (i === current) openLightbox(card);
                    else goTo(i);
                }
            });
        });

        /* ── Mouse drag ── */
        track.addEventListener('mousedown', onDragStart);
        window.addEventListener('mousemove', onDragMove);
        window.addEventListener('mouseup',   onDragEnd);

        function onDragStart(e) {
            isDragging = true;
            dragStartX = e.clientX;
            dragDeltaX = 0;
            lastX      = e.clientX;
            velocity   = 0;
            track.classList.add('sv-aw-dragging'); // kills CSS transition → instant RAF
            stopAutoplay();
            cancelAnimationFrame(rafId);
            e.preventDefault();
        }

        function onDragMove(e) {
            if (!isDragging) return;
            dragDeltaX     = e.clientX - dragStartX;
            velocity       = (e.clientX - lastX) * 0.85;
            lastX          = e.clientX;
            const resist   = dragDeltaX * 0.75;
            track.style.transform = `translateX(${targetX(current) + resist}px)`;
        }

        function onDragEnd() {
            if (!isDragging) return;
            isDragging = false;
            track.classList.remove('sv-aw-dragging'); // restore CSS transition

            const SWIPE = 50, VEL = 3;
            if      (dragDeltaX < -SWIPE || velocity < -VEL) goTo(Math.min(total - 1, current + 1));
            else if (dragDeltaX >  SWIPE || velocity >  VEL) goTo(Math.max(0,         current - 1));
            else    goTo(current); // snap back with CSS ease

            // Gentle momentum coast before snapping
            momentumSettle();
        }

        function momentumSettle() {
            if (Math.abs(velocity) < 0.5) return;
            velocity *= 0.88;
            track.style.transform = `translateX(${targetX(current) + velocity * 7}px)`;
            rafId = requestAnimationFrame(momentumSettle);
        }

        /* ── Touch drag ── */
        track.addEventListener('touchstart', onTouchStart, { passive: true });
        track.addEventListener('touchmove',  onTouchMove,  { passive: true });
        track.addEventListener('touchend',   onTouchEnd);

        function onTouchStart(e) {
            const t    = e.touches[0];
            isDragging = true;
            dragStartX = t.clientX;
            dragDeltaX = 0;
            lastX      = t.clientX;
            velocity   = 0;
            track.classList.add('sv-aw-dragging');
            stopAutoplay();
        }

        function onTouchMove(e) {
            if (!isDragging) return;
            const t    = e.touches[0];
            dragDeltaX = t.clientX - dragStartX;
            velocity   = (t.clientX - lastX) * 0.85;
            lastX      = t.clientX;
            track.style.transform = `translateX(${targetX(current) + dragDeltaX * 0.78}px)`;
        }

        function onTouchEnd() {
            if (!isDragging) return;
            isDragging = false;
            track.classList.remove('sv-aw-dragging');

            if      (dragDeltaX < -45 || velocity < -2.5) goTo(Math.min(total - 1, current + 1));
            else if (dragDeltaX >  45 || velocity >  2.5) goTo(Math.max(0,         current - 1));
            else    goTo(current);
        }

        /* ── Keyboard ── */
        document.addEventListener('keydown', (e) => {
            const lb = document.getElementById('svLightbox');
            if (lb && !lb.hidden) return;
            if (e.key === 'ArrowLeft')  goTo(current - 1 < 0 ? total - 1 : current - 1);
            if (e.key === 'ArrowRight') goTo((current + 1) % total);
        });

        /* ── Pause on hover ── */
        const stage = document.getElementById('svAwardsStage');
        if (stage) {
            stage.addEventListener('mouseenter', stopAutoplay);
            stage.addEventListener('mouseleave', startAutoplay);
        }

        /* ── Re-centre on resize ── */
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => applyState(current, true), 120);
        });

        /* ── Expose for lightbox navigation ── */
        window._svAwardGoTo      = goTo;
        window._svAwardGetCurrent = () => current;
        window._svAwardTotal     = total;
    }

    /* ════════════════════════════════════════════════════════
       LIGHTBOX
       FIX 2: all element IDs now match the sv-aw-lb- HTML
    ════════════════════════════════════════════════════════ */
    function initLightbox() {
        const lb       = document.getElementById('svLightbox');
        const backdrop = document.getElementById('svLightboxBackdrop');
        const closeBtn = document.getElementById('svLightboxClose');
        const imgEl    = document.getElementById('svLightboxImg');
        const titleEl  = document.getElementById('svLightboxTitle');
        const orgEl    = document.getElementById('svLightboxOrgText');
        const dateEl   = document.getElementById('svLightboxDateText');
        const descEl   = document.getElementById('svLightboxDesc');
        const catEl    = document.getElementById('svLightboxCat');
        const lbPrev   = document.getElementById('svLightboxPrev');
        const lbNext   = document.getElementById('svLightboxNext');

        if (!lb) return;

        let lbCurrent = 0;
        const cards   = Array.from(document.querySelectorAll('.sv-aw-card'));
        const total   = cards.length;

        /* ── Open ── */
        window.openLightbox = function (card) {
            lbCurrent = parseInt(card.dataset.index, 10) || 0;
            populate(lbCurrent);
            lb.hidden = false;
            document.body.style.overflow = 'hidden';
            setTimeout(() => closeBtn && closeBtn.focus(), 50);
        };

        /* ── Populate all fields from data-* attributes ── */
       /* Image — reliable load */
if (imgEl) {

    const newSrc = card.dataset.src || '';

    // Remove previous load handler
    imgEl.onload = null;

    // Reset animation state
    imgEl.style.transition = 'none';
    imgEl.style.opacity = '0';
    imgEl.style.transform = 'scale(0.96)';

    // FORCE reload (this is the real fix)
    imgEl.src = '';
    
    // Small delay ensures browser registers change
    requestAnimationFrame(() => {
        imgEl.src = newSrc;
        imgEl.alt = card.dataset.title || '';

        imgEl.onload = () => {
            imgEl.style.transition = 'opacity 0.45s ease, transform 0.45s ease';
            imgEl.style.opacity = '1';
            imgEl.style.transform = 'scale(1)';
        };
    });
}
            /* Text fields */
            if (catEl)   catEl.textContent  = card.dataset.cat   || '';
            if (titleEl) titleEl.textContent = card.dataset.title || '';
            if (orgEl)   orgEl.textContent   = card.dataset.org   || '';
            if (dateEl)  dateEl.textContent  = card.dataset.date  || '';
            if (descEl)  descEl.textContent  = card.dataset.desc  || '';
        }

        /* ── Close ── */
        function close() {
            lb.hidden = true;
            document.body.style.overflow = '';
        }

        if (closeBtn) closeBtn.addEventListener('click', close);
        if (backdrop) backdrop.addEventListener('click', close);

        /* ── Lightbox prev / next ── */
        if (lbPrev) lbPrev.addEventListener('click', () => {
            populate(lbCurrent - 1);
            if (window._svAwardGoTo) window._svAwardGoTo(lbCurrent);
        });
        if (lbNext) lbNext.addEventListener('click', () => {
            populate(lbCurrent + 1);
            if (window._svAwardGoTo) window._svAwardGoTo(lbCurrent);
        });

        /* ── Keyboard inside lightbox ── */
        lb.addEventListener('keydown', (e) => {
            if (e.key === 'Escape')     close();
            if (e.key === 'ArrowLeft')  lbPrev?.click();
            if (e.key === 'ArrowRight') lbNext?.click();
        });

        /* ── Swipe on lightbox image ── */
        let lbTouchX = 0;
        if (imgEl) {
            imgEl.addEventListener('touchstart', e => { lbTouchX = e.touches[0].clientX; }, { passive: true });
            imgEl.addEventListener('touchend',   e => {
                const dx = e.changedTouches[0].clientX - lbTouchX;
                if      (dx < -50) lbNext?.click();
                else if (dx >  50) lbPrev?.click();
            });
        }
    }

})();


// ===== SEPARATOR =====


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

})();/**
 * backedby.js — Sound Vision · Backed By Section
 * ─────────────────────────────────────────────────
 * RAF infinite scroll — identical engine to team.js and award.js.
 * Right → left nonstop scroll with eased hover-pause.
 *
 * Globals prefixed svBb to avoid collision with svTm / svAw.
 *
 * HOW TO ADD MORE LOGOS:
 *   Just add entries to $sv_backed_by in backedby.php.
 *   The JS automatically clones the set — no JS changes needed.
 */
(function () {
    'use strict';

    /* ── Scroll speed: pixels per second ─────────────────────
       Same 55 px/s as team & award sections for visual harmony.
       Increase to scroll faster, decrease for slower drift.     */
    var SV_BB_PPS = 55;

    document.addEventListener('DOMContentLoaded', svBbInit);

    /* ════════════════════════════════════════════════════════
       BOOT
    ════════════════════════════════════════════════════════ */
    function svBbInit() {
        svBbReveal();
        svBbScroll();
    }

    /* ════════════════════════════════════════════════════════
       SCROLL REVEAL
       (Intersection Observer — fades header & strip in when
       they scroll into view)
    ════════════════════════════════════════════════════════ */
    function svBbReveal() {
        var els = document.querySelectorAll('[data-bb-reveal]');
        if (!els.length) return;

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry, idx) {
                if (!entry.isIntersecting) return;
                setTimeout(function () {
                    entry.target.classList.add('sv-bb-in');
                }, idx * 150);
                io.unobserve(entry.target);
            });
        }, { threshold: 0.12 });

        els.forEach(function (el) { io.observe(el); });
    }

    /* ════════════════════════════════════════════════════════
       INFINITE RAF SCROLL
       ──────────────────────────────────────────────────────
       Algorithm (identical to team.js & award.js):

       1.  Clone all original .sv-bb-logo-slot elements and
           append to track → track now has 2 identical sets.
       2.  Measure pixel width of ONE full set (slots + gaps).
       3.  Each RAF frame: advance offset by speed × deltaTime.
       4.  offset ≥ setWidth  →  subtract setWidth.
           Jump is invisible because the two sets are identical
           and the reset happens at the exact moment set 1 exits
           and set 2 enters from the right.
       5.  Hover: ease targetSpeed → 0 (smooth deceleration).
           Mouse leave: ease targetSpeed back to SV_BB_PPS.
    ════════════════════════════════════════════════════════ */
    function svBbScroll() {
        var outer = document.getElementById('svBbOuter');
        var track = document.getElementById('svBbTrack');
        if (!outer || !track) return;

        /* ── Step 1: clone original logo slots ── */
        var origSlots = Array.from(track.querySelectorAll('.sv-bb-logo-slot'));
        if (!origSlots.length) return;

        origSlots.forEach(function (slot) {
            var clone = slot.cloneNode(true);
            /* Clones: hide from screen readers, remove from tab order */
            clone.setAttribute('aria-hidden', 'true');
            /* Disable any <a> links inside clones from being focusable */
            var links = clone.querySelectorAll('a');
            links.forEach(function (a) { a.setAttribute('tabindex', '-1'); });
            track.appendChild(clone);
        });

        /* ── State ── */
        var setW         = 0;           /* px width of ONE full set of logos  */
        var offset       = 0;           /* current scroll position in px      */
        var currentSpeed = SV_BB_PPS;  /* actual speed applied this frame    */
        var targetSpeed  = SV_BB_PPS;  /* speed we are easing toward         */
        var lastTs       = null;        /* previous RAF timestamp             */
        var rafId        = null;        /* (unused but kept for clarity)      */

        /* ── Step 2: measure one set ── */
        function measure() {
            var gap = parseFloat(getComputedStyle(track).gap) || 32;
            var w   = 0;
            origSlots.forEach(function (slot) {
                w += slot.offsetWidth + gap;
            });
            setW = w;
        }

        /* ── Steps 3 & 4: RAF tick ── */
        function tick(ts) {
            /* Ease currentSpeed toward targetSpeed.
               0.07 factor → ~0.35 s settling time (smooth, not instant). */
            currentSpeed += (targetSpeed - currentSpeed) * 0.07;

            var isMoving = currentSpeed > 0.4 || targetSpeed > 0;

            if (isMoving && setW > 0) {
                if (lastTs !== null) {
                    /* Cap delta at 50 ms — prevents giant jump after tab switch */
                    var delta = Math.min((ts - lastTs) / 1000, 0.05);
                    offset   += currentSpeed * delta;

                    /* Step 4: seamless loop */
                    if (offset >= setW) offset -= setW;

                    track.style.transform = 'translateX(-' + offset.toFixed(2) + 'px)';
                }
                lastTs = ts;
            } else {
                /* Fully paused — freeze lastTs so no delta spike on resume */
                lastTs = null;
            }

            rafId = requestAnimationFrame(tick);
        }

        /* ── Step 5: hover ease (desktop) ── */
        outer.addEventListener('mouseenter', function () {
            targetSpeed = 0;
        });
        outer.addEventListener('mouseleave', function () {
            targetSpeed = SV_BB_PPS;
        });

        /* ── Touch: tap anywhere outside a logo to toggle pause ── */
        var touchPaused = false;
        outer.addEventListener('touchstart', function (e) {
            /* Allow logo link taps to pass through */
            if (e.target.closest('a.sv-bb-logo-wrap')) return;
            touchPaused = !touchPaused;
            targetSpeed = touchPaused ? 0 : SV_BB_PPS;
        }, { passive: true });

        /* ── Visibility API — prevents giant delta on tab return ── */
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible' && !touchPaused) {
                targetSpeed = SV_BB_PPS;
                lastTs      = null;
            }
        });

        /* ── Resize: re-measure and clamp offset ── */
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                measure();
                if (setW > 0 && offset >= setW) offset = offset % setW;
            }, 220);
        });

        /* ── Start: wait 2 RAF frames so layout is fully painted ── */
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                measure();
                rafId = requestAnimationFrame(tick);
            });
        });
    }

})();
