/**
 * team.js  ·  Sound Vision — Team Section  v4
 * ─────────────────────────────────────────────
 * RAF-based infinite scroll — zero CSS animation, zero end-jump.
 * All globals prefixed svTm to avoid collision with award.js
 */
(function () {
    'use strict';

    /* pixels per second — tune this to taste */
    var SVT_PPS = 55;

    document.addEventListener('DOMContentLoaded', svTmInit);

    /* ── boot ───────────────────────────────────── */
    function svTmInit() {
        svTmParticles();
        svTmReveal();
        svTmScroll();
    }

    /* ════════════════════════════════════════════
       FLOATING PARTICLES
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
       SCROLL REVEAL
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
       INFINITE RAF SCROLL
       ─────────────────────────────────────────
       1. Clone original cards → append to track (2 identical sets).
       2. Measure width of one set.
       3. Drive translateX with RAF each frame.
       4. When offset ≥ setWidth → subtract setWidth (invisible reset).
       5. Speed eases on hover enter/leave (no jarring stop).
    ════════════════════════════════════════════ */
    function svTmScroll() {
        var outer = document.getElementById('svTmOuter');
        var track = document.getElementById('svTmTrack');
        if (!outer || !track) return;

        /* ── Clone cards to create second set ── */
        var origCards = Array.from(track.querySelectorAll('.sv-tm-card'));
        if (!origCards.length) return;

        origCards.forEach(function (card) {
            var clone = card.cloneNode(true);
            clone.setAttribute('aria-hidden', 'true');
            track.appendChild(clone);
        });

        /* ── State ── */
        var setW         = 0;       /* pixel width of ONE full set        */
        var offset       = 0;       /* current scroll position in px      */
        var currentSpeed = SVT_PPS; /* actual speed this frame            */
        var targetSpeed  = SVT_PPS; /* speed we are easing toward         */
        var lastTs       = null;    /* previous RAF timestamp             */
        var rafId        = null;

        /* ── Measure one set width ── */
        function measure() {
            var gap = parseFloat(getComputedStyle(track).gap) || 24;
            var w   = 0;
            origCards.forEach(function (c) { w += c.offsetWidth + gap; });
            setW = w;
        }

        /* ── RAF tick with eased speed ── */
        function tick(ts) {
            /* Ease currentSpeed toward targetSpeed */
            currentSpeed += (targetSpeed - currentSpeed) * 0.07;

            var moving = currentSpeed > 0.4 || targetSpeed > 0;

            if (moving && setW > 0) {
                if (lastTs !== null) {
                    var delta = Math.min((ts - lastTs) / 1000, 0.05); /* cap at 50 ms */
                    offset += currentSpeed * delta;
                    /* Seamless loop — reset when one full set has scrolled by */
                    if (offset >= setW) offset -= setW;
                    track.style.transform = 'translateX(-' + offset.toFixed(2) + 'px)';
                }
                lastTs = ts;
            } else {
                /* Fully paused — don't advance timestamp so no delta spike on resume */
                lastTs = null;
            }

            rafId = requestAnimationFrame(tick);
        }

        /* ── Hover: ease to stop / ease back ── */
        outer.addEventListener('mouseenter', function () { targetSpeed = 0; });
        outer.addEventListener('mouseleave', function () { targetSpeed = SVT_PPS; });

        /* ── Touch: tap to toggle pause ── */
        var touchPaused = false;
        outer.addEventListener('touchstart', function () {
            touchPaused = !touchPaused;
            targetSpeed = touchPaused ? 0 : SVT_PPS;
        }, { passive: true });

        /* ── Visibility API: resume if tab was backgrounded ── */
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible' && !touchPaused) {
                targetSpeed = SVT_PPS;
                lastTs = null; /* prevent giant delta spike after long bg */
            }
        });

        /* ── Resize: re-measure (with clamp) ── */
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                measure();
                if (setW > 0 && offset >= setW) offset = offset % setW;
            }, 220);
        });

        /* ── Start: wait two frames so layout is stable ── */
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                measure();
                rafId = requestAnimationFrame(tick);
            });
        });
    }

})();