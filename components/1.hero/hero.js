/**
 * Sound Vision Hero Slider
 * Self-contained — no dependencies.
 *
 * CONFIG:
 *   AUTOPLAY_MS  = duration each slide shows before auto-advancing
 *   TOTAL        = number of slides (update if you add/remove slides)
 */
(function(){
    'use strict';

    var AUTOPLAY_MS = 6000;   // ← change slide duration here (ms)
    var TOTAL       = 3;      // ← update if slides change

    var track    = document.getElementById('hsTrack');
    var slides   = Array.from(document.querySelectorAll('.hs-slide'));
    var dots     = Array.from(document.querySelectorAll('.hs-dot'));
    var prevBtn  = document.getElementById('hsPrev');
    var nextBtn  = document.getElementById('hsNext');
    var bar      = document.getElementById('hsProgressBar');
    var hero     = document.getElementById('hero');

    if (!slides.length) return;

    var cur    = 0;
    var timer  = null;
    var paused = false;

    /* ── Navigate to slide n ─────────────────────────── */
    function goTo(n) {
        n = ((n % TOTAL) + TOTAL) % TOTAL;
        if (n === cur) return;

        slides[cur].classList.remove('is-active');
        dots[cur].classList.remove('active');
        dots[cur].setAttribute('aria-selected','false');

        cur = n;

        slides[cur].classList.add('is-active');
        dots[cur].classList.add('active');
        dots[cur].setAttribute('aria-selected','true');
    }

    /* ── Progress bar ─────────────────────────────────── */
    function startBar() {
        if (!bar) return;
        bar.style.transition = 'none';
        bar.style.width      = '0%';
        void bar.offsetWidth;                          // force reflow
        bar.style.transition = 'width '+AUTOPLAY_MS+'ms linear';
        bar.style.width      = '100%';
    }
    function stopBar() {
        if (!bar) return;
        bar.style.transition = 'none';
        bar.style.width      = '0%';
    }

    /* ── Autoplay ─────────────────────────────────────── */
    function startAuto() {
        if (paused) return;
        clearTimeout(timer);
        startBar();
        timer = setTimeout(function(){ goTo(cur+1); startAuto(); }, AUTOPLAY_MS);
    }
    function stopAuto()  { clearTimeout(timer); stopBar(); }
    function resetAuto() { stopAuto(); if(!paused) startAuto(); }

    /* ── Events ───────────────────────────────────────── */
    if (prevBtn) prevBtn.addEventListener('click', function(){ goTo(cur-1); resetAuto(); });
    if (nextBtn) nextBtn.addEventListener('click', function(){ goTo(cur+1); resetAuto(); });

    dots.forEach(function(d){
        d.addEventListener('click', function(){
            goTo(parseInt(d.dataset.idx,10)); resetAuto();
        });
    });

   if (hero) {
    // Hover pause functionality removed - slider continues playing
    // hero.addEventListener('mouseenter', function(){ paused=true;  stopAuto(); });
    // hero.addEventListener('mouseleave', function(){ paused=false; startAuto(); });
    // hero.addEventListener('focusin',    function(){ paused=true;  stopAuto(); });
    // hero.addEventListener('focusout',   function(){ paused=false; startAuto(); });
}

    /* Touch swipe */
    var tx=0, moved=false;
    if (hero) {
        hero.addEventListener('touchstart', function(e){ tx=e.changedTouches[0].clientX; moved=false; },{passive:true});
        hero.addEventListener('touchmove',  function(){ moved=true; },{passive:true});
        hero.addEventListener('touchend',   function(e){
            if(!moved) return;
            var dx = e.changedTouches[0].clientX - tx;
            if(Math.abs(dx)>48){ goTo(dx<0?cur+1:cur-1); resetAuto(); }
        });
    }

    /* Keyboard */
    document.addEventListener('keydown', function(e){
        if(e.key==='ArrowLeft' ){ goTo(cur-1); resetAuto(); }
        if(e.key==='ArrowRight'){ goTo(cur+1); resetAuto(); }
    });

    /* Pause when tab hidden */
    document.addEventListener('visibilitychange', function(){
        if(document.hidden){ stopAuto(); }
        else if(!paused)   { startAuto(); }
    });

    /* Init */
    goTo(0);
    startAuto();

}());