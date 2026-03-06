/* ===== TESTIMONIALS.JS ===== */

(function () {
    const AUTOPLAY_MS = 6000;

    const wrapper  = document.querySelector('.testimonials-slider-wrapper');
    if (!wrapper) return;

    const slider   = wrapper.querySelector('.testimonials-slider');
    const cards    = Array.from(wrapper.querySelectorAll('.testimonial-card'));
    const prevBtn  = wrapper.querySelector('.testimonial-prev');
    const nextBtn  = wrapper.querySelector('.testimonial-next');
    const dotsWrap = wrapper.querySelector('.testimonials-dots');
    const counter  = wrapper.querySelector('.t-counter');

    if (!cards.length) return;

    let current = 0;
    let autoTimer = null;

    /* ── Build dots ──────────────────────────────────────────── */
    cards.forEach((_, i) => {
        const btn = document.createElement('button');
        btn.className = 't-dot' + (i === 0 ? ' active' : '');
        btn.setAttribute('aria-label', 'Go to testimonial ' + (i + 1));
        btn.addEventListener('click', () => { clearAuto(); goTo(i, i > current ? 'next' : 'prev'); startAuto(); });
        dotsWrap.appendChild(btn);
    });

    /* ── Init ────────────────────────────────────────────────── */
    cards[0].classList.add('active');
    updateCounter(0);
    startProgress(cards[0]);
    startAuto();

    /* ── Button listeners ────────────────────────────────────── */
    prevBtn.addEventListener('click', () => { clearAuto(); goTo(prev(current), 'prev'); startAuto(); });
    nextBtn.addEventListener('click', () => { clearAuto(); goTo(next(current), 'next'); startAuto(); });

    /* ── Keyboard ────────────────────────────────────────────── */
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft')  { clearAuto(); goTo(prev(current), 'prev'); startAuto(); }
        if (e.key === 'ArrowRight') { clearAuto(); goTo(next(current), 'next'); startAuto(); }
    });

    /* ── Touch swipe ─────────────────────────────────────────── */
    let tx = null;
    slider.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', e => {
        if (tx === null) return;
        const dx = e.changedTouches[0].clientX - tx;
        tx = null;
        if (Math.abs(dx) < 40) return;
        clearAuto();
        dx < 0 ? goTo(next(current), 'next') : goTo(prev(current), 'prev');
        startAuto();
    });

    /* ── Pause on hover ──────────────────────────────────────── */
    wrapper.addEventListener('mouseenter', clearAuto);
    wrapper.addEventListener('mouseleave', () => { clearAuto(); startAuto(); });

    /* ── Core transition ─────────────────────────────────────── */
    function goTo(idx, dir) {
        if (idx === current) return;

        const outCard = cards[current];
        const inCard  = cards[idx];
        const dots    = dotsWrap.querySelectorAll('.t-dot');

        /* stop outgoing progress */
        const outProg = outCard.querySelector('.testimonial-progress');
        if (outProg) { outProg.style.transition = 'none'; outProg.style.width = '0%'; }

        /* exit */
        outCard.classList.remove('active');
        outCard.classList.add(dir === 'next' ? 'exit-left' : 'exit-right');

        /* position entering card off-screen */
        inCard.style.transform = dir === 'next' ? 'translateX(50px)' : 'translateX(-50px)';
        inCard.style.opacity = '0';
        void inCard.offsetWidth; /* reflow */

        inCard.classList.add('active');
        inCard.style.transform = '';
        inCard.style.opacity = '';

        outCard.addEventListener('transitionend', () => {
            outCard.classList.remove('exit-left', 'exit-right');
        }, { once: true });

        /* update dots + counter */
        dots.forEach((d, i) => d.classList.toggle('active', i === idx));
        current = idx;
        updateCounter(idx);
        startProgress(inCard);
    }

    function startProgress(card) {
        const bar = card.querySelector('.testimonial-progress');
        if (!bar) return;
        bar.style.transition = 'none';
        bar.style.width = '0%';
        void bar.offsetWidth;
        bar.style.transition = 'width ' + AUTOPLAY_MS + 'ms linear';
        bar.style.width = '100%';
    }

    function updateCounter(idx) {
        if (!counter) return;
        const pad = n => String(n).padStart(2, '0');
        counter.textContent = pad(idx + 1) + ' / ' + pad(cards.length);
    }

    function next(i) { return (i + 1) % cards.length; }
    function prev(i) { return (i - 1 + cards.length) % cards.length; }

    function clearAuto() {
        clearInterval(autoTimer);
        /* freeze current progress bar */
        const bar = cards[current].querySelector('.testimonial-progress');
        if (bar) {
            const w = bar.getBoundingClientRect().width;
            const pw = bar.parentElement.getBoundingClientRect().width;
            bar.style.transition = 'none';
            bar.style.width = (w / pw * 100) + '%';
        }
    }

    function startAuto() {
        /* resume progress */
        const bar = cards[current].querySelector('.testimonial-progress');
        if (bar) {
            void bar.offsetWidth;
            const remaining = AUTOPLAY_MS * (1 - parseFloat(bar.style.width) / 100);
            bar.style.transition = 'width ' + Math.max(remaining, 0) + 'ms linear';
            bar.style.width = '100%';
        }
        autoTimer = setInterval(() => goTo(next(current), 'next'), AUTOPLAY_MS);
    }
})();