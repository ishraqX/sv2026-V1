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

        /* ── Get cards and setup state ── */
        var cards = Array.from(track.querySelectorAll('.sv-tm-card'));
        if (!cards.length) return;

        var totalCards = cards.length;
        var currentIdx = 0;
        var isDragging = false;
        var dragStartX = 0;
        var dragDeltaX = 0;
        var lastX = 0;
        var velocity = 0;

        /* ── Card dimensions ── */
        function getCardWidth() { return cards[0]?.offsetWidth || 280; }
        function getGap() { return parseFloat(getComputedStyle(track).gap) || 24; }
        function getStep() { return getCardWidth() + getGap(); }

        /* ── Target translateX for centering a card ── */
        function targetX(idx) {
            var outerW = outer.offsetWidth;
            var centerOffset = (outerW - getCardWidth()) / 2;
            return centerOffset - idx * getStep();
        }

        /* ── Apply transform with optional instant snap ── */
        function setTransform(tx, instant) {
            if (instant) {
                track.classList.add('sv-tm-dragging');
                track.style.transform = 'translateX(' + tx + 'px)';
                void track.offsetWidth; // force reflow
                track.classList.remove('sv-tm-dragging');
            } else {
                track.style.transform = 'translateX(' + tx + 'px)';
            }
        }

        /* ── Update active states ── */
        function applyState(idx, instant) {
            idx = Math.max(0, Math.min(totalCards - 1, idx));
            currentIdx = idx;

            cards.forEach(function(card, i) {
                card.classList.toggle('sv-tm-active', i === idx);
                card.classList.toggle('sv-tm-adjacent', Math.abs(i - idx) === 1);
            });

            setTransform(targetX(idx), instant);
        }

        /* ── Smooth navigation to index ── */
        function goTo(idx) {
            applyState(idx, false); // false = use CSS transition for smooth movement
        }

        /* ── Build arrows ── */
        function buildArrows() {
            var col = outer.closest('.sv-tm-scroll-col') || outer.parentElement;
            if (!col) return;

            col.style.position = 'relative';

            function makeBtn(dir) {
                var btn = document.createElement('button');
                btn.className = 'sv-tm-arrow sv-tm-arrow--' + dir;
                btn.setAttribute('aria-label', dir === 'prev' ? 'Previous team member' : 'Next team member');
                btn.innerHTML = dir === 'prev'
                    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>'
                    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';
                return btn;
            }

            var prevBtn = makeBtn('prev');
            var nextBtn = makeBtn('next');

            col.appendChild(prevBtn);
            col.appendChild(nextBtn);

            /* Smooth directional navigation */
            prevBtn.addEventListener('click', function() {
                goTo(currentIdx - 1 < 0 ? totalCards - 1 : currentIdx - 1);
            });

            nextBtn.addEventListener('click', function() {
                goTo((currentIdx + 1) % totalCards);
            });
        }

        /* ── Drag functionality ── */
        var supportsPointer = (typeof PointerEvent !== 'undefined');

        function onDragStart(clientX) {
            isDragging = true;
            dragStartX = clientX;
            dragDeltaX = 0;
            lastX = clientX;
            velocity = 0;
            track.classList.add('sv-tm-dragging');
        }

        function onDragMove(clientX) {
            if (!isDragging) return;
            var dx = lastX - clientX;
            dragDeltaX += Math.abs(dx);
            velocity = velocity * 0.8 + dx * 0.2;
            lastX = clientX;

            var currentX = targetX(currentIdx);
            setTransform(currentX - (clientX - dragStartX), true);
        }

        function onDragEnd() {
            if (!isDragging) return;
            isDragging = false;
            track.classList.remove('sv-tm-dragging');

            /* Determine target based on drag distance and velocity */
            var dragDistance = Math.abs(dragStartX - lastX);
            var threshold = getCardWidth() * 0.3; // 30% of card width

            var targetIdx = currentIdx;
            if (dragDistance > threshold || Math.abs(velocity) > 15) {
                if (velocity > 0 || dragStartX - lastX > threshold) {
                    targetIdx = (currentIdx + 1) % totalCards;
                } else if (velocity < 0 || lastX - dragStartX > threshold) {
                    targetIdx = currentIdx - 1 < 0 ? totalCards - 1 : currentIdx - 1;
                }
            }

            goTo(targetIdx);
        }

        /* ── Event listeners ── */
        if (supportsPointer) {
            outer.addEventListener('pointerdown', function(e) {
                if (e.button !== 0) return;
                outer.setPointerCapture(e.pointerId);
                onDragStart(e.clientX);
            });
            outer.addEventListener('pointermove', function(e) {
                if (!isDragging) return;
                e.preventDefault();
                onDragMove(e.clientX);
            }, { passive: false });
            outer.addEventListener('pointerup', onDragEnd);
            outer.addEventListener('pointercancel', onDragEnd);
        } else {
            outer.addEventListener('mousedown', function(e) {
                if (e.button !== 0) return;
                onDragStart(e.clientX);
            });
            window.addEventListener('mousemove', function(e) { onDragMove(e.clientX); });
            window.addEventListener('mouseup', onDragEnd);

            outer.addEventListener('touchstart', function(e) {
                onDragStart(e.touches[0].clientX);
            }, { passive: true });
            outer.addEventListener('touchmove', function(e) {
                e.preventDefault();
                onDragMove(e.touches[0].clientX);
            }, { passive: false });
            outer.addEventListener('touchend', onDragEnd);
        }

        /* ── Hover pause ── */
        outer.addEventListener('mouseenter', function() {
            // Could add hover pause logic here if needed
        });
        outer.addEventListener('mouseleave', function() {
            // Could add hover resume logic here if needed
        });

        /* ── Resize handling ── */
        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                applyState(currentIdx, true);
            }, 200);
        });

        /* ── Initialize ── */
        buildArrows();
        applyState(0, true); // Start with first card centered
    }

})();