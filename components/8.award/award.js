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