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

                        /* Spring toward target — tuned to match award section feel.
                           0.22 accel + 0.78 damping → fast start, slight overshoot, clean settle */
                        nudgeSpeed += remaining * 0.22;
                        nudgeSpeed *= 0.78;          /* damping — lower = bouncier */
                        offset     += nudgeSpeed;
                        offset      = wrapOffset(offset);

                        /* Close enough → snap and clear */
                        if (Math.abs(remaining) < 0.3 && Math.abs(nudgeSpeed) < 0.2) {
                            offset     = wrapOffset(nudgeTarget);
                            nudgeTarget = null;
                            nudgeSpeed  = 0;
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
                if (setW <= 0) return;
                pauseAuto();
                nudgeSpeed  = 0;
                nudgeTarget = wrapOffset(offset - cardStep());
            });

            nextBtn.addEventListener('click', function () {
                if (setW <= 0) return;
                pauseAuto();
                nudgeSpeed  = 0;
                nudgeTarget = wrapOffset(offset + cardStep());
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
            pauseAuto();
            outer.classList.add('sv-tm-dragging');
        }

        function onDragMove(clientX) {
            if (!dragActive) return;
            var dx = dragLastX - clientX;          /* positive = scroll right */
            dragVelocity = dragVelocity * 0.72 + dx * 0.28; /* EMA — smoother, less noisy */
            dragLastX    = clientX;
            offset       = wrapOffset(offset + dx);
            track.style.transform = 'translateX(-' + offset.toFixed(2) + 'px)';
        }

        function onDragEnd() {
            if (!dragActive) return;
            dragActive = false;
            outer.classList.remove('sv-tm-dragging');

            /* Momentum coast: apply remaining velocity as a nudge target */
            if (Math.abs(dragVelocity) > 1.5) {
                var coast = dragVelocity * 12;      /* coast distance in px — tighter than before */
                nudgeTarget = wrapOffset(offset + coast);
                nudgeSpeed  = dragVelocity * 0.5;
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