<?php
// ============================================================
// dashboard.php — Sound Vision Analytics Dashboard
// FIXES: Charts data injection, full IP display, IP visit counts,
//        hacker-style world map, professional visitors log
// ============================================================

define('SV_DASH_PASS', 'soundvision2024');

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sv_pass'])) {
    if ($_POST['sv_pass'] === SV_DASH_PASS) {
        $_SESSION['sv_admin']     = true;
        $_SESSION['sv_admin_exp'] = time() + 3600;
    } else {
        $sv_login_error = true;
    }
}

if (!empty($_SESSION['sv_admin_exp']) && time() > $_SESSION['sv_admin_exp']) {
    unset($_SESSION['sv_admin'], $_SESSION['sv_admin_exp']);
}

if (isset($_GET['logout'])) {
    unset($_SESSION['sv_admin'], $_SESSION['sv_admin_exp']);
    header('Location: dashboard.php'); exit;
}

$sv_is_local = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1','::1'], true);
if (empty($_SESSION['sv_admin']) && !$sv_is_local) {
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Login — Sound Vision</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Space Grotesk',sans-serif;background:#080c14;display:flex;align-items:center;justify-content:center;min-height:100vh;color:#e2e8f0}
.login-box{background:#0f172a;border:1px solid rgba(59,130,246,0.2);border-radius:16px;padding:48px 40px;width:100%;max-width:400px;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,0.5)}
.login-icon{font-size:40px;margin-bottom:16px}
h1{font-size:22px;font-weight:700;margin-bottom:6px}
p{font-size:13px;color:#64748b;margin-bottom:32px}
input[type=password]{width:100%;padding:12px 16px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);background:#1e293b;color:#f1f5f9;font-size:14px;font-family:inherit;margin-bottom:12px;outline:none;transition:border-color .2s}
input[type=password]:focus{border-color:#3b82f6}
button{width:100%;padding:12px;border-radius:8px;border:none;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;transition:opacity .2s}
button:hover{opacity:.85}
.error{background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:8px;padding:10px;font-size:13px;margin-bottom:14px}
</style>
</head>
<body>
<div class="login-box">
    <div class="login-icon">🔐</div>
    <h1>Analytics Dashboard</h1>
    <p>Sound Vision — Admin Only</p>
    <?php if (!empty($sv_login_error)): ?>
    <div class="error">❌ Wrong password. Try again.</div>
    <?php endif; ?>
    <form method="POST">
        <input type="password" name="sv_pass" placeholder="Enter dashboard password" autofocus>
        <button type="submit">Access Dashboard</button>
    </form>
</div>
</body>
</html><?php
    exit;
}

// ── Data files ───────────────────────────────────────────────
$visitorFile  = __DIR__ . '/data/visitor.txt';
$userDataFile = __DIR__ . '/data/user-data.txt';
$onlineFile   = __DIR__ . '/data/online-user.txt';

// ── Load data ─────────────────────────────────────────────────

$totalVisitors = file_exists($visitorFile) ? (int)trim(file_get_contents($visitorFile)) : 0;

$onlineCount = 0;
if (file_exists($onlineFile)) {
    $now = time(); $window = 300;
    $ol  = file($onlineFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($ol as $l) {
        [, $ts] = array_pad(explode('|', $l, 2), 2, 0);
        if ($now - (int)$ts < $window) $onlineCount++;
    }
}

$rows = [];
if (file_exists($userDataFile)) {
    $lines = file($userDataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $l) {
        $p = explode('|', $l);
        if (count($p) >= 11) {
            $rows[] = [
                'datetime'    => $p[0],
                'ip'          => $p[1],
                'country'     => $p[2],
                'countryCode' => $p[3],
                'city'        => $p[4],
                'region'      => $p[5],
                'device'      => $p[6],
                'os'          => $p[7],
                'browser'     => $p[8],
                'referer'     => $p[9],
                'page'        => $p[10],
            ];
        }
    }
}

$totalUnique = count($rows);

// Aggregate stats
$countries = []; $devices = []; $browsers = []; $os_list = []; $daily = [];
$ipCounts  = []; // IP visit frequency
$todayStr  = date('Y-m-d');
$todayCount = 0;

// Country code lookup map
$countryCodeMap = [];

foreach ($rows as $r) {
    $c = $r['country'] ?: 'Unknown';
    $countries[$c] = ($countries[$c] ?? 0) + 1;
    if (!isset($countryCodeMap[$c])) $countryCodeMap[$c] = $r['countryCode'];

    $d = $r['device']  ?: 'Unknown';
    $devices[$d] = ($devices[$d] ?? 0) + 1;

    $b = $r['browser'] ?: 'Unknown';
    $browsers[$b] = ($browsers[$b] ?? 0) + 1;

    $o = $r['os'] ?: 'Unknown';
    $os_list[$o] = ($os_list[$o] ?? 0) + 1;

    $day = substr($r['datetime'], 0, 10);
    $daily[$day] = ($daily[$day] ?? 0) + 1;
    if ($day === $todayStr) $todayCount++;

    // IP frequency
    $ip = $r['ip'];
    if (!isset($ipCounts[$ip])) {
        $ipCounts[$ip] = ['count' => 0, 'country' => $c, 'countryCode' => $r['countryCode'], 'city' => $r['city'], 'device' => $r['device'], 'browser' => $r['browser'], 'last' => $r['datetime']];
    }
    $ipCounts[$ip]['count']++;
    $ipCounts[$ip]['last'] = $r['datetime'];
}

arsort($countries); arsort($devices); arsort($browsers); arsort($os_list);
ksort($daily);
arsort($ipCounts);

// Top 15 countries for pie chart
$top15 = array_slice($countries, 0, 15, true);

// Last 30 days for chart
$last30 = [];
for ($i = 29; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $last30[$d] = $daily[$d] ?? 0;
}

// Recent visitors (last 100, newest first)
$recent = array_slice(array_reverse($rows), 0, 100);

// Top IPs (top 50)
$topIPs = array_slice($ipCounts, 0, 50, true);

// Encode for JS
$top15Json  = json_encode($top15,    JSON_UNESCAPED_UNICODE);
$last30Json = json_encode($last30,   JSON_UNESCAPED_UNICODE);
$devJson    = json_encode($devices,  JSON_UNESCAPED_UNICODE);
$browJson   = json_encode($browsers, JSON_UNESCAPED_UNICODE);
$osJson     = json_encode($os_list,  JSON_UNESCAPED_UNICODE);

// Country data with codes for map
$countryMapData = [];
foreach ($countries as $cn => $cv) {
    $countryMapData[] = [
        'name'  => $cn,
        'code'  => $countryCodeMap[$cn] ?? 'XX',
        'count' => $cv,
    ];
}
$countryMapJson = json_encode($countryMapData, JSON_UNESCAPED_UNICODE);

function flag(string $cc): string {
    if (strlen($cc) !== 2 || in_array(strtoupper($cc), ['XX','LO'])) return '🌐';
    return implode('', array_map(fn($c) => mb_chr(0x1F1E0 + ord($c) - ord('A')), str_split(strtoupper($cc))));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard — Sound Vision</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- D3 for world map -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/topojson-client@3.1.0/dist/topojson.min.js"></script>
    <style>
    /* ── Map Section Styles ── */
    #map-container {
        position: relative;
        background: #060a12;
        border-radius: 8px;
        overflow: hidden;
        min-height: 420px;
    }
    #world-map {
        width: 100%;
        display: block;
    }
    #world-map .country-path {
        fill: #0d1f35;
        stroke: #1a3a5c;
        stroke-width: 0.5;
        transition: fill 0.3s;
        cursor: pointer;
    }
    #world-map .country-path:hover {
        stroke: #3b82f6;
        stroke-width: 1.5;
    }
    #world-map .country-path.has-data {
        fill: #0d3a6e;
    }
    /* Graticule */
    .graticule { fill: none; stroke: #0a1628; stroke-width: 0.3; }
    .sphere { fill: #040810; }

    /* Hacker scan line overlay */
    #map-container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #3b82f6, transparent);
        animation: map-scan 4s linear infinite;
        z-index: 5;
        pointer-events: none;
    }
    @keyframes map-scan {
        0%   { top: 0; opacity: 0; }
        5%   { opacity: 1; }
        95%  { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }

    /* Map corner HUD elements */
    .map-hud {
        position: absolute;
        z-index: 10;
        font-family: 'Space Mono', monospace;
        font-size: 10px;
        color: rgba(59,130,246,0.7);
        pointer-events: none;
    }
    .map-hud-tl { top: 12px; left: 16px; }
    .map-hud-tr { top: 12px; right: 16px; text-align: right; }
    .map-hud-bl { bottom: 12px; left: 16px; }
    .map-hud-br { bottom: 12px; right: 16px; text-align: right; }
    .map-hud-line { display: block; margin: 2px 0; }
    .map-hud-line span { color: #22c55e; }

    /* Map tooltip */
    #map-tooltip {
        position: absolute;
        background: rgba(6,10,18,0.95);
        border: 1px solid rgba(59,130,246,0.4);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12px;
        color: #e2e8f0;
        pointer-events: none;
        z-index: 20;
        display: none;
        min-width: 160px;
        backdrop-filter: blur(8px);
    }
    #map-tooltip .tt-country { font-weight: 700; font-size: 14px; margin-bottom: 4px; }
    #map-tooltip .tt-count   { color: #3b82f6; font-family: 'Space Mono', monospace; }
    #map-tooltip .tt-pct     { color: #64748b; font-size: 11px; }

    /* Live pulses on map */
    .map-pulse {
        position: absolute;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(34,197,94,0.9);
        transform: translate(-50%, -50%);
        z-index: 15;
        pointer-events: none;
    }
    .map-pulse::before {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: 50%;
        background: rgba(34,197,94,0.3);
        animation: pulse-expand 1.5s ease-out infinite;
    }
    @keyframes pulse-expand {
        0%   { transform: scale(0.5); opacity: 1; }
        100% { transform: scale(2.5); opacity: 0; }
    }

    /* Map legend */
    .map-legend {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: #64748b;
        padding: 8px 0 0;
        font-family: 'Space Mono', monospace;
    }
    .map-legend-grad {
        width: 120px;
        height: 8px;
        border-radius: 4px;
        background: linear-gradient(90deg, #0d3a6e, #1d6fb5, #3b82f6, #60a5fa, #bfdbfe);
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* ── IP Frequency Table extras ── */
    .ip-badge-high   { background: rgba(239,68,68,0.15);  color: #ef4444; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 700; }
    .ip-badge-med    { background: rgba(249,115,22,0.15); color: #f97316; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 700; }
    .ip-badge-low    { background: rgba(59,130,246,0.15); color: #3b82f6; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 700; }

    /* ── Visitors log extras ── */
    .db-ip-cell { font-family: 'Space Mono', monospace; font-size: 12px; color: #60a5fa; letter-spacing: 0.3px; }
    .db-visits-badge {
        display: inline-block;
        background: rgba(34,197,94,0.12);
        color: #22c55e;
        font-family: 'Space Mono', monospace;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid rgba(34,197,94,0.2);
        white-space: nowrap;
    }
    .db-visits-badge.multi { background: rgba(249,115,22,0.12); color: #f97316; border-color: rgba(249,115,22,0.2); }

    /* Map section tab layout */
    .map-tabs { display: flex; gap: 8px; margin-bottom: 16px; }
    .map-tab {
        padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 600;
        cursor: pointer; border: 1px solid rgba(255,255,255,0.08);
        background: rgba(255,255,255,0.03); color: #64748b; transition: all 0.2s;
    }
    .map-tab.active { background: rgba(59,130,246,0.15); color: #3b82f6; border-color: rgba(59,130,246,0.3); }

    /* Section header extras */
    .db-section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 20px;
    }
    .db-section-header h2 { font-size: 16px; font-weight: 700; color: #e2e8f0; }
    .db-stat-pill {
        background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2);
        color: #3b82f6; font-size: 11px; font-weight: 600;
        padding: 4px 12px; border-radius: 20px; font-family: 'Space Mono', monospace;
    }
    </style>
</head>
<body>

<!-- ── Sidebar ── -->
<aside class="db-sidebar">
    <div class="db-logo">
        <div class="db-logo-icon"><i class="fas fa-chart-line"></i></div>
        <div>
            <div class="db-logo-name">Sound Vision</div>
            <div class="db-logo-sub">Analytics</div>
        </div>
    </div>

    <nav class="db-nav">
        <a href="#overview"  class="db-nav-item active" data-section="overview">
            <i class="fas fa-gauge-high"></i><span>Overview</span>
        </a>
        <a href="#countries" class="db-nav-item" data-section="countries">
            <i class="fas fa-earth-asia"></i><span>Countries + Map</span>
        </a>
        <a href="#devices"   class="db-nav-item" data-section="devices">
            <i class="fas fa-mobile-screen-button"></i><span>Devices</span>
        </a>
        <a href="#visitors"  class="db-nav-item" data-section="visitors">
            <i class="fas fa-users"></i><span>Visitors Log</span>
        </a>
        <a href="#iptracker" class="db-nav-item" data-section="iptracker">
            <i class="fas fa-network-wired"></i><span>IP Tracker</span>
        </a>
    </nav>

    <div class="db-sidebar-footer">
        <div class="db-live-dot"></div>
        <span id="sidebarOnline"><?= $onlineCount ?> online now</span>
    </div>
</aside>

<!-- ── Main ── -->
<main class="db-main">

    <!-- Top Bar -->
    <header class="db-topbar">
        <div class="db-topbar-left">
            <h1 class="db-page-title">Analytics Overview</h1>
            <p class="db-page-date">Last updated: <?= date('D, d M Y · H:i') ?></p>
        </div>
        <div class="db-topbar-right">
            <a href="../../index.php" class="db-back-btn"><i class="fas fa-arrow-left"></i> Back to Site</a>
            <?php if (!$sv_is_local): ?>
            <a href="dashboard.php?logout=1" class="db-back-btn" style="color:#fca5a5;border-color:rgba(239,68,68,0.3);margin-left:8px"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION: Overview                              -->
    <!-- ══════════════════════════════════════════════ -->
    <section class="db-section" id="overview">

        <!-- KPI Cards -->
        <div class="db-kpi-grid">
            <div class="db-kpi db-kpi--blue">
                <div class="db-kpi-icon"><i class="fas fa-eye"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num"><?= number_format($totalVisitors) ?></div>
                    <div class="db-kpi-label">Total Page Views</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-eye"></i></div>
            </div>
            <div class="db-kpi db-kpi--green">
                <div class="db-kpi-icon"><i class="fas fa-user-check"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num"><?= number_format($totalUnique) ?></div>
                    <div class="db-kpi-label">Unique Sessions</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-user-check"></i></div>
            </div>
            <div class="db-kpi db-kpi--purple">
                <div class="db-kpi-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num"><?= number_format($todayCount) ?></div>
                    <div class="db-kpi-label">Today's Visitors</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-calendar-day"></i></div>
            </div>
            <div class="db-kpi db-kpi--orange">
                <div class="db-kpi-icon"><i class="fas fa-signal"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num db-online-live"><?= $onlineCount ?></div>
                    <div class="db-kpi-label">Online Right Now</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-signal"></i></div>
            </div>
            <div class="db-kpi db-kpi--teal">
                <div class="db-kpi-icon"><i class="fas fa-globe"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num"><?= count($countries) ?></div>
                    <div class="db-kpi-label">Countries Reached</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-globe"></i></div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="db-charts-row">
            <div class="db-chart-card db-chart-card--wide">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-area"></i> Daily Traffic (Last 30 Days)</h3>
                </div>
                <div class="db-chart-body" style="height:260px">
                    <canvas id="chartDaily"></canvas>
                </div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-pie"></i> Top 15 Countries</h3>
                </div>
                <div class="db-chart-body" style="height:260px">
                    <canvas id="chartCountry"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="db-charts-row db-charts-row--3">
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fas fa-mobile-alt"></i> Devices</h3></div>
                <div class="db-chart-body" style="height:220px">
                    <canvas id="chartDevice"></canvas>
                </div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fas fa-browser"></i> Browsers</h3></div>
                <div class="db-chart-body" style="height:220px">
                    <canvas id="chartBrowser"></canvas>
                </div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fab fa-windows"></i> Operating Systems</h3></div>
                <div class="db-chart-body" style="height:220px">
                    <canvas id="chartOS"></canvas>
                </div>
            </div>
        </div>

    </section>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION: Countries + Hacker Map               -->
    <!-- ══════════════════════════════════════════════ -->
    <section class="db-section db-section--hidden" id="countries">

        <div class="db-section-header">
            <h2><i class="fas fa-earth-asia" style="color:#3b82f6;margin-right:8px"></i>Countries &amp; World Map</h2>
            <span class="db-stat-pill"><?= count($countries) ?> countries · <?= number_format($totalUnique) ?> visits</span>
        </div>

        <!-- World Map Card -->
        <div class="db-card" style="margin-bottom:20px">
            <div class="db-card-header">
                <h3><i class="fas fa-map" style="color:#3b82f6"></i>&nbsp; Live Visitor World Map</h3>
                <div style="display:flex;align-items:center;gap:12px;margin-left:auto">
                    <span style="font-size:11px;color:#64748b;font-family:'Space Mono',monospace" id="mapStatus">LOADING INTEL...</span>
                </div>
            </div>
            <div style="padding:20px">
                <div id="map-container">
                    <div class="map-hud map-hud-tl">
                        <span class="map-hud-line">SND_VISION // GEOINT</span>
                        <span class="map-hud-line">ACTIVE_NODES: <span id="hudActiveNodes"><?= count($countries) ?></span></span>
                        <span class="map-hud-line">LIVE_USERS: <span><?= $onlineCount ?></span></span>
                    </div>
                    <div class="map-hud map-hud-tr">
                        <span class="map-hud-line"><?= date('Y-m-d') ?></span>
                        <span class="map-hud-line"><?= date('H:i:s') ?> UTC+0</span>
                        <span class="map-hud-line">GLOBAL_COVERAGE</span>
                    </div>
                    <div class="map-hud map-hud-bl">
                        <span class="map-hud-line">TOP: <?= array_key_first($countries) ?? 'N/A' ?></span>
                        <span class="map-hud-line">VISITS: <?= number_format(array_values($countries)[0] ?? 0) ?></span>
                    </div>
                    <div class="map-hud map-hud-br">
                        <span class="map-hud-line">PROJECTION: MERCATOR</span>
                        <span class="map-hud-line">STATUS: <span>ONLINE</span></span>
                    </div>
                    <div id="map-tooltip">
                        <div class="tt-country" id="tt-country"></div>
                        <div class="tt-count" id="tt-count"></div>
                        <div class="tt-pct" id="tt-pct"></div>
                    </div>
                    <svg id="world-map" style="width:100%;height:420px"></svg>
                </div>
                <div class="map-legend">
                    <span>Low</span>
                    <div class="map-legend-grad"></div>
                    <span>High</span>
                    <span style="margin-left:16px;color:#22c55e">● Active country</span>
                    <span style="margin-left:8px;color:#0d3a6e">■ No data</span>
                </div>
            </div>
        </div>

        <!-- Countries Table -->
        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-list"></i> All Countries</h3>
                <span class="db-badge"><?= count($countries) ?> countries</span>
            </div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Country</th>
                            <th>Visitors</th>
                            <th>Share %</th>
                            <th>Distribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; $maxC = max(array_values($countries) ?: [1]);
                        foreach ($countries as $cn => $cv): ?>
                        <tr>
                            <td class="db-rank"><?= $rank++ ?></td>
                            <td><?= flag($countryCodeMap[$cn] ?? 'XX') ?> <?= htmlspecialchars($cn) ?></td>
                            <td class="db-num"><?= number_format($cv) ?></td>
                            <td class="db-num"><?= $totalUnique > 0 ? round($cv / $totalUnique * 100, 1) : 0 ?>%</td>
                            <td style="min-width:120px">
                                <div class="db-progress">
                                    <div class="db-progress-bar" style="width:<?= $maxC > 0 ? round($cv/$maxC*100) : 0 ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($countries)): ?>
                        <tr><td colspan="5" style="text-align:center;padding:40px;color:#64748b">No data yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION: Devices                              -->
    <!-- ══════════════════════════════════════════════ -->
    <section class="db-section db-section--hidden" id="devices">
        <div class="db-charts-row db-charts-row--3">
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fas fa-mobile-alt"></i> Device Types</h3></div>
                <div class="db-chart-body" style="height:250px"><canvas id="chartDeviceSec"></canvas></div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fas fa-browser"></i> Browsers</h3></div>
                <div class="db-chart-body" style="height:250px"><canvas id="chartBrowserSec"></canvas></div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fab fa-windows"></i> OS</h3></div>
                <div class="db-chart-body" style="height:250px"><canvas id="chartOSSec"></canvas></div>
            </div>
        </div>

        <div class="db-charts-row">
            <div class="db-card">
                <div class="db-card-header"><h3>Browser Breakdown</h3></div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead><tr><th>Browser</th><th>Visitors</th><th>Share</th><th>Bar</th></tr></thead>
                        <tbody>
                            <?php $maxB = max(array_values($browsers) ?: [1]);
                            foreach ($browsers as $bn => $bv): ?>
                            <tr>
                                <td><?= htmlspecialchars($bn) ?></td>
                                <td class="db-num"><?= number_format($bv) ?></td>
                                <td class="db-num"><?= $totalUnique > 0 ? round($bv/$totalUnique*100,1) : 0 ?>%</td>
                                <td><div class="db-progress"><div class="db-progress-bar db-progress-bar--purple" style="width:<?= $maxB > 0 ? round($bv/$maxB*100) : 0 ?>%"></div></div></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="db-card">
                <div class="db-card-header"><h3>OS Breakdown</h3></div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead><tr><th>OS</th><th>Visitors</th><th>Share</th><th>Bar</th></tr></thead>
                        <tbody>
                            <?php $maxO = max(array_values($os_list) ?: [1]);
                            foreach ($os_list as $on => $ov): ?>
                            <tr>
                                <td><?= htmlspecialchars($on) ?></td>
                                <td class="db-num"><?= number_format($ov) ?></td>
                                <td class="db-num"><?= $totalUnique > 0 ? round($ov/$totalUnique*100,1) : 0 ?>%</td>
                                <td><div class="db-progress"><div class="db-progress-bar db-progress-bar--teal" style="width:<?= $maxO > 0 ? round($ov/$maxO*100) : 0 ?>%"></div></div></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION: Visitors Log                         -->
    <!-- ══════════════════════════════════════════════ -->
    <section class="db-section db-section--hidden" id="visitors">
        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-scroll"></i> Visitor Log</h3>
                <span class="db-badge">Latest 100 entries</span>
            </div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>Date / Time</th>
                            <th>IP Address</th>
                            <th>Visits</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Device</th>
                            <th>OS</th>
                            <th>Browser</th>
                            <th>Referer</th>
                            <th>Page</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $r):
                            $visitsForIp = $ipCounts[$r['ip']]['count'] ?? 1;
                        ?>
                        <tr>
                            <td class="db-mono db-nowrap" style="font-size:11px"><?= htmlspecialchars($r['datetime']) ?></td>
                            <td class="db-ip-cell"><?= htmlspecialchars($r['ip']) ?></td>
                            <td><span class="db-visits-badge <?= $visitsForIp > 1 ? 'multi' : '' ?>"><?= $visitsForIp ?>x</span></td>
                            <td><?= flag($r['countryCode']) ?> <?= htmlspecialchars($r['country']) ?></td>
                            <td style="color:#94a3b8;font-size:12px"><?= htmlspecialchars($r['city']) ?></td>
                            <td><span class="db-tag db-tag--<?= strtolower($r['device']) ?>"><?= htmlspecialchars($r['device']) ?></span></td>
                            <td style="font-size:12px"><?= htmlspecialchars($r['os']) ?></td>
                            <td style="font-size:12px"><?= htmlspecialchars($r['browser']) ?></td>
                            <td class="db-truncate" title="<?= htmlspecialchars($r['referer']) ?>" style="max-width:120px"><?= htmlspecialchars($r['referer']) ?></td>
                            <td class="db-truncate" title="<?= htmlspecialchars($r['page']) ?>" style="max-width:120px"><?= htmlspecialchars($r['page']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent)): ?>
                        <tr><td colspan="10" style="text-align:center;padding:40px;color:#64748b">No visitor data yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION: IP Tracker                           -->
    <!-- ══════════════════════════════════════════════ -->
    <section class="db-section db-section--hidden" id="iptracker">
        <div class="db-section-header">
            <h2><i class="fas fa-network-wired" style="color:#3b82f6;margin-right:8px"></i>IP Frequency Tracker</h2>
            <span class="db-stat-pill"><?= count($ipCounts) ?> unique IPs tracked</span>
        </div>

        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-fingerprint"></i> Top IPs by Visit Count</h3>
                <span class="db-badge"><?= count($topIPs) ?> IPs</span>
            </div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>IP Address</th>
                            <th>Total Visits</th>
                            <th>Frequency</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Device</th>
                            <th>Browser</th>
                            <th>Last Seen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ipRank = 1; $maxIPCount = max(array_column(array_values($topIPs), 'count') ?: [1]);
                        foreach ($topIPs as $ip => $info):
                            $cnt = $info['count'];
                            if ($cnt >= 10) $badgeClass = 'ip-badge-high';
                            elseif ($cnt >= 3) $badgeClass = 'ip-badge-med';
                            else $badgeClass = 'ip-badge-low';
                        ?>
                        <tr>
                            <td class="db-rank"><?= $ipRank++ ?></td>
                            <td class="db-ip-cell"><?= htmlspecialchars($ip) ?></td>
                            <td><span class="<?= $badgeClass ?>"><?= $cnt ?> visits</span></td>
                            <td style="min-width:100px">
                                <div class="db-progress">
                                    <div class="db-progress-bar" style="width:<?= $maxIPCount > 0 ? round($cnt/$maxIPCount*100) : 0 ?>%"></div>
                                </div>
                            </td>
                            <td><?= flag($info['countryCode']) ?> <?= htmlspecialchars($info['country']) ?></td>
                            <td style="color:#94a3b8;font-size:12px"><?= htmlspecialchars($info['city']) ?></td>
                            <td><span class="db-tag db-tag--<?= strtolower($info['device']) ?>"><?= htmlspecialchars($info['device']) ?></span></td>
                            <td style="font-size:12px"><?= htmlspecialchars($info['browser']) ?></td>
                            <td class="db-mono" style="font-size:11px;color:#64748b"><?= htmlspecialchars($info['last']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($topIPs)): ?>
                        <tr><td colspan="9" style="text-align:center;padding:40px;color:#64748b">No IP data yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

</main>

<!-- ── Data for JS ── -->
<script>
// FIX: Use var so dashboard.js can access as window.DB_DATA
var DB_DATA = {
    top15:    <?= $top15Json ?>,
    last30:   <?= $last30Json ?>,
    devices:  <?= $devJson ?>,
    browsers: <?= $browJson ?>,
    os:       <?= $osJson ?>,
    online:   <?= $onlineCount ?>,
    countryMap: <?= $countryMapJson ?>
};
</script>
<script src="dashboard.js"></script>
<script>
// ── Hacker World Map ─────────────────────────────────────────
(function() {
    'use strict';

    const mapData = DB_DATA.countryMap || [];
    const totalVisits = mapData.reduce((s, d) => s + d.count, 0) || 1;

    // Build lookup by ISO2 code
    const codeMap = {};
    mapData.forEach(d => { codeMap[d.code.toUpperCase()] = d; });

    const maxCount = Math.max(...mapData.map(d => d.count), 1);

    // Color scale: dark blue → bright blue
    const colorScale = (count) => {
        if (!count) return '#0d1f35';
        const t = Math.pow(count / maxCount, 0.4); // power scale for better distribution
        const r = Math.round(13  + t * (59  - 13));
        const g = Math.round(31  + t * (130 - 31));
        const b = Math.round(53  + t * (246 - 53));
        return `rgb(${r},${g},${b})`;
    };

    const svg = d3.select('#world-map');
    const container = document.getElementById('map-container');
    const tooltip = document.getElementById('map-tooltip');

    function draw() {
        const W = container.clientWidth || 900;
        const H = 420;
        svg.attr('viewBox', `0 0 ${W} ${H}`).attr('width', W).attr('height', H);
        svg.selectAll('*').remove();

        const projection = d3.geoNaturalEarth1()
            .scale(W / 6.2)
            .translate([W / 2, H / 2]);

        const path = d3.geoPath().projection(projection);

        // Background sphere
        svg.append('path')
            .datum({type: 'Sphere'})
            .attr('class', 'sphere')
            .attr('d', path);

        // Graticule
        const graticule = d3.geoGraticule()();
        svg.append('path')
            .datum(graticule)
            .attr('class', 'graticule')
            .attr('d', path);

        // Load world topology
        fetch('https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json')
            .then(r => r.json())
            .then(world => {
                const countries = topojson.feature(world, world.objects.countries);

                // ISO numeric → ISO alpha-2 table (common ones)
                const numToAlpha2 = {
                    "004":"AF","008":"AL","012":"DZ","024":"AO","032":"AR","036":"AU","040":"AT","050":"BD",
                    "056":"BE","064":"BT","068":"BO","076":"BR","100":"BG","116":"KH","120":"CM","124":"CA",
                    "144":"LK","152":"CL","156":"CN","170":"CO","178":"CG","180":"CD","191":"HR","192":"CU",
                    "196":"CY","203":"CZ","208":"DK","218":"EC","818":"EG","233":"EE","231":"ET","246":"FI",
                    "250":"FR","276":"DE","288":"GH","300":"GR","320":"GT","324":"GN","332":"HT","340":"HN",
                    "348":"HU","356":"IN","360":"ID","364":"IR","368":"IQ","372":"IE","376":"IL","380":"IT",
                    "388":"JM","392":"JP","400":"JO","398":"KZ","404":"KE","408":"KP","410":"KR","414":"KW",
                    "418":"LA","422":"LB","434":"LY","440":"LT","442":"LU","458":"MY","466":"ML","484":"MX",
                    "504":"MA","508":"MZ","516":"NA","524":"NP","528":"NL","540":"NC","554":"NZ","558":"NI",
                    "566":"NG","578":"NO","586":"PK","591":"PA","598":"PG","600":"PY","604":"PE","608":"PH",
                    "616":"PL","620":"PT","630":"PR","634":"QA","642":"RO","643":"RU","646":"RW","682":"SA",
                    "686":"SN","694":"SL","703":"SK","706":"SO","710":"ZA","724":"ES","729":"SD","752":"SE",
                    "756":"CH","760":"SY","762":"TJ","764":"TH","792":"TR","800":"UG","804":"UA","784":"AE",
                    "826":"GB","840":"US","858":"UY","860":"UZ","862":"VE","704":"VN","887":"YE","894":"ZM",
                    "716":"ZW","643":"RU","032":"AR","076":"BR","124":"CA","156":"CN","250":"FR","276":"DE",
                    "356":"IN","380":"IT","392":"JP","484":"MX","554":"NZ","566":"NG","643":"RU","710":"ZA",
                    "724":"ES","752":"SE","756":"CH","826":"GB","840":"US"
                };

                svg.selectAll('.country-path')
                    .data(countries.features)
                    .enter()
                    .append('path')
                    .attr('class', d => {
                        const code = numToAlpha2[String(d.id).padStart(3,'0')] || '';
                        return 'country-path' + (codeMap[code] ? ' has-data' : '');
                    })
                    .attr('d', path)
                    .attr('fill', d => {
                        const code = numToAlpha2[String(d.id).padStart(3,'0')] || '';
                        const info = codeMap[code];
                        return info ? colorScale(info.count) : '#0d1f35';
                    })
                    .on('mousemove', function(event, d) {
                        const code = numToAlpha2[String(d.id).padStart(3,'0')] || '';
                        const info = codeMap[code];
                        if (!info) return;

                        const rect = container.getBoundingClientRect();
                        let x = event.clientX - rect.left + 16;
                        let y = event.clientY - rect.top - 10;

                        document.getElementById('tt-country').textContent = info.name;
                        document.getElementById('tt-count').textContent = info.count.toLocaleString() + ' visits';
                        document.getElementById('tt-pct').textContent = ((info.count / totalVisits) * 100).toFixed(1) + '% of total traffic';
                        tooltip.style.display = 'block';
                        tooltip.style.left = Math.min(x, container.clientWidth - 180) + 'px';
                        tooltip.style.top  = y + 'px';

                        d3.select(this).attr('stroke', '#60a5fa').attr('stroke-width', 1.5);
                    })
                    .on('mouseleave', function(event, d) {
                        tooltip.style.display = 'none';
                        d3.select(this).attr('stroke', '#1a3a5c').attr('stroke-width', 0.5);
                    });

                // Add glowing dots on top countries (centroid-based)
                const topN = mapData.slice(0, 8);
                topN.forEach(item => {
                    // Find the feature
                    const feat = countries.features.find(f => {
                        const code = numToAlpha2[String(f.id).padStart(3,'0')] || '';
                        return code === item.code.toUpperCase();
                    });
                    if (!feat) return;
                    const c = path.centroid(feat);
                    if (!c || isNaN(c[0]) || isNaN(c[1])) return;

                    const g = svg.append('g').attr('transform', `translate(${c[0]},${c[1]})`);
                    // Pulse ring
                    g.append('circle')
                        .attr('r', 6)
                        .attr('fill', 'rgba(34,197,94,0.2)')
                        .attr('stroke', 'none')
                        .append('animate')
                        .attr('attributeName', 'r')
                        .attr('values', '4;14;4')
                        .attr('dur', (1.5 + Math.random()).toFixed(1) + 's')
                        .attr('repeatCount', 'indefinite');
                    g.append('circle')
                        .attr('r', 4)
                        .attr('fill', 'none')
                        .attr('stroke', 'rgba(34,197,94,0.4)')
                        .attr('stroke-width', 1)
                        .append('animate')
                        .attr('attributeName', 'r')
                        .attr('values', '3;12;3')
                        .attr('dur', '2s')
                        .attr('repeatCount', 'indefinite');
                    // Center dot
                    g.append('circle')
                        .attr('r', 3)
                        .attr('fill', '#22c55e')
                        .attr('filter', 'url(#glow)');
                });

                // Glow filter
                const defs = svg.append('defs');
                const filter = defs.append('filter').attr('id', 'glow');
                filter.append('feGaussianBlur').attr('stdDeviation', '2').attr('result', 'coloredBlur');
                const merge = filter.append('feMerge');
                merge.append('feMergeNode').attr('in', 'coloredBlur');
                merge.append('feMergeNode').attr('in', 'SourceGraphic');

                document.getElementById('mapStatus').textContent = 'FEED ACTIVE // ' + mapData.length + ' NODES';
            })
            .catch(() => {
                document.getElementById('mapStatus').textContent = 'MAP LOAD FAILED';
            });
    }

    // Draw on load + redraw on resize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', draw);
    } else {
        setTimeout(draw, 100);
    }

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(draw, 300);
    });

    // Redraw when countries section becomes visible
    document.querySelectorAll('.db-nav-item').forEach(item => {
        item.addEventListener('click', () => {
            if (item.dataset.section === 'countries') {
                setTimeout(draw, 150);
            }
        });
    });
})();
</script>
</body>
</html>
