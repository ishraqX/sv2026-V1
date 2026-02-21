/* dashboard.js — Sound Vision Analytics Dashboard
   FIX: Access DB_DATA directly (declared as var in PHP output)
   FIX: Charts fill container height properly
   FIX: Live refresh updates sidebar + KPI online count
*/
(function () {
    'use strict';

    // ── Colour Palette ──────────────────────────────────────────
    const PALETTE = [
        '#3b82f6','#22c55e','#a855f7','#f97316','#14b8a6',
        '#ec4899','#eab308','#06b6d4','#8b5cf6','#f43f5e',
        '#10b981','#f59e0b','#6366f1','#84cc16','#0ea5e9',
    ];

    const CHART_DEFAULTS = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 800, easing: 'easeInOutQuart' },
        plugins: {
            legend: {
                labels: {
                    color: '#94a3b8',
                    font: { family: 'Space Grotesk', size: 12 },
                    boxWidth: 10,
                    padding: 12,
                },
            },
            tooltip: {
                backgroundColor: '#1e293b',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                titleColor: '#e2e8f0',
                bodyColor: '#94a3b8',
                padding: 10,
            },
        },
    };

    function gridScales() {
        return {
            x: {
                grid:  { color: 'rgba(255,255,255,0.04)' },
                ticks: { color: '#64748b', font: { family: 'Space Mono', size: 10 }, maxRotation: 45 },
            },
            y: {
                grid:  { color: 'rgba(255,255,255,0.04)' },
                ticks: { color: '#64748b', font: { family: 'Space Mono', size: 10 } },
                beginAtZero: true,
            },
        };
    }

    // ── Helpers ─────────────────────────────────────────────────

    function getCanvas(id) {
        const el = document.getElementById(id);
        return el || null;
    }

    function makeDonut(id, data) {
        const labels = Object.keys(data);
        const values = Object.values(data);
        if (!labels.length) return;
        const ctx = getCanvas(id);
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: PALETTE.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#111827',
                    hoverOffset: 10,
                }],
            },
            options: {
                ...CHART_DEFAULTS,
                cutout: '58%',
                plugins: {
                    ...CHART_DEFAULTS.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            font: { family: 'Space Grotesk', size: 11 },
                            boxWidth: 10,
                            padding: 10,
                        },
                    },
                },
            },
        });
    }

    function makeBar(id, data, color) {
        const labels = Object.keys(data);
        const values = Object.values(data);
        if (!labels.length) return;
        const ctx = getCanvas(id);
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: color || PALETTE[0],
                    borderRadius: 5,
                    borderSkipped: false,
                }],
            },
            options: {
                ...CHART_DEFAULTS,
                plugins: {
                    ...CHART_DEFAULTS.plugins,
                    legend: { display: false },
                },
                scales: gridScales(),
            },
        });
    }

    // ── Build Charts ─────────────────────────────────────────────

    function buildCharts() {
        // DB_DATA is declared as var in dashboard.php so it's on window
        const d = (typeof DB_DATA !== 'undefined') ? DB_DATA : {};

        if (!d || Object.keys(d).length === 0) {
            console.warn('[Dashboard] DB_DATA is empty or not defined');
            return;
        }

        // ── Daily traffic area line chart ──
        const dailyLabels = Object.keys(d.last30 || {}).map(k => {
            const dt = new Date(k + 'T00:00:00');
            return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
        });
        const dailyValues = Object.values(d.last30 || {});

        const ctxDaily = getCanvas('chartDaily');
        if (ctxDaily && dailyLabels.length) {
            const gradient = ctxDaily.getContext('2d').createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0,   'rgba(59,130,246,0.40)');
            gradient.addColorStop(0.6, 'rgba(59,130,246,0.10)');
            gradient.addColorStop(1,   'rgba(59,130,246,0.00)');

            new Chart(ctxDaily, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        data: dailyValues,
                        borderColor: '#3b82f6',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#111827',
                        pointBorderWidth: 1,
                        pointHoverRadius: 6,
                    }],
                },
                options: {
                    ...CHART_DEFAULTS,
                    plugins: {
                        ...CHART_DEFAULTS.plugins,
                        legend: { display: false },
                    },
                    scales: gridScales(),
                },
            });
        }

        // ── Country pie chart (top 15) ──
        const cLabels = Object.keys(d.top15 || {});
        const cValues = Object.values(d.top15 || {});
        const ctxCountry = getCanvas('chartCountry');
        if (ctxCountry && cLabels.length) {
            new Chart(ctxCountry, {
                type: 'pie',
                data: {
                    labels: cLabels,
                    datasets: [{
                        data: cValues,
                        backgroundColor: PALETTE.slice(0, cLabels.length),
                        borderWidth: 2,
                        borderColor: '#111827',
                        hoverOffset: 12,
                    }],
                },
                options: {
                    ...CHART_DEFAULTS,
                    plugins: {
                        ...CHART_DEFAULTS.plugins,
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                font: { family: 'Space Grotesk', size: 10 },
                                boxWidth: 10,
                                padding: 6,
                            },
                        },
                    },
                },
            });
        }

        // ── Overview section: Device / Browser / OS ──
        makeDonut('chartDevice',  d.devices  || {});
        makeDonut('chartBrowser', d.browsers || {});
        makeBar  ('chartOS',      d.os       || {}, PALETTE[4]);

        // ── Devices section duplicates ──
        makeDonut('chartDeviceSec',  d.devices  || {});
        makeDonut('chartBrowserSec', d.browsers || {});
        makeDonut('chartOSSec',      d.os       || {});
    }

    // ── Navigation ──────────────────────────────────────────────

    function initNav() {
        const navItems = document.querySelectorAll('.db-nav-item');
        const sections = document.querySelectorAll('.db-section');

        navItems.forEach(item => {
            item.addEventListener('click', e => {
                e.preventDefault();
                const target = item.dataset.section;

                navItems.forEach(n => n.classList.remove('active'));
                item.classList.add('active');

                sections.forEach(s => {
                    if (s.id === target) {
                        s.classList.remove('db-section--hidden');
                        const label = item.querySelector('span');
                        if (label) {
                            document.querySelector('.db-page-title').textContent = label.textContent;
                        }
                    } else {
                        s.classList.add('db-section--hidden');
                    }
                });
            });
        });
    }

    // ── Live Online Refresh ──────────────────────────────────────

    function initLive() {
        function refresh() {
            fetch('ping.php?t=' + Date.now(), { cache: 'no-store' })
                .then(r => r.json())
                .then(data => {
                    if (data.online !== undefined) {
                        // Update KPI card
                        document.querySelectorAll('.db-online-live').forEach(el => {
                            el.textContent = data.online;
                        });
                        // Update sidebar footer
                        const sidebar = document.getElementById('sidebarOnline');
                        if (sidebar) sidebar.textContent = data.online + ' online now';
                        // Update HUD
                        const hudLive = document.querySelector('.map-hud-tl .map-hud-line:nth-child(3) span');
                        if (hudLive) hudLive.textContent = data.online;
                    }
                    if (data.total !== undefined) {
                        const tot = document.getElementById('svTotalCount');
                        if (tot) tot.textContent = Number(data.total).toLocaleString();
                    }
                })
                .catch(() => {});
        }

        // Poll every 30 seconds
        setInterval(refresh, 30000);
    }

    // ── Animate KPI numbers ──────────────────────────────────────

    function animateKPIs() {
        document.querySelectorAll('.db-kpi-num').forEach(el => {
            const raw = parseInt(el.textContent.replace(/,/g, ''), 10);
            if (!raw || isNaN(raw)) return;
            const target = raw;
            const dur = 900;
            const t0  = performance.now();
            const step = now => {
                const p = Math.min((now - t0) / dur, 1);
                const eased = 1 - Math.pow(1 - p, 3); // ease out cubic
                el.textContent = Math.round(eased * target).toLocaleString();
                if (p < 1) requestAnimationFrame(step);
                else el.textContent = target.toLocaleString();
            };
            requestAnimationFrame(step);
        });
    }

    // ── Init ─────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', () => {
        buildCharts();
        initNav();
        initLive();
        animateKPIs();
    });
})();
