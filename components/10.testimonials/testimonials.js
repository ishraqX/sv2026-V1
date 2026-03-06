/* ===== TESTIMONIALS.JS - Slider Logic ===== */

(function () {
    const AUTOPLAY_MS = 6000;

    const wrapper = document.querySelector('.testimonials-slider-wrapper');
    if (!wrapper) return;

    const slider   = wrapper.querySelector('.testimonials-slider');
    const cards    = Array.from(document.querySelectorAll('.testimonial-card'));
    const prevBtn  = wrapper.querySelector('.testimonial-prev');
    const nextBtn  = wrapper.querySelector('.testimonial-next');
    const dotsEl   = wrapper.querySelector('.testimonials-dots');

    if (!cards.length) return;

    let current   = 0;
    let timer     = null;
    let progTimer = null;
    let progBar   = null;

    /* ── Build dots ──────────────────────────────────────── */
    cards.forEach((_, i) => {
        const btn = document.createElement('button');
        btn.className = 'dot' + (i === 0 ? ' active' : '');
        btn.setAttribute('aria-label', `Go to testimonial ${i + 1}`);
        btn.addEventListener('click', () => goTo(i, i > current ? 'next' : 'prev'));
        dotsEl.appendChild(btn);
    });

    /* ── Build progress bar ──────────────────────────────── */
    progBar = document.createElement('div');
    progBar.className = 'testimonial-progress';

    /* inject into first active card */
    function attachProgress() {
        const activeCard = cards[current];
        if (!activeCard.contains(progBar)) activeCard.appendChild(progBar);
    }

    /* ── Init: show first card ───────────────────────────── */
    cards[0].classList.add('active');
    attachProgress();
    startProgress();
    startAutoplay();

    /* ── Navigation ──────────────────────────────────────── */
    prevBtn.addEventListener('click', () => { clearTimers(); goTo(prev(current), 'prev'); startAutoplay(); });
    nextBtn.addEventListener('click', () => { clearTimers(); goTo(next(current), 'next'); startAutoplay(); });

    /* ── Keyboard ────────────────────────────────────────── */
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft')  { clearTimers(); goTo(prev(current), 'prev'); startAutoplay(); }
        if (e.key === 'ArrowRight') { clearTimers(); goTo(next(current), 'next'); startAutoplay(); }
    });

    /* ── Touch / swipe ───────────────────────────────────── */
    let touchStartX = null;
    slider.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', e => {
        if (touchStartX === null) return;
        const dx = e.changedTouches[0].clientX - touchStartX;
        touchStartX = null;
        if (Math.abs(dx) < 40) return;
        clearTimers();
        if (dx < 0) goTo(next(current), 'next');
        else        goTo(prev(current), 'prev');
        startAutoplay();
    });

    /* ── Pause on hover ──────────────────────────────────── */
    wrapper.addEventListener('mouseenter', clearTimers);
    wrapper.addEventListener('mouseleave', () => { clearTimers(); startAutoplay(); });

    /* ── Core transition ─────────────────────────────────── */
    function goTo(idx, direction) {
        if (idx === current) return;

        const outCard = cards[current];
        const inCard  = cards[idx];
        const dots    = dotsEl.querySelectorAll('.dot');

        /* exit animation */
        outCard.classList.remove('active');
        outCard.classList.add(direction === 'next' ? 'exit-left' : 'exit-right');

        /* reset enter card position */
        inCard.style.transform = direction === 'next' ? 'translateX(60px) scale(0.97)' : 'translateX(-60px) scale(0.97)';
        inCard.style.opacity   = '0';

        /* force reflow then activate */
        void inCard.offsetWidth;
        inCard.classList.add('active');
        inCard.style.transform = '';
        inCard.style.opacity   = '';

        /* clean up exit card after transition */
        outCard.addEventListener('transitionend', () => {
            outCard.classList.remove('exit-left', 'exit-right');
        }, { once: true });

        /* update dots */
        dots.forEach((d, i) => d.classList.toggle('active', i === idx));

        current = idx;

        /* move progress bar */
        attachProgress();
        startProgress();
    }

    /* ── Helpers ─────────────────────────────────────────── */
    function next(i) { return (i + 1) % cards.length; }
    function prev(i) { return (i - 1 + cards.length) % cards.length; }

    function clearTimers() {
        clearInterval(timer);
        clearTimeout(progTimer);
        if (progBar) { progBar.style.transition = 'none'; progBar.style.width = '0%'; }
    }

    function startAutoplay() {
        timer = setInterval(() => goTo(next(current), 'next'), AUTOPLAY_MS);
    }

    function startProgress() {
        if (!progBar) return;
        progBar.style.transition = 'none';
        progBar.style.width = '0%';
        void progBar.offsetWidth; /* reflow */
        progBar.style.transition = `width ${AUTOPLAY_MS}ms linear`;
        progBar.style.width = '100%';
    }
})();