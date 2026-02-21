/**
 * award.js — Sound Vision Awards Carousel
 * Physics-based momentum · Touch/Drag · Gesture hint · Lightbox
 */
(function () {
    'use strict';

    /* ── Wait for DOM ─────────────────────────────────────── */
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
            const p = document.createElement('span');
            p.className = 'sv-particle';
            const size = Math.random() * 3 + 1;
            const left = Math.random() * 100;
            const delay = Math.random() * 12;
            const dur   = Math.random() * 10 + 10;
            const drift = (Math.random() - 0.5) * 80;
            Object.assign(p.style, {
                width:  size + 'px',
                height: size + 'px',
                left:   left + '%',
                animationDelay:    delay + 's',
                animationDuration: dur + 's',
                '--drift': drift + 'px',
                opacity: Math.random() * 0.5 + 0.2,
            });
            container.appendChild(p);
        }
    }

    /* ════════════════════════════════════════════════════════
       SCROLL REVEAL
    ════════════════════════════════════════════════════════ */
    function initScrollReveal() {
        const els = document.querySelectorAll('[data-animate]');
        if (!els.length) return;
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('sv-revealed');
                    obs.unobserve(e.target);
                }
            });
        }, { threshold: 0.15 });
        els.forEach(el => obs.observe(el));
    }

    /* ════════════════════════════════════════════════════════
       CAROUSEL
    ════════════════════════════════════════════════════════ */
    function initCarousel() {
        const track       = document.getElementById('svAwardsTrack');
        const dotsWrap    = document.getElementById('svAwardsDots');
        const prevBtn     = document.getElementById('svAwardPrev');
        const nextBtn     = document.getElementById('svAwardNext');
        const counterEl   = document.getElementById('svCounterCurrent');
        const gestureHint = document.getElementById('svGestureHint');

        if (!track) return;

        const cards   = Array.from(track.querySelectorAll('.sv-award-card'));
        const total   = cards.length;
        const dots    = dotsWrap ? Array.from(dotsWrap.querySelectorAll('.sv-awards-dot')) : [];

        let current       = 0;
        let gestureShown  = false;
        let gestureTimer;
        let autoplayTimer;
        let velocity      = 0;  // physics momentum
        let lastX         = 0;
        let isDragging    = false;
        let dragStartX    = 0;
        let dragDeltaX    = 0;
        let rafId;

        /* ── Layout constants ── */
        const CARD_W = () => cards[0]?.offsetWidth || 300;
        const GAP    = () => parseInt(getComputedStyle(track).gap) || 24;
        const STEP   = () => CARD_W() + GAP();

        /* ── Compute translateX for index ── */
        function targetX(idx) {
            const outer      = track.parentElement;
            const outerW     = outer ? outer.offsetWidth : window.innerWidth;
            const centerOffset = (outerW - CARD_W()) / 2;
            return centerOffset - idx * STEP();
        }

        /* ── Apply state ── */
        function applyState(idx, instant = false) {
            // Clamp
            idx = Math.max(0, Math.min(total - 1, idx));

            cards.forEach((card, i) => {
                card.classList.remove('sv-active', 'sv-adjacent');
                if (i === idx)              card.classList.add('sv-active');
                else if (Math.abs(i - idx) === 1) card.classList.add('sv-adjacent');
            });

            // Update track position
            const tx = targetX(idx);
            if (instant) {
                track.style.transition = 'none';
                track.style.transform  = `translateX(${tx}px)`;
                void track.offsetHeight; // force reflow
                track.style.transition = '';
            } else {
                track.style.transform = `translateX(${tx}px)`;
            }

            // Dots
            dots.forEach((d, i) => {
                d.classList.toggle('sv-awards-dot--active', i === idx);
            });

            // Counter
            if (counterEl) {
                counterEl.textContent = String(idx + 1).padStart(2, '0');
                // Brief scale pop
                counterEl.style.transform = 'scale(1.3)';
                counterEl.style.color     = '#F5E27A';
                setTimeout(() => {
                    counterEl.style.transform = '';
                    counterEl.style.color     = '';
                }, 250);
            }
        }

        function goTo(idx) {
            current = Math.max(0, Math.min(total - 1, idx));
            applyState(current);
            resetAutoplay();
        }

        /* ── Autoplay ── */
        function startAutoplay() {
            clearInterval(autoplayTimer);
            autoplayTimer = setInterval(() => {
                if (!isDragging) goTo((current + 1) % total);
            }, 4000);
        }
        function resetAutoplay() {
            clearInterval(autoplayTimer);
            startAutoplay();
        }

        /* ── Gesture hint ── */
        function showGestureHint() {
            if (gestureShown || !gestureHint) return;
            gestureHint.classList.remove('sv-gesture-hidden');
            gestureTimer = setTimeout(() => {
                if (gestureHint) gestureHint.classList.add('sv-gesture-hidden');
            }, 4200);
        }

        function hideGestureHint() {
            if (!gestureHint) return;
            clearTimeout(gestureTimer);
            gestureHint.classList.add('sv-gesture-hidden');
            gestureShown = true;
        }

        /* ── Init ── */
        applyState(0, true);
        startAutoplay();

        // Show gesture hint after 1.2s (gives page time to settle)
        setTimeout(showGestureHint, 1200);

        /* ── Arrow buttons ── */
        if (prevBtn) prevBtn.addEventListener('click', () => {
            hideGestureHint();
            goTo(current - 1 < 0 ? total - 1 : current - 1);
        });
        if (nextBtn) nextBtn.addEventListener('click', () => {
            hideGestureHint();
            goTo((current + 1) % total);
        });

        /* ── Dots ── */
        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                hideGestureHint();
                goTo(i);
            });
        });

        /* ── Card click (open lightbox) ── */
        cards.forEach((card, i) => {
            card.addEventListener('click', (e) => {
                if (Math.abs(dragDeltaX) > 8) return; // was a drag
                if (i === current) {
                    openLightbox(card);
                } else {
                    hideGestureHint();
                    goTo(i);
                }
            });
            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (i === current) openLightbox(card);
                    else goTo(i);
                }
            });
        });

        /* ── Physics drag (mouse) ── */
        track.addEventListener('mousedown', onDragStart);
        window.addEventListener('mousemove', onDragMove);
        window.addEventListener('mouseup', onDragEnd);

        /* ── Touch ── */
        track.addEventListener('touchstart', onTouchStart, { passive: true });
        track.addEventListener('touchmove',  onTouchMove,  { passive: true });
        track.addEventListener('touchend',   onTouchEnd);

        let pointerX = 0;

        function onDragStart(e) {
            isDragging   = true;
            dragStartX   = e.clientX;
            dragDeltaX   = 0;
            pointerX     = e.clientX;
            lastX        = e.clientX;
            velocity     = 0;
            track.classList.add('sv-dragging');
            clearInterval(autoplayTimer);
            cancelAnimationFrame(rafId);
            hideGestureHint();
            e.preventDefault();
        }

        function onDragMove(e) {
            if (!isDragging) return;
            dragDeltaX = e.clientX - dragStartX;

            // Compute velocity for momentum
            velocity = (e.clientX - lastX) * 0.85;
            lastX    = e.clientX;

            const base = targetX(current);
            const resist = dragDeltaX * 0.75; // resistance
            track.style.transition = 'none';
            track.style.transform  = `translateX(${base + resist}px)`;
        }

        function onDragEnd() {
            if (!isDragging) return;
            isDragging = false;
            track.classList.remove('sv-dragging');
            track.style.transition = '';

            const SWIPE_THRESHOLD = 50;
            const VEL_THRESHOLD   = 3;

            if (dragDeltaX < -SWIPE_THRESHOLD || velocity < -VEL_THRESHOLD) {
                goTo(Math.min(total - 1, current + 1));
            } else if (dragDeltaX > SWIPE_THRESHOLD || velocity > VEL_THRESHOLD) {
                goTo(Math.max(0, current - 1));
            } else {
                applyState(current); // snap back
                resetAutoplay();
            }

            // Momentum coast then snap
            momentumSettle();
        }

        function momentumSettle() {
            if (Math.abs(velocity) < 0.5) return;
            velocity *= 0.9;
            const base = targetX(current);
            const drift = velocity * 8;
            track.style.transition = 'transform 0.05s linear';
            track.style.transform  = `translateX(${base + drift}px)`;
            rafId = requestAnimationFrame(() => {
                velocity *= 0.85;
                if (Math.abs(velocity) > 0.5) momentumSettle();
                else {
                    track.style.transition = '';
                    applyState(current);
                }
            });
        }

        function onTouchStart(e) {
            const t = e.touches[0];
            isDragging = true;
            dragStartX = t.clientX;
            dragDeltaX = 0;
            lastX      = t.clientX;
            velocity   = 0;
            track.classList.add('sv-dragging');
            clearInterval(autoplayTimer);
            hideGestureHint();
        }

        function onTouchMove(e) {
            if (!isDragging) return;
            const t    = e.touches[0];
            dragDeltaX = t.clientX - dragStartX;
            velocity   = (t.clientX - lastX) * 0.85;
            lastX      = t.clientX;

            const base   = targetX(current);
            const resist = dragDeltaX * 0.78;
            track.style.transition = 'none';
            track.style.transform  = `translateX(${base + resist}px)`;
        }

        function onTouchEnd() {
            if (!isDragging) return;
            isDragging = false;
            track.classList.remove('sv-dragging');
            track.style.transition = '';

            if (dragDeltaX < -45 || velocity < -2.5) {
                goTo(Math.min(total - 1, current + 1));
            } else if (dragDeltaX > 45 || velocity > 2.5) {
                goTo(Math.max(0, current - 1));
            } else {
                applyState(current);
                resetAutoplay();
            }
        }

        /* ── Keyboard ── */
        document.addEventListener('keydown', (e) => {
            if (document.getElementById('svLightbox') && !document.getElementById('svLightbox').hidden) return;
            if (e.key === 'ArrowLeft')  goTo(current - 1 < 0 ? total - 1 : current - 1);
            if (e.key === 'ArrowRight') goTo((current + 1) % total);
        });

        /* ── Resize: re-center ── */
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => applyState(current, true), 120);
        });

        /* ── Pause autoplay on hover ── */
        const stage = document.getElementById('svAwardsStage');
        if (stage) {
            stage.addEventListener('mouseenter', () => clearInterval(autoplayTimer));
            stage.addEventListener('mouseleave', startAutoplay);
        }

        /* ── Export goTo for lightbox navigation ── */
        window._svAwardGoTo = goTo;
        window._svAwardGetCurrent = () => current;
        window._svAwardTotal = total;
    }

    /* ════════════════════════════════════════════════════════
       LIGHTBOX
    ════════════════════════════════════════════════════════ */
    function initLightbox() {
        const lb         = document.getElementById('svLightbox');
        const backdrop   = document.getElementById('svLightboxBackdrop');
        const closeBtn   = document.getElementById('svLightboxClose');
        const img        = document.getElementById('svLightboxImg');
        const title      = document.getElementById('svLightboxTitle');
        const orgEl      = document.getElementById('svLightboxOrgText');
        const dateEl     = document.getElementById('svLightboxDateText');
        const descEl     = document.getElementById('svLightboxDesc');
        const catEl      = document.getElementById('svLightboxCat');
        const lbPrev     = document.getElementById('svLightboxPrev');
        const lbNext     = document.getElementById('svLightboxNext');

        if (!lb) return;

        let lbCurrent = 0;
        const cards   = Array.from(document.querySelectorAll('.sv-award-card'));

        window.openLightbox = function (card) {
            const idx  = parseInt(card.dataset.index, 10);
            lbCurrent  = idx;
            populateLightbox(idx);
            lb.hidden  = false;
            document.body.style.overflow = 'hidden';
            // Trap focus
            setTimeout(() => closeBtn && closeBtn.focus(), 50);
        };

        function populateLightbox(idx) {
            const card = cards[idx];
            if (!card) return;

            // Image: fade transition
            if (img) {
                // Reset any previous error state
                img.style.display   = '';
                const errDiv = document.getElementById('svLightboxImgError');
                if (errDiv) errDiv.style.display = 'none';

                img.style.opacity   = '0';
                img.style.transform = 'scale(0.97)';
                img.src = card.dataset.src || '';
                img.alt = card.dataset.title || '';
                img.onload = () => {
                    img.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    img.style.opacity    = '1';
                    img.style.transform  = 'scale(1)';
                };
                // Immediate show even if cached
                if (img.complete && img.naturalWidth > 0) {
                    img.style.opacity   = '1';
                    img.style.transform = 'scale(1)';
                }
            }
            if (title)  title.textContent  = card.dataset.title || '';
            if (orgEl)  orgEl.textContent  = card.dataset.org   || '';
            if (dateEl) dateEl.textContent = card.dataset.date  || '';
            if (descEl) descEl.textContent = card.dataset.desc  || '';
            if (catEl)  catEl.textContent  = card.dataset.cat   || '';
            lbCurrent = idx;
        }

        function closeLightbox() {
            lb.hidden = true;
            document.body.style.overflow = '';
        }

        if (closeBtn)  closeBtn.addEventListener('click', closeLightbox);
        if (backdrop)  backdrop.addEventListener('click', closeLightbox);

        if (lbPrev) lbPrev.addEventListener('click', () => {
            const prev = lbCurrent - 1 < 0 ? cards.length - 1 : lbCurrent - 1;
            populateLightbox(prev);
            if (window._svAwardGoTo) window._svAwardGoTo(prev);
        });
        if (lbNext) lbNext.addEventListener('click', () => {
            const next = (lbCurrent + 1) % cards.length;
            populateLightbox(next);
            if (window._svAwardGoTo) window._svAwardGoTo(next);
        });

        // Keyboard inside lightbox
        lb.addEventListener('keydown', (e) => {
            if (e.key === 'Escape')       closeLightbox();
            if (e.key === 'ArrowLeft')    lbPrev?.click();
            if (e.key === 'ArrowRight')   lbNext?.click();
        });

        // Swipe on lightbox image
        let lbTouchX = 0;
        if (img) {
            img.addEventListener('touchstart', e => { lbTouchX = e.touches[0].clientX; }, { passive: true });
            img.addEventListener('touchend',   e => {
                const dx = e.changedTouches[0].clientX - lbTouchX;
                if      (dx < -50) lbNext?.click();
                else if (dx >  50) lbPrev?.click();
            });
        }
    }

})();