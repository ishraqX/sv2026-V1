/**
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
