<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">

<!-- ══ NAV BAR ══ -->
<nav class="pnav" id="pnav" role="navigation" aria-label="Main navigation">

    <div class="pnav-accent-line" aria-hidden="true"></div>

    <div class="pnav-inner">

        <!-- Logo -->
        <a href="#home" class="pnav-logo" aria-label="Sound Vision — Home">
            <img src="assets/images/logo.png" alt="Sound Vision" class="pnav-logo-img">
        </a>

        <!-- Desktop links -->
        <ul class="pnav-links" role="list">
            <li><a href="#hero"         class="pnav-link" data-label="Home">Home</a></li>
            <li><a href="#sdg"          class="pnav-link" data-label="Our Impact">Our Impact</a></li>
            <li><a href="#features"     class="pnav-link" data-label="Solutions">Solutions</a></li>
            <li><a href="#media"        class="pnav-link" data-label="Media">Media</a></li>
            <li><a href="#team"         class="pnav-link" data-label="Team">Team</a></li>
            <li><a href="#download"     class="pnav-link" data-label="Download">Download</a></li>
            <li><a href="#about"        class="pnav-link" data-label="About Us">About Us</a></li>
            <li><a href="#awards"       class="pnav-link" data-label="Awards">Awards</a></li>
            <li><a href="#testimonials" class="pnav-link" data-label="Testimonials">Testimonials</a></li>
        </ul>

        <!-- Desktop CTA -->
        <div class="pnav-actions">
            <a href="#download" class="pnav-cta">
                <span>Get App</span>
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                    <path d="M2 7h10M7 2l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        <!-- Mobile trigger -->
        <button class="pnav-trigger" id="pnavTrigger" aria-label="Open menu" aria-expanded="false" aria-controls="pnavPanel">
            <span class="pnav-trigger-label">Menu</span>
            <span class="pnav-trigger-icon" aria-hidden="true">
                <span class="pnav-tbar"></span>
                <span class="pnav-tbar pnav-tbar--short"></span>
            </span>
        </button>

    </div>
</nav>

<!-- ══ PANEL & BACKDROP — outside <nav> to escape its stacking context ══ -->
<div class="pnav-panel" id="pnavPanel" aria-hidden="true" role="dialog" aria-modal="true">

    <div class="pnav-panel-top">
        <a href="#home" class="pnav-logo" onclick="pnavClose()">
            <img src="assets/images/logo.png" alt="Sound Vision" class="pnav-logo-img pnav-logo-img--panel">
        </a>
        <button class="pnav-panel-close" id="pnavClose" aria-label="Close menu">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M4 4l12 12M16 4L4 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <nav class="pnav-panel-nav" aria-label="Mobile navigation">
        <ul class="pnav-panel-list" role="list">
            <li style="--pi:0"><a href="#home"         class="pnav-panel-link"><span class="pnav-panel-num">01</span><span class="pnav-panel-name">Home</span></a></li>
            <li style="--pi:1"><a href="#sdg"          class="pnav-panel-link"><span class="pnav-panel-num">02</span><span class="pnav-panel-name">Our Impact</span></a></li>
            <li style="--pi:2"><a href="#features"     class="pnav-panel-link"><span class="pnav-panel-num">03</span><span class="pnav-panel-name">Solutions</span></a></li>
            <li style="--pi:3"><a href="#media"        class="pnav-panel-link"><span class="pnav-panel-num">04</span><span class="pnav-panel-name">Media</span></a></li>
            <li style="--pi:4"><a href="#team"         class="pnav-panel-link"><span class="pnav-panel-num">05</span><span class="pnav-panel-name">Team</span></a></li>
            <li style="--pi:5"><a href="#download"     class="pnav-panel-link"><span class="pnav-panel-num">06</span><span class="pnav-panel-name">Download</span></a></li>
            <li style="--pi:6"><a href="#about"        class="pnav-panel-link"><span class="pnav-panel-num">07</span><span class="pnav-panel-name">About Us</span></a></li>
            <li style="--pi:7"><a href="#awards"       class="pnav-panel-link"><span class="pnav-panel-num">08</span><span class="pnav-panel-name">Awards</span></a></li>
            <li style="--pi:8"><a href="#testimonials" class="pnav-panel-link"><span class="pnav-panel-num">09</span><span class="pnav-panel-name">Testimonials</span></a></li>
        </ul>
    </nav>

    <div class="pnav-panel-footer">
        <a href="#download" class="pnav-panel-cta" onclick="pnavClose()">Get the App →</a>
        <p class="pnav-panel-tagline">Sound Vision · Transforming Healthcare</p>
    </div>

</div>

<!-- Backdrop — also outside <nav> -->
<div class="pnav-backdrop" id="pnavBackdrop" aria-hidden="true"></div>

<script>
(function () {
    'use strict';

    var nav      = document.getElementById('pnav');
    var trigger  = document.getElementById('pnavTrigger');
    var panel    = document.getElementById('pnavPanel');
    var closeBtn = document.getElementById('pnavClose');
    var backdrop = document.getElementById('pnavBackdrop');
    var panelLinks = panel.querySelectorAll('.pnav-panel-link');

    function openPanel() {
        panel.classList.add('pnav-panel--open');
        backdrop.classList.add('pnav-backdrop--show');
        trigger.classList.add('pnav-trigger--open');
        trigger.setAttribute('aria-expanded', 'true');
        panel.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closePanel() {
        panel.classList.remove('pnav-panel--open');
        backdrop.classList.remove('pnav-backdrop--show');
        trigger.classList.remove('pnav-trigger--open');
        trigger.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    /* Expose closePanel globally for onclick on logo/cta inside panel */
    window.pnavClose = closePanel;

    trigger.addEventListener('click', function () {
        panel.classList.contains('pnav-panel--open') ? closePanel() : openPanel();
    });

    closeBtn.addEventListener('click', closePanel);
    backdrop.addEventListener('click', closePanel);
    panelLinks.forEach(function (l) { l.addEventListener('click', closePanel); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePanel();
    });

    /* Scroll state */
    var ticking = false;
    window.addEventListener('scroll', function () {
        if (!ticking) {
            requestAnimationFrame(function () {
                nav.classList.toggle('pnav--scrolled', window.scrollY > 48);
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    /* Active section highlight */
    var allLinks = document.querySelectorAll('.pnav-link, .pnav-panel-link');
    var sections = Array.from(document.querySelectorAll('section[id], div[id]'));

    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) {
                var id = '#' + e.target.id;
                allLinks.forEach(function (l) {
                    l.classList.toggle('pnav-link--active', l.getAttribute('href') === id);
                });
            }
        });
    }, { rootMargin: '-20% 0px -60% 0px' });

    sections.forEach(function (s) { io.observe(s); });

})();
</script>