/* visitor_counter.js — Sound Vision Live Stats Refresh */
(function () {
    'use strict';

    // Poll online count every 30 seconds via a lightweight ping endpoint
    function refreshOnline() {
        fetch('components/visitor/ping.php?t=' + Date.now(), { cache: 'no-store' })
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('svOnlineCount');
                if (el && data.online !== undefined) {
                    el.textContent = data.online;
                }
                const tot = document.getElementById('svTotalCount');
                if (tot && data.total !== undefined) {
                    tot.textContent = Number(data.total).toLocaleString();
                }
            })
            .catch(() => {}); // silent fail
    }

    // Animate numbers on first load
    function animateNum(el, target) {
        if (!el) return;
        let start = 0;
        const duration = 1200;
        const startTime = performance.now();
        const update = (now) => {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // ease out
            const val = Math.round(progress * target);
            el.textContent = val.toLocaleString();
            if (progress < 1) requestAnimationFrame(update);
        };
        requestAnimationFrame(update);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const totalEl  = document.getElementById('svTotalCount');
        const onlineEl = document.getElementById('svOnlineCount');

        if (totalEl) {
            const raw = parseInt(totalEl.textContent.replace(/,/g, ''), 10) || 0;
            animateNum(totalEl, raw);
        }
        if (onlineEl) {
            const raw = parseInt(onlineEl.textContent, 10) || 0;
            animateNum(onlineEl, raw);
        }

        // Start polling
        setInterval(refreshOnline, 30000);
    });
})();
