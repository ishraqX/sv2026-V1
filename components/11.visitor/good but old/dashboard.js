/* dashboard.js — Sound Vision Analytics Dashboard */
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
        scales: {},
    };

    function gridScales() {
        return {
            x: {
                grid:  { color: 'rgba(255,255,255,0.05)' },
                ticks: { color: '#64748b', font: { family: 'Space Mono', size: 11 } },
            },
            y: {
                grid:  { color: 'rgba(255,255,255,0.05)' },
                ticks: { color: '#64748b', font: { family: 'Space Mono', size: 11 } },
                beginAtZero: true,
            },
        };
    }

    // ── Helpers ─────────────────────────────────────────────────

    function makeDonut(id, data) {
        const labels = Object.keys(data);
        const values = Object.values(data);
        if (!labels.length) return;
        const ctx = document.getElementById(id);
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: values, backgroundColor: PALETTE.slice(0, labels.length), borderWidth: 0, hoverOffset: 8 }],
            },
            options: {
                ...CHART_DEFAULTS,
                cutout: '60%',
                plugins: { ...CHART_DEFAULTS.plugins },
            },
        });
    }

    function makeBar(id, data, color) {
        const labels = Object.keys(data);
        const values = Object.values(data);
        if (!labels.length) return;
        const ctx = document.getElementById(id);
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: color || PALETTE[0],
                    borderRadius: 4,
                    borderSkipped: false,
                }],
            },
            options: {
                ...CHART_DEFAULTS,
                plugins: { ...CHART_DEFAULTS.plugins, legend: { display: false } },
                scales: gridScales(),
            },
        });
    }

    // ── Build Charts ─────────────────────────────────────────────

    function buildCharts() {
        const d = window.DB_DATA || {};

        // Daily traffic - area line chart
        const dailyLabels = Object.keys(d.last30 || {}).map(k => {
            const dt = new Date(k);
            return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
        });
        const dailyValues = Object.values(d.last30 || {});

        const ctxDaily = document.getElementById('chartDaily');
        if (ctxDaily) {
            const gradient = ctxDaily.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0,   'rgba(59,130,246,0.35)');
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
                        pointHoverRadius: 6,
                    }],
                },
                options: {
                    ...CHART_DEFAULTS,
                    plugins: { ...CHART_DEFAULTS.plugins, legend: { display: false } },
                    scales: gridScales(),
                },
            });
        }

        // Country pie (top 15)
        const cLabels = Object.keys(d.top15 || {});
        const cValues = Object.values(d.top15 || {});
        const ctxCountry = document.getElementById('chartCountry');
        if (ctxCountry && cLabels.length) {
            new Chart(ctxCountry, {
                type: 'pie',
                data: {
                    labels: cLabels,
                    datasets: [{
                        data: cValues,
                        backgroundColor: PALETTE.slice(0, cLabels.length),
                        borderWidth: 0,
                        hoverOffset: 10,
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
                                font: { family: 'Space Grotesk', size: 11 },
                                boxWidth: 10,
                                padding: 8,
                            },
                        },
                    },
                },
            });
        }

        // Device / Browser / OS (overview section)
        makeDonut('chartDevice',  d.devices  || {});
        makeDonut('chartBrowser', d.browsers || {});
        makeBar  ('chartOS',      d.os       || {}, PALETTE[4]);

        // Device section duplicates
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
                        document.querySelector('.db-page-title').textContent =
                            item.querySelector('span').textContent;
                    } else {
                        s.classList.add('db-section--hidden');
                    }
                });
            });
        });
    }

    // ── Live Online Refresh ──────────────────────────────────────

    function initLive() {
        setInterval(() => {
            fetch('ping.php?t=' + Date.now(), { cache: 'no-store' })
                .then(r => r.json())
                .then(data => {
                    document.querySelectorAll('.db-online-live').forEach(el => {
                        el.textContent = data.online ?? el.textContent;
                    });
                    const footer = document.querySelector('.db-sidebar-footer span');
                    if (footer && data.online !== undefined) {
                        footer.textContent = data.online + ' online now';
                    }
                })
                .catch(() => {});
        }, 30000);
    }

    // ── Animate KPI numbers ──────────────────────────────────────

    function animateKPIs() {
        document.querySelectorAll('.db-kpi-num').forEach(el => {
            const raw = parseInt(el.textContent.replace(/,/g, ''), 10);
            if (!raw || isNaN(raw)) return;
            let start = 0;
            const dur = 1000;
            const t0  = performance.now();
            const step = now => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = Math.round(p * raw).toLocaleString();
                if (p < 1) requestAnimationFrame(step);
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
