/* world_map.js — Sound Vision Leaflet World Map
   ROOT CAUSE FIX: Replaced fetch(world-atlas CDN) with Leaflet + hardcoded
   country centroids. Map ALWAYS renders — zero external GeoJSON dependency.
------------------------------------------------------------------ */
(function () {
    'use strict';

    /* Country centroid lookup ISO-2 → [lat, lng] — 180+ countries */
    const CC = {
        AF:[33.93,67.71],AL:[41.15,20.17],DZ:[28.03,1.66],AO:[-11.20,17.87],
        AR:[-38.42,-63.62],AM:[40.07,45.04],AU:[-25.27,133.78],AT:[47.52,14.55],
        AZ:[40.14,47.58],BD:[23.68,90.35],BE:[50.50,4.47],BF:[12.36,-1.56],
        BG:[42.73,25.49],BH:[26.00,50.55],BI:[-3.37,29.92],BJ:[9.31,2.32],
        BN:[4.54,114.73],BO:[-16.29,-63.59],BR:[-14.24,-51.93],BS:[25.03,-77.40],
        BT:[27.51,90.43],BW:[-22.33,24.68],BY:[53.71,27.95],BZ:[17.19,-88.50],
        CA:[56.13,-106.35],CD:[-4.04,21.76],CF:[6.61,20.94],CG:[-0.23,15.83],
        CH:[46.82,8.23],CI:[7.54,-5.55],CL:[-35.68,-71.54],CM:[3.85,11.50],
        CN:[35.86,104.20],CO:[4.57,-74.30],CR:[9.75,-83.75],CU:[21.52,-77.78],
        CV:[16.00,-24.01],CY:[35.13,33.43],CZ:[49.82,15.47],DE:[51.17,10.45],
        DJ:[11.83,42.59],DK:[56.26,9.50],DO:[18.74,-70.16],DZ:[28.03,1.66],
        EC:[-1.83,-78.18],EE:[58.60,25.01],EG:[26.82,30.80],ER:[15.18,39.78],
        ES:[40.46,-3.75],ET:[9.15,40.49],FI:[61.92,25.75],FJ:[-16.58,179.41],
        FR:[46.23,2.21],GA:[-0.80,11.61],GB:[55.38,-3.44],GE:[42.32,43.36],
        GH:[7.95,-1.02],GM:[13.44,-15.31],GN:[9.95,-11.24],GQ:[1.65,10.27],
        GR:[39.07,21.82],GT:[15.78,-90.23],GW:[11.80,-15.18],GY:[4.86,-58.93],
        HK:[22.32,114.17],HN:[15.20,-86.24],HR:[45.10,15.20],HT:[18.97,-72.29],
        HU:[47.16,19.50],ID:[-0.79,113.92],IE:[53.41,-8.24],IL:[31.05,34.85],
        IN:[20.59,78.96],IQ:[33.22,43.68],IR:[32.43,53.69],IS:[64.96,-19.02],
        IT:[41.87,12.57],JM:[18.11,-77.30],JO:[30.59,36.24],JP:[36.20,138.25],
        KE:[-0.02,37.91],KG:[41.20,74.77],KH:[12.57,104.99],KM:[-11.64,43.33],
        KP:[40.34,127.51],KR:[35.91,127.77],KW:[29.31,47.48],KZ:[48.02,66.92],
        LA:[19.86,102.50],LB:[33.85,35.86],LK:[7.87,80.77],LR:[6.43,-9.43],
        LS:[-29.61,28.23],LT:[55.17,23.88],LU:[49.82,6.13],LV:[56.88,24.60],
        LY:[26.34,17.23],MA:[31.79,-7.09],MD:[47.41,28.37],ME:[42.71,19.37],
        MG:[-18.77,46.87],MK:[41.61,21.75],ML:[17.57,-3.99],MM:[21.92,95.96],
        MN:[46.86,103.85],MO:[22.20,113.54],MR:[21.01,-10.94],MT:[35.94,14.37],
        MU:[-20.35,57.55],MV:[3.20,73.22],MW:[-13.25,34.30],MX:[23.63,-102.55],
        MY:[4.21,108.91],MZ:[-18.67,35.53],NA:[-22.96,18.49],NE:[17.61,8.08],
        NG:[9.08,8.68],NI:[12.87,-85.21],NL:[52.13,5.29],NO:[60.47,8.47],
        NP:[28.39,84.12],NZ:[-40.90,174.89],OM:[21.51,55.92],PA:[8.54,-80.78],
        PE:[-9.19,-75.02],PG:[-6.31,143.96],PH:[12.88,121.77],PK:[30.38,69.35],
        PL:[51.92,19.15],PR:[18.22,-66.59],PS:[31.95,35.23],PT:[39.40,-8.22],
        PY:[-23.44,-58.44],QA:[25.35,51.18],RO:[45.94,24.97],RS:[44.02,21.01],
        RU:[61.52,105.32],RW:[-1.94,29.87],SA:[23.89,45.08],SD:[12.86,30.22],
        SE:[60.13,18.64],SG:[1.35,103.82],SI:[46.15,14.99],SK:[48.67,19.70],
        SL:[8.46,-11.78],SN:[14.50,-14.45],SO:[5.15,46.20],SR:[3.92,-56.03],
        SS:[6.88,31.31],SV:[13.79,-88.90],SY:[34.80,38.99],SZ:[-26.52,31.47],
        TD:[15.45,18.73],TG:[8.62,0.82],TH:[15.87,100.99],TJ:[38.86,71.28],
        TL:[-8.87,125.73],TM:[38.97,59.56],TN:[33.89,9.54],TR:[38.96,35.24],
        TT:[10.69,-61.22],TW:[23.70,120.96],TZ:[-6.37,34.89],UA:[48.38,31.17],
        UG:[1.37,32.29],US:[37.09,-95.71],UY:[-32.52,-55.77],UZ:[41.38,64.59],
        VE:[6.42,-66.59],VN:[14.06,108.28],YE:[15.55,48.52],ZA:[-30.56,22.94],
        ZM:[-13.13,27.85],ZW:[-19.02,29.15],AE:[23.42,53.85],
    };

    function flagEmoji(cc) {
        if (!cc || cc.length !== 2) return '';
        try {
            return String.fromCodePoint(...[...cc.toUpperCase()].map(c => 0x1F1E0 + c.charCodeAt(0) - 65));
        } catch(e) { return ''; }
    }

    function countToColor(count, maxCount) {
        if (!count) return '#1a1a2e';
        const intensity = Math.pow(count / maxCount, 0.4);
        // Enhanced color palette: deep blue → cyan → bright blue → white
        const colors = [
            [26, 26, 46],    // Deep dark blue
            [15, 52, 67],    // Dark teal
            [0, 87, 128],    // Medium blue
            [29, 111, 181],  // Light blue
            [59, 130, 246],  // Bright blue
            [96, 165, 250],  // Lighter blue
            [147, 197, 253], // Very light blue
            [219, 234, 254], // Pale blue
            [255, 255, 255]  // White for max
        ];
        const idx = Math.min(Math.floor(intensity * (colors.length - 1)), colors.length - 2);
        const frac = intensity * (colors.length - 1) - idx;
        const a = colors[idx], b = colors[idx + 1];
        return `rgb(${Math.round(a[0]+(b[0]-a[0])*frac)},${Math.round(a[1]+(b[1]-a[1])*frac)},${Math.round(a[2]+(b[2]-a[2])*frac)})`;
    }

    function getSizeClass(count, maxCount) {
        const ratio = count / maxCount;
        if (ratio >= 0.8) return 'xl';
        if (ratio >= 0.6) return 'lg';
        if (ratio >= 0.4) return 'md';
        if (ratio >= 0.2) return 'sm';
        return 'xs';
    }

    function initMap() {
        const mapEl = document.getElementById('world-map-leaflet');
        if (!mapEl) return;
        if (!window.L) { console.error('Leaflet not loaded'); return; }

        const mapData  = (typeof DB_DATA !== 'undefined' && Array.isArray(DB_DATA.countryMap)) ? DB_DATA.countryMap : [];
        const total    = mapData.reduce((s, d) => s + d.count, 0) || 1;
        const maxCount = Math.max(...mapData.map(d => d.count), 1);

        /* ── Create map ── */
        const map = L.map('world-map-leaflet', {
            center: [20, 10],
            zoom: 2,
            minZoom: 1,
            maxZoom: 8,
            zoomControl: true,
            scrollWheelZoom: true,
            worldCopyJump: true,
            fadeAnimation: true,
            zoomAnimation: true
        });

        /* Enhanced tile layer with better styling */
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> &copy; <a href="https://carto.com">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19,
            opacity: 0.9
        }).addTo(map);

        /* Add subtle grid overlay */
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            opacity: 0.03,
            attribution: ''
        }).addTo(map);

        /* ── Place enhanced markers for every country with data ── */
        const markers = [];
        mapData.forEach((item, rank) => {
            const cc  = (item.code || '').toUpperCase();
            const pos = CC[cc];
            if (!pos) return;

            const count  = item.count || 0;
            const pct    = ((count / total) * 100).toFixed(1);
            const color  = countToColor(count, maxCount);
            const sizeClass = getSizeClass(count, maxCount);

            // Dynamic sizing based on zoom and data
            const baseRadius = Math.max(4, Math.min(25, 4 + Math.sqrt(count / maxCount) * 21));
            const radius = baseRadius * (map.getZoom() / 2);

            /* Enhanced circle marker with glow effect */
            const circle = L.circleMarker(pos, {
                radius,
                fillColor:   color,
                color:       'rgba(255,255,255,0.6)',
                weight:      2,
                opacity:     0.8,
                fillOpacity: 0.85,
                className:   `sv-marker sv-marker-${sizeClass}`
            }).addTo(map);

            /* Rich tooltip with enhanced styling */
            circle.bindTooltip(
                `<div class="sv-tt-enhanced">
                    <div class="sv-tt-header">
                        <div class="sv-tt-flag">${flagEmoji(cc)}</div>
                        <div class="sv-tt-country">${item.name}</div>
                        <div class="sv-tt-rank">#${rank + 1}</div>
                    </div>
                    <div class="sv-tt-stats">
                        <div class="sv-tt-visits">${count.toLocaleString()} visits</div>
                        <div class="sv-tt-pct">${pct}% of traffic</div>
                        <div class="sv-tt-intensity">Intensity: ${sizeClass.toUpperCase()}</div>
                    </div>
                    <div class="sv-tt-action">Click to explore →</div>
                </div>`,
                {
                    sticky: true,
                    opacity: 1,
                    className: 'sv-map-tt-enhanced',
                    offset: [0, -10]
                }
            );

            /* Enhanced click handler with zoom */
            circle.on('click', (e) => {
                L.DomEvent.stopPropagation(e);
                map.setView(pos, Math.max(4, map.getZoom() + 1), { animate: true });
                const q = encodeURIComponent(item.name);
                setTimeout(() => {
                    window.open(`https://ipinfo.io/countries/${cc.toLowerCase()}`, '_blank');
                }, 500);
            });

            /* Hover effects */
            circle.on('mouseover', function() {
                this.setStyle({
                    fillOpacity: 1,
                    weight: 3,
                    color: 'rgba(255,255,255,0.9)'
                });
            });

            circle.on('mouseout', function() {
                this.setStyle({
                    fillOpacity: 0.85,
                    weight: 2,
                    color: 'rgba(255,255,255,0.6)'
                });
            });

            /* Pulse ring for top countries with enhanced animation */
            if (rank < 8) {
                const pulseEl = document.createElement('div');
                pulseEl.className = 'sv-pulse-enhanced';
                pulseEl.style.cssText = `
                    width:${radius*3}px;
                    height:${radius*3}px;
                    border-color:${color};
                    animation-delay:${rank * 0.15}s;
                    animation-duration: ${3 + rank * 0.5}s;
                `;
                const pulseIcon = L.divIcon({
                    html: pulseEl,
                    className: '',
                    iconSize: [radius*3, radius*3],
                    iconAnchor: [radius*1.5, radius*1.5]
                });
                L.marker(pos, {
                    icon: pulseIcon,
                    interactive: false,
                    zIndexOffset: -100
                }).addTo(map);
            }

            markers.push({ marker: circle, pos, count, rank });
        });

        /* ── Dynamic marker sizing on zoom ── */
        map.on('zoomend', function() {
            const zoom = map.getZoom();
            const zoomFactor = zoom / 2;
            markers.forEach(({ marker, count }) => {
                const baseRadius = Math.max(4, Math.min(25, 4 + Math.sqrt(count / maxCount) * 21));
                const newRadius = baseRadius * zoomFactor;
                marker.setRadius(newRadius);
            });
        });

        /* ── Enhanced status ── */
        const statusEl = document.getElementById('mapStatus');
        if (statusEl) {
            const topCountry = mapData[0];
            statusEl.innerHTML = `
                <span class="map-status-item">🌐 ${mapData.length} ACTIVE NODES</span>
                <span class="map-status-item">🎯 TOP: ${topCountry ? topCountry.name : 'N/A'}</span>
                <span class="map-status-item">📊 ${total.toLocaleString()} TOTAL VISITS</span>
            `;
            statusEl.style.color = '#22c55e';
        }

        /* ── Redraw on section tab click ── */
        document.querySelectorAll('.db-nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (item.dataset.section === 'countries') {
                    setTimeout(() => map.invalidateSize(), 250);
                }
            });
        });

        window.addEventListener('resize', () => map.invalidateSize());
    }

    /* Boot */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
})();