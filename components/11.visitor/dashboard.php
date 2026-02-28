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

// Per-country IP drill-down data: code → [ip, city, region, device, browser, count, last]
$ipByCountry = [];
foreach ($ipCounts as $ipAddr => $ipData) {
    $cc = strtoupper(trim($ipData['countryCode'] ?? ''));
    if (!$cc || $cc === 'XX' || $cc === 'LO') continue;
    if (!isset($ipByCountry[$cc])) $ipByCountry[$cc] = [];
    $ipByCountry[$cc][] = [
        'ip'      => $ipAddr,
        'city'    => $ipData['city']    ?? 'Unknown',
        'region'  => $ipData['region']  ?? '',
        'device'  => $ipData['device']  ?? '',
        'browser' => $ipData['browser'] ?? '',
        'count'   => (int)($ipData['count'] ?? 1),
        'last'    => $ipData['last']    ?? '',
    ];
}
// Sort by count desc, keep top 100 per country
foreach ($ipByCountry as $cc => &$ips) {
    usort($ips, fn($a,$b) => $b['count'] - $a['count']);
    if (count($ips) > 100) $ips = array_slice($ips, 0, 100);
}
unset($ips);
$ipByCountryJson = json_encode($ipByCountry, JSON_UNESCAPED_UNICODE);

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
    <!-- Leaflet — interactive zoomable map -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <style>
    /* ══════════════════════════════════════════════════
       PREMIUM LEAFLET MAP — Sound Vision Analytics
       ══════════════════════════════════════════════════ */

    /* IP Frequency Table */
    .ip-badge-high   { background:rgba(239,68,68,.15);  color:#ef4444; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:700; }
    .ip-badge-med    { background:rgba(249,115,22,.15); color:#f97316; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:700; }
    .ip-badge-low    { background:rgba(59,130,246,.15); color:#3b82f6; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:700; }

    /* Visitors log */
    .db-ip-cell { font-family:'Space Mono',monospace; font-size:12px; color:#60a5fa; letter-spacing:.3px; }
    .db-visits-badge { display:inline-block; background:rgba(34,197,94,.12); color:#22c55e; font-family:'Space Mono',monospace; font-size:11px; font-weight:700; padding:2px 7px; border-radius:4px; border:1px solid rgba(34,197,94,.2); white-space:nowrap; }
    .db-visits-badge.multi { background:rgba(249,115,22,.12); color:#f97316; border-color:rgba(249,115,22,.2); }

    /* Section header */
    .db-section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
    .db-section-header h2 { font-size:16px; font-weight:700; color:#e2e8f0; }
    .db-stat-pill { background:rgba(59,130,246,.1); border:1px solid rgba(59,130,246,.2); color:#3b82f6; font-size:11px; font-weight:600; padding:4px 12px; border-radius:20px; font-family:'Space Mono',monospace; }

    /* Map tab layout (kept for compatibility) */
    .map-tabs { display:flex; gap:8px; margin-bottom:16px; }
    .map-tab { padding:6px 16px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.03); color:#64748b; transition:all .2s; }
    .map-tab.active { background:rgba(59,130,246,.15); color:#3b82f6; border-color:rgba(59,130,246,.3); }

    /* ── MAP WRAPPER ── */
    #sv-map-wrap {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        background: #020810;
        border: 1px solid rgba(59,130,246,0.12);
    }

    /* The actual Leaflet map div */
    #sv-leaflet-map {
        width: 100%;
        height: 520px;
        background: #020810;
        cursor: crosshair;
    }

    /* Override Leaflet defaults for dark theme */
    .leaflet-container { background: #020810 !important; font-family:'Space Grotesk',sans-serif !important; }
    .leaflet-tile { filter: brightness(0.9) contrast(1.05) saturate(1.1); }

    /* Zoom controls */
    .leaflet-control-zoom {
        border: 1px solid rgba(59,130,246,0.3) !important;
        border-radius: 8px !important;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.6) !important;
        margin-top: 56px !important;
    }
    .leaflet-control-zoom a {
        background: rgba(6,10,20,0.95) !important;
        color: #60a5fa !important;
        border-bottom: 1px solid rgba(59,130,246,0.2) !important;
        font-size: 18px !important;
        width: 32px !important; height: 32px !important;
        line-height: 32px !important;
        transition: all 0.15s !important;
    }
    .leaflet-control-zoom a:hover { background: rgba(59,130,246,0.2) !important; color: #fff !important; }
    .leaflet-control-zoom-out { border-bottom: none !important; }

    /* Attribution */
    .leaflet-control-attribution {
        background: rgba(2,8,16,0.85) !important;
        color: #1e3a5f !important;
        font-size: 9px !important;
        padding: 2px 8px !important;
        border-radius: 4px 0 0 0 !important;
    }
    .leaflet-control-attribution a { color: #2d4f6e !important; }

    /* Leaflet popups */
    .leaflet-popup-content-wrapper {
        background: rgba(6,10,20,0.97) !important;
        border: 1px solid rgba(59,130,246,0.4) !important;
        border-radius: 10px !important;
        box-shadow: 0 8px 40px rgba(0,0,0,0.8), 0 0 0 1px rgba(59,130,246,0.1) !important;
        color: #e2e8f0 !important;
    }
    .leaflet-popup-tip-container { display: none; }
    .leaflet-popup-content { margin: 0 !important; font-family:'Space Grotesk',sans-serif !important; }
    .leaflet-popup-close-button { color: #64748b !important; font-size: 18px !important; top: 8px !important; right: 10px !important; }
    .leaflet-popup-close-button:hover { color: #ef4444 !important; background: transparent !important; }

    /* ── MAP HUD OVERLAYS ── */
    .sv-hud {
        position: absolute;
        z-index: 800;
        font-family: 'Space Mono', monospace;
        font-size: 10px;
        pointer-events: none;
    }
    .sv-hud-tl { top:12px; left:12px; }
    .sv-hud-tr { top:12px; right:52px; text-align:right; }
    .sv-hud-bl { bottom:14px; left:12px; }
    .sv-hud-br { bottom:14px; right:12px; text-align:right; }
    .sv-hud-box {
        background: rgba(2,8,16,0.88);
        border: 1px solid rgba(59,130,246,0.22);
        border-radius: 6px;
        padding: 5px 10px;
        backdrop-filter: blur(8px);
        line-height: 1.85;
    }
    .sv-hud-line { display:block; color:rgba(96,165,250,0.75); white-space:nowrap; }
    .sv-hud-line em { color:#22c55e; font-style:normal; }
    .sv-hud-line b  { color:#93c5fd; font-style:normal; }

    /* Scan line */
    .sv-scan {
        position: absolute; top:0; left:0; right:0;
        height: 2px; z-index: 799; pointer-events:none;
        background: linear-gradient(90deg, transparent 0%, #1d4ed8 30%, #60a5fa 50%, #1d4ed8 70%, transparent 100%);
        animation: sv-scan-anim 6s linear infinite;
        opacity: 0.6;
    }
    @keyframes sv-scan-anim {
        0%   { top:0;    opacity:0; }
        4%   { opacity:.6; }
        96%  { opacity:.6; }
        100% { top:100%; opacity:0; }
    }

    /* ── MAP CONTROLS BAR ── */
    .sv-map-bar {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 14px;
        background: rgba(6,10,20,0.95);
        border-top: 1px solid rgba(59,130,246,0.1);
        flex-wrap: wrap;
    }
    .sv-map-btn {
        padding: 4px 13px; border-radius:5px;
        border: 1px solid rgba(59,130,246,0.2);
        background: rgba(59,130,246,0.06);
        color: #64748b; font-size:11px; font-weight:600;
        font-family:'Space Mono',monospace; cursor:pointer;
        transition: all .18s; letter-spacing:.4px; white-space:nowrap;
    }
    .sv-map-btn:hover { background:rgba(59,130,246,0.15); color:#60a5fa; border-color:rgba(59,130,246,0.35); }
    .sv-map-btn.sv-active { background:rgba(59,130,246,0.2); color:#3b82f6; border-color:rgba(59,130,246,0.45); }
    .sv-map-btn-back { color:#f97316; border-color:rgba(249,115,22,0.25); background:rgba(249,115,22,0.06); display:none; }
    .sv-map-btn-back:hover { background:rgba(249,115,22,0.18); border-color:rgba(249,115,22,0.4); color:#fb923c; }
    .sv-map-btn-back.sv-visible { display:inline-flex; align-items:center; gap:5px; }

    .sv-map-legend { display:flex; align-items:center; gap:6px; font-family:'Space Mono',monospace; font-size:10px; color:#334155; }
    .sv-map-legend-bar { width:90px; height:5px; border-radius:3px; background:linear-gradient(90deg,#071224,#0a2040,#0f3a70,#1d6fb5,#3b82f6,#60a5fa,#93c5fd); }
    .sv-map-status { font-family:'Space Mono',monospace; font-size:10px; margin-left:auto; }
    .sv-status-live { color:#22c55e; }
    .sv-status-loading { color:#f97316; }
    .sv-status-err { color:#ef4444; }

    /* ── COUNTRY TOOLTIP ── */
    .sv-tooltip {
        background: rgba(4,9,20,0.97);
        border: 1px solid rgba(59,130,246,0.45);
        border-radius: 9px;
        padding: 12px 15px;
        min-width: 185px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.75), 0 0 0 1px rgba(59,130,246,0.08);
        font-family: 'Space Grotesk', sans-serif;
        pointer-events: none;
    }
    .sv-tt-header { display:flex; align-items:center; gap:8px; margin-bottom:9px; }
    .sv-tt-flag { font-size:20px; line-height:1; }
    .sv-tt-name { font-weight:700; font-size:14px; color:#f1f5f9; }
    .sv-tt-row { display:flex; justify-content:space-between; align-items:center; margin-top:5px; font-size:12px; }
    .sv-tt-lbl { color:#475569; }
    .sv-tt-val { font-family:'Space Mono',monospace; font-weight:700; color:#3b82f6; }
    .sv-tt-pct { font-family:'Space Mono',monospace; font-size:11px; color:#22c55e; }
    .sv-tt-hint { margin-top:10px; padding-top:8px; border-top:1px solid rgba(255,255,255,0.06); font-size:10px; color:#334155; text-align:center; font-family:'Space Mono',monospace; }

    /* ── IP DRILL-DOWN PANEL ── */
    .sv-drill {
        position: absolute; right:12px; top:12px;
        width: 290px; max-height: 430px;
        background: rgba(4,9,20,0.97);
        border: 1px solid rgba(59,130,246,0.35);
        border-radius: 10px;
        z-index: 1000;
        display: none; flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.85), 0 0 0 1px rgba(59,130,246,0.08);
        backdrop-filter: blur(20px);
    }
    .sv-drill.open { display:flex; }
    .sv-drill-head {
        padding: 11px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        display: flex; align-items:center; gap:8px; flex-shrink:0;
    }
    .sv-drill-flag { font-size:22px; }
    .sv-drill-title { font-weight:700; font-size:14px; flex:1; color:#f1f5f9; }
    .sv-drill-meta { font-family:'Space Mono',monospace; font-size:10px; color:#22c55e; white-space:nowrap; }
    .sv-drill-x {
        width:22px; height:22px; border-radius:4px;
        background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25);
        color:#ef4444; font-size:14px; cursor:pointer; pointer-events:all;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
        transition:all .15s;
    }
    .sv-drill-x:hover { background:rgba(239,68,68,.25); }
    .sv-drill-body { overflow-y:auto; flex:1; }
    .sv-drill-body::-webkit-scrollbar { width:3px; }
    .sv-drill-body::-webkit-scrollbar-thumb { background:rgba(59,130,246,0.25); border-radius:2px; }
    .sv-drill-empty { padding:28px; text-align:center; color:#334155; font-size:12px; font-family:'Space Mono',monospace; }
    .sv-ip-row {
        padding: 8px 13px; border-bottom:1px solid rgba(255,255,255,0.04);
        cursor:pointer; transition:background .12s;
        display:flex; flex-direction:column; gap:3px;
    }
    .sv-ip-row:hover { background:rgba(59,130,246,0.08); }
    .sv-ip-row:last-child { border-bottom:none; }
    .sv-ip-top { display:flex; align-items:center; justify-content:space-between; gap:6px; }
    .sv-ip-addr { font-family:'Space Mono',monospace; font-size:12px; color:#60a5fa; font-weight:600; }
    .sv-ip-badge { font-family:'Space Mono',monospace; font-size:10px; padding:1px 6px; border-radius:3px; background:rgba(34,197,94,0.1); color:#22c55e; white-space:nowrap; }
    .sv-ip-badge.sv-multi { background:rgba(249,115,22,0.1); color:#f97316; }
    .sv-ip-city { font-size:11px; color:#64748b; }
    .sv-ip-meta { font-size:10px; color:#1e3a5f; }
    .sv-ip-links { display:flex; gap:5px; margin-top:3px; flex-wrap:wrap; }
    .sv-ip-link { font-size:10px; color:#1d4ed8; border:1px solid rgba(29,78,216,0.2); padding:1px 6px; border-radius:3px; text-decoration:none; transition:all .15s; font-family:'Space Mono',monospace; }
    .sv-ip-link:hover { background:rgba(59,130,246,0.12); color:#60a5fa; border-color:rgba(59,130,246,0.35); }

    /* ── IP DOT MARKERS ── */
    .sv-dot-outer {
        width:16px; height:16px; border-radius:50%; cursor:pointer;
        background: rgba(34,197,94,0.18);
        border: 1.5px solid rgba(34,197,94,0.6);
        display:flex; align-items:center; justify-content:center;
        animation: sv-dot-pulse 2.2s ease-out infinite;
        transition: transform .15s;
    }
    .sv-dot-outer:hover { transform: scale(1.4); }
    .sv-dot-inner { width:6px; height:6px; border-radius:50%; background:#22c55e; box-shadow:0 0 6px #22c55e; }
    @keyframes sv-dot-pulse {
        0%   { box-shadow: 0 0 0 0px rgba(34,197,94,0.5); }
        70%  { box-shadow: 0 0 0 10px rgba(34,197,94,0); }
        100% { box-shadow: 0 0 0 0px rgba(34,197,94,0); }
    }

    /* Popup content */
    .sv-popup { min-width:200px; padding:12px 14px; }
    .sv-popup-ip { font-family:'Space Mono',monospace; font-size:14px; color:#60a5fa; font-weight:700; margin-bottom:8px; }
    .sv-popup-row { display:flex; gap:8px; margin-top:5px; font-size:12px; }
    .sv-popup-lbl { color:#475569; min-width:56px; }
    .sv-popup-val { color:#cbd5e1; }
    .sv-popup-links { display:flex; gap:6px; margin-top:10px; padding-top:9px; border-top:1px solid rgba(255,255,255,0.07); flex-wrap:wrap; }
    .sv-popup-link { font-size:11px; color:#3b82f6; border:1px solid rgba(59,130,246,0.25); padding:3px 9px; border-radius:4px; text-decoration:none; transition:all .15s; font-family:'Space Mono',monospace; }
    .sv-popup-link:hover { background:rgba(59,130,246,0.15); color:#93c5fd; }
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
                    <span id="mapStatus" class="sv-status-loading" style="font-size:11px;font-family:'Space Mono',monospace">LOADING MAP…</span>
                </div>
            </div>
            <div style="padding:16px 20px 0">

                <!-- Map wrapper: contains Leaflet + HUD overlays + drill panel -->
                <div id="sv-map-wrap">
                    <!-- Scan line animation -->
                    <div class="sv-scan"></div>

                    <!-- HUD: top-left -->
                    <div class="sv-hud sv-hud-tl">
                        <div class="sv-hud-box">
                            <span class="sv-hud-line">SND_VISION // GEOINT</span>
                            <span class="sv-hud-line">NODES: <em id="svHudNodes"><?= count($countries) ?></em></span>
                            <span class="sv-hud-line">LIVE: <em id="svHudLive"><?= $onlineCount ?></em></span>
                        </div>
                    </div>

                    <!-- HUD: top-right -->
                    <div class="sv-hud sv-hud-tr">
                        <div class="sv-hud-box">
                            <span class="sv-hud-line" id="svHudDate"><?= date('Y-m-d') ?></span>
                            <span class="sv-hud-line" id="svHudTime"><?= date('H:i:s') ?> UTC</span>
                            <span class="sv-hud-line">GLOBAL_COVERAGE</span>
                        </div>
                    </div>

                    <!-- HUD: bottom-left -->
                    <div class="sv-hud sv-hud-bl">
                        <div class="sv-hud-box">
                            <span class="sv-hud-line">TOP: <b><?= htmlspecialchars(array_key_first($countries) ?? 'N/A') ?></b></span>
                            <span class="sv-hud-line">VISITS: <b><?= number_format(array_values($countries)[0] ?? 0) ?></b></span>
                        </div>
                    </div>

                    <!-- HUD: bottom-right (zoom hint) -->
                    <div class="sv-hud sv-hud-br">
                        <div class="sv-hud-box">
                            <span class="sv-hud-line" id="svHudZoom">ZOOM: 2</span>
                            <span class="sv-hud-line">SCROLL TO ZOOM</span>
                        </div>
                    </div>

                    <!-- IP Drill-down panel (shown when country is clicked) -->
                    <div class="sv-drill" id="svDrillPanel">
                        <div class="sv-drill-head">
                            <span class="sv-drill-flag" id="svDrillFlag">🌍</span>
                            <span class="sv-drill-title" id="svDrillTitle">Country</span>
                            <span class="sv-drill-meta" id="svDrillMeta">0 IPs</span>
                            <div class="sv-drill-x" id="svDrillClose" title="Close">✕</div>
                        </div>
                        <div class="sv-drill-body" id="svDrillBody">
                            <div class="sv-drill-empty">Click a country on the map</div>
                        </div>
                    </div>

                    <!-- Leaflet map -->
                    <div id="sv-leaflet-map"></div>
                </div>

                <!-- Controls bar below map -->
                <div class="sv-map-bar">
                    <button class="sv-map-btn sv-map-btn-back" id="svBtnBack">
                        ← WORLD VIEW
                    </button>
                    <button class="sv-map-btn sv-active" id="svBtnChoro" title="Color countries by visit count">CHOROPLETH</button>
                    <button class="sv-map-btn" id="svBtnDots" title="Show visitor IP dots on zoom">IP DOTS</button>
                    <button class="sv-map-btn" id="svBtnReset" title="Reset to world view">RESET VIEW</button>
                    <div class="sv-map-legend" style="margin-left:auto">
                        <span>Low</span>
                        <div class="sv-map-legend-bar"></div>
                        <span>High</span>
                        <span style="margin-left:10px;color:#22c55e">● Has visitors</span>
                        <span style="margin-left:6px;color:#0f2845">■ No data</span>
                    </div>
                </div>

            </div><!-- /padding -->
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
    top15:       <?= $top15Json ?>,
    last30:      <?= $last30Json ?>,
    devices:     <?= $devJson ?>,
    browsers:    <?= $browJson ?>,
    os:          <?= $osJson ?>,
    online:      <?= $onlineCount ?>,
    countryMap:  <?= $countryMapJson ?>,
    ipByCountry: <?= $ipByCountryJson ?>
};
</script>
<script src="dashboard.js"></script>
<script>
// ══════════════════════════════════════════════════════════════
// Sound Vision — Premium Interactive World Map
// Engine: Leaflet.js + CartoDB Dark Matter tiles
// Features:
//   • Full world choropleth (countries colored by visit density)
//   • Scroll/pinch zoom — real map tiles at every zoom level
//   • Click any country → zoom in + show IP dots (city level)
//   • Drill-down panel with IP list, city, device, browser
//   • Click IP dot → popup with details + external lookup links
//   • Back button returns to world view
//   • HUD clock, zoom level display
//   • All data sourced from PHP visitor log (no API calls)
// ══════════════════════════════════════════════════════════════
(function () {
'use strict';

// ── Helpers ───────────────────────────────────────────────────

// ISO2 → flag emoji
function flagEmoji(cc) {
    if (!cc || cc.length !== 2) return '🌐';
    const pts = cc.toUpperCase().split('').map(c => 0x1F1E0 + c.charCodeAt(0) - 65);
    return String.fromCodePoint(pts[0]) + String.fromCodePoint(pts[1]);
}

// Rough city name → [lat, lon] — covers most common visitor cities globally
// Format: "City,CC" → [lat,lon]  or just "City" for major world cities
const CITY_COORDS = {
    // Bangladesh
    'Dhaka,BD':[23.7104,90.4074],'Chittagong,BD':[22.3569,91.7832],'Sylhet,BD':[24.8949,91.8687],
    'Rajshahi,BD':[24.3745,88.6042],'Khulna,BD':[22.8456,89.5403],'Barishal,BD':[22.7010,90.3535],
    'Mymensingh,BD':[24.7471,90.4203],'Comilla,BD':[23.4607,91.1809],'Narayanganj,BD':[23.6238,90.4997],
    'Gazipur,BD':[23.9999,90.4203],'Tongi,BD':[23.8897,90.3988],'Narsingdi,BD':[23.9218,90.7152],
    // India
    'Mumbai,IN':[19.0760,72.8777],'Delhi,IN':[28.6139,77.2090],'Bangalore,IN':[12.9716,77.5946],
    'Chennai,IN':[13.0827,80.2707],'Kolkata,IN':[22.5726,88.3639],'Hyderabad,IN':[17.3850,78.4867],
    'Pune,IN':[18.5204,73.8567],'Ahmedabad,IN':[23.0225,72.5714],'Surat,IN':[21.1702,72.8311],
    'Jaipur,IN':[26.9124,75.7873],'Lucknow,IN':[26.8467,80.9462],'Kanpur,IN':[26.4499,80.3319],
    'Patna,IN':[25.5941,85.1376],'Bhopal,IN':[23.2599,77.4126],'Indore,IN':[22.7196,75.8577],
    'Nagpur,IN':[21.1458,79.0882],'Visakhapatnam,IN':[17.6868,83.2185],'Kochi,IN':[9.9312,76.2673],
    // USA
    'New York,US':[40.7128,-74.0060],'Los Angeles,US':[34.0522,-118.2437],'Chicago,US':[41.8781,-87.6298],
    'Houston,US':[29.7604,-95.3698],'Phoenix,US':[33.4484,-112.0740],'Philadelphia,US':[39.9526,-75.1652],
    'San Antonio,US':[29.4241,-98.4936],'San Diego,US':[32.7157,-117.1611],'Dallas,US':[32.7767,-96.7970],
    'San Jose,US':[37.3382,-121.8863],'Austin,US':[30.2672,-97.7431],'Seattle,US':[47.6062,-122.3321],
    'Denver,US':[39.7392,-104.9903],'Boston,US':[42.3601,-71.0589],'Atlanta,US':[33.7490,-84.3880],
    'Miami,US':[25.7617,-80.1918],'Minneapolis,US':[44.9778,-93.2650],'Portland,US':[45.5051,-122.6750],
    'Las Vegas,US':[36.1699,-115.1398],'Detroit,US':[42.3314,-83.0457],
    // UK
    'London,GB':[51.5074,-0.1278],'Manchester,GB':[53.4808,-2.2426],'Birmingham,GB':[52.4862,-1.8904],
    'Leeds,GB':[53.8008,-1.5491],'Glasgow,GB':[55.8642,-4.2518],'Edinburgh,GB':[55.9533,-3.1883],
    'Liverpool,GB':[53.4084,-2.9916],'Bristol,GB':[51.4545,-2.5879],'Sheffield,GB':[53.3811,-1.4701],
    // Pakistan
    'Karachi,PK':[24.8607,67.0011],'Lahore,PK':[31.5204,74.3587],'Islamabad,PK':[33.7294,73.0931],
    'Rawalpindi,PK':[33.5651,73.0169],'Faisalabad,PK':[31.4504,73.1350],'Multan,PK':[30.1575,71.5249],
    'Peshawar,PK':[34.0151,71.5249],'Quetta,PK':[30.1798,66.9750],
    // Brazil
    'São Paulo,BR':[-23.5505,-46.6333],'Rio de Janeiro,BR':[-22.9068,-43.1729],
    'Brasília,BR':[-15.7801,-47.9292],'Salvador,BR':[-12.9714,-38.5014],
    'Fortaleza,BR':[-3.7172,-38.5433],'Manaus,BR':[-3.1190,-60.0217],
    // Nigeria
    'Lagos,NG':[6.5244,3.3792],'Abuja,NG':[9.0579,7.4951],'Kano,NG':[12.0022,8.5920],
    'Ibadan,NG':[7.3775,3.9470],'Port Harcourt,NG':[4.8156,7.0498],
    // China
    'Shanghai,CN':[31.2304,121.4737],'Beijing,CN':[39.9042,116.4074],'Guangzhou,CN':[23.1291,113.2644],
    'Shenzhen,CN':[22.5431,114.0579],'Chengdu,CN':[30.5728,104.0668],'Hangzhou,CN':[30.2741,120.1551],
    'Wuhan,CN':[30.5928,114.3055],'Tianjin,CN':[39.3434,117.3616],'Xi\'an,CN':[34.3416,108.9398],
    // Germany
    'Berlin,DE':[52.5200,13.4050],'Munich,DE':[48.1351,11.5820],'Hamburg,DE':[53.5753,10.0153],
    'Cologne,DE':[50.9333,6.9500],'Frankfurt,DE':[50.1109,8.6821],'Stuttgart,DE':[48.7758,9.1829],
    // France
    'Paris,FR':[48.8566,2.3522],'Lyon,FR':[45.7640,4.8357],'Marseille,FR':[43.2965,5.3698],
    'Toulouse,FR':[43.6047,1.4442],'Bordeaux,FR':[44.8378,-0.5792],
    // Canada
    'Toronto,CA':[43.6532,-79.3832],'Vancouver,CA':[49.2827,-123.1207],'Montreal,CA':[45.5017,-73.5673],
    'Calgary,CA':[51.0447,-114.0719],'Ottawa,CA':[45.4215,-75.6972],'Edmonton,CA':[53.5461,-113.4938],
    // Australia
    'Sydney,AU':[-33.8688,151.2093],'Melbourne,AU':[-37.8136,144.9631],'Brisbane,AU':[-27.4698,153.0251],
    'Perth,AU':[-31.9505,115.8605],'Adelaide,AU':[-34.9285,138.6007],
    // South Korea
    'Seoul,KR':[37.5665,126.9780],'Busan,KR':[35.1796,129.0756],'Incheon,KR':[37.4563,126.7052],
    // Japan
    'Tokyo,JP':[35.6762,139.6503],'Osaka,JP':[34.6937,135.5023],'Nagoya,JP':[35.1815,136.9066],
    'Yokohama,JP':[35.4437,139.6380],'Sapporo,JP':[43.0642,141.3469],
    // Indonesia
    'Jakarta,ID':[-6.2088,106.8456],'Surabaya,ID':[-7.2575,112.7521],'Bandung,ID':[-6.9175,107.6191],
    'Medan,ID':[3.5952,98.6722],'Makassar,ID':[-5.1477,119.4327],
    // Russia
    'Moscow,RU':[55.7558,37.6173],'St Petersburg,RU':[59.9343,30.3351],'Novosibirsk,RU':[54.9884,82.9357],
    // Saudi Arabia
    'Riyadh,SA':[24.7136,46.6753],'Jeddah,SA':[21.3891,39.8579],'Mecca,SA':[21.3891,39.8579],
    // UAE
    'Dubai,AE':[25.2048,55.2708],'Abu Dhabi,AE':[24.4539,54.3773],
    // Egypt
    'Cairo,EG':[30.0444,31.2357],'Alexandria,EG':[31.2001,29.9187],
    // Kenya
    'Nairobi,KE':[-1.2921,36.8219],'Mombasa,KE':[-4.0435,39.6682],
    // Turkey
    'Istanbul,TR':[41.0082,28.9784],'Ankara,TR':[39.9334,32.8597],'Izmir,TR':[38.4192,27.1287],
    // Mexico
    'Mexico City,MX':[19.4326,-99.1332],'Guadalajara,MX':[20.6597,-103.3496],'Monterrey,MX':[25.6866,-100.3161],
    // Argentina
    'Buenos Aires,AR':[-34.6037,-58.3816],'Córdoba,AR':[-31.4201,-64.1888],
    // South Africa
    'Johannesburg,ZA':[-26.2041,28.0473],'Cape Town,ZA':[-33.9249,18.4241],'Durban,ZA':[-29.8587,31.0218],
    // Ethiopia
    'Addis Ababa,ET':[9.0320,38.7469],
    // Philippines
    'Manila,PH':[14.5995,120.9842],'Quezon City,PH':[14.7356,121.0720],'Cebu,PH':[10.3157,123.8854],
    // Vietnam
    'Ho Chi Minh City,VN':[10.8231,106.6297],'Hanoi,VN':[21.0245,105.8412],'Da Nang,VN':[16.0471,108.2068],
    // Malaysia
    'Kuala Lumpur,MY':[3.1390,101.6869],'Johor Bahru,MY':[1.4927,103.7414],'Penang,MY':[5.4141,100.3288],
    // Thailand
    'Bangkok,TH':[13.7563,100.5018],'Chiang Mai,TH':[18.7883,98.9853],
    // Ghana
    'Accra,GH':[5.6037,-0.1870],'Kumasi,GH':[6.6885,-1.6244],
    // Morocco
    'Casablanca,MA':[33.5731,-7.5898],'Rabat,MA':[34.0209,-6.8416],'Marrakech,MA':[31.6295,-7.9811],
    // Singapore
    'Singapore,SG':[1.3521,103.8198],
    // Netherlands
    'Amsterdam,NL':[52.3676,4.9041],'Rotterdam,NL':[51.9244,4.4777],
    // Spain
    'Madrid,ES':[40.4168,-3.7038],'Barcelona,ES':[41.3851,2.1734],'Valencia,ES':[39.4699,-0.3763],
    // Italy
    'Rome,IT':[41.9028,12.4964],'Milan,IT':[45.4654,9.1859],'Naples,IT':[40.8518,14.2681],
    // Poland
    'Warsaw,PL':[52.2297,21.0122],'Kraków,PL':[50.0647,19.9450],
    // Ukraine
    'Kyiv,UA':[50.4501,30.5234],'Kharkiv,UA':[49.9935,36.2304],
    // Sweden
    'Stockholm,SE':[59.3293,18.0686],'Gothenburg,SE':[57.7089,11.9746],
    // Norway
    'Oslo,NO':[59.9139,10.7522],'Bergen,NO':[60.3913,5.3221],
    // Denmark
    'Copenhagen,DK':[55.6761,12.5683],
    // Finland
    'Helsinki,FI':[60.1699,24.9384],
    // Ireland
    'Dublin,IE':[53.3498,-6.2603],
    // Portugal
    'Lisbon,PT':[38.7223,-9.1393],'Porto,PT':[41.1579,-8.6291],
    // Switzerland
    'Zurich,CH':[47.3769,8.5417],'Geneva,CH':[46.2044,6.1432],
    // Austria
    'Vienna,AT':[48.2082,16.3738],
    // Belgium
    'Brussels,BE':[50.8503,4.3517],
    // Iran
    'Tehran,IR':[35.6892,51.3890],'Mashhad,IR':[36.2605,59.6168],
    // Iraq
    'Baghdad,IQ':[33.3152,44.3661],
    // Israel
    'Tel Aviv,IL':[32.0853,34.7818],'Jerusalem,IL':[31.7683,35.2137],
    // Jordan
    'Amman,JO':[31.9539,35.9106],
    // Lebanon
    'Beirut,LB':[33.8938,35.5018],
    // Syria
    'Damascus,SY':[33.5138,36.2765],
    // Kuwait
    'Kuwait City,KW':[29.3759,47.9774],
    // Oman
    'Muscat,OM':[23.5880,58.3829],
    // Qatar
    'Doha,QA':[25.2854,51.5310],
    // Bahrain
    'Manama,BH':[26.2235,50.5876],
    // Myanmar
    'Yangon,MM':[16.8661,96.1951],'Mandalay,MM':[21.9162,96.0891],
    // Sri Lanka
    'Colombo,LK':[6.9271,79.8612],
    // Nepal
    'Kathmandu,NP':[27.7172,85.3240],
    // Afghanistan
    'Kabul,AF':[34.5553,69.2075],
    // Uganda
    'Kampala,UG':[0.3476,32.5825],
    // Tanzania
    'Dar es Salaam,TZ':[-6.7924,39.2083],
    // DR Congo
    'Kinshasa,CD':[-4.4419,15.2663],
    // Cameroon
    'Douala,CM':[4.0511,9.7679],'Yaoundé,CM':[3.8480,11.5021],
    // Zimbabwe
    'Harare,ZW':[-17.8252,31.0335],
    // Zambia
    'Lusaka,ZM':[-15.4166,28.2833],
    // New Zealand
    'Auckland,NZ':[-36.8485,174.7633],'Wellington,NZ':[-41.2865,174.7762],
    // Misc major cities (no CC)
    'Unknown':null
};

// Try to get coords for a city+country pair
function getCityCoords(city, cc) {
    if (!city || city === 'Unknown' || city === '') return null;
    const key1 = city + ',' + (cc||'').toUpperCase();
    if (CITY_COORDS[key1]) return CITY_COORDS[key1];
    // Try without CC (won't be ambiguous for capitals)
    const key2 = city;
    if (CITY_COORDS[key2]) return CITY_COORDS[key2];
    // Add small random jitter for same-city IPs so dots don't stack
    return null;
}

// ── Country bounding boxes for "fly-to" on click ──────────────
const COUNTRY_BOUNDS = {
    BD:[[20.74,88.01],[26.63,92.68]],IN:[[6.75,68.18],[37.09,97.40]],
    US:[[24.52,-124.77],[49.38,-66.95]],CN:[[18.20,73.55],[53.56,134.77]],
    GB:[[49.96,-5.72],[58.64,1.76]],PK:[[23.69,60.87],[37.09,77.84]],
    BR:[[-33.75,-73.99],[5.27,-28.85]],NG:[[4.27,2.69],[13.90,14.68]],
    ID:[[-10.92,95.29],[5.47,141.02]],AU:[[-43.63,113.34],[-10.67,153.57]],
    DE:[[47.27,5.87],[55.06,15.04]],FR:[[41.32,-4.80],[51.09,9.56]],
    JP:[[24.04,122.93],[45.52,145.82]],RU:[[41.19,19.64],[81.86,169.01]],
    ZA:[[-34.83,16.48],[-22.13,32.89]],TR:[[35.82,25.67],[42.11,44.79]],
    SA:[[16.38,34.63],[32.16,55.67]],EG:[[22.00,24.70],[31.67,36.90]],
    ET:[[3.40,33.00],[14.89,47.98]],KE:[[-4.68,33.91],[4.62,41.90]],
    TH:[[5.64,97.34],[20.46,105.64]],VN:[[8.56,102.14],[23.39,109.46]],
    PH:[[4.59,116.93],[21.12,126.60]],MY:[[0.85,99.64],[7.35,119.27]],
    MM:[[9.77,92.18],[28.55,101.18]],KR:[[33.19,124.61],[38.61,129.58]],
    ES:[[35.17,-9.31],[43.79],[3.33]],IT:[[36.62,6.61],[47.09,18.52]],
    MX:[[14.53,-117.13],[32.72,-86.74]],AR:[[-55.06,-73.56],[-21.78,-53.64]],
    CO:[[−4.23,-81.74],[12.46,-66.87]],CA:[[41.68,-141.00],[83.11,-52.62]],
    UA:[[44.39,22.14],[52.37],[40.23]],PL:[[49.00,14.12],[54.84,24.15]],
    IR:[[25.06,44.05],[39.78,63.32]],SD:[[3.49,21.83],[22.23,38.58]],
    MA:[[27.67,-13.17],[35.93],[-1.12]],DZ:[[19.06,-8.67],[37.09,11.99]],
    TN:[[30.23],[8.18],[37.55,11.58]],LY:[[19.50,9.39],[33.17,25.15]],
    MG:[[-25.61,43.22],[-11.95,50.48]],GH:[[4.74,-3.26],[11.17,1.20]],
    TZ:[[-11.74,29.34],[-0.99,40.44]],ZM:[[-18.08,21.97],[-8.22,33.70]],
    MZ:[[-26.87,30.22],[-10.47,40.84]],ZW:[[-22.42,25.24],[-15.61,33.07]],
    SN:[[12.31,-17.53],[15.08],[-11.36]],
    // Default fallbacks
    'default':null
};

// Country centroids (for dot placement when we have no city coords)
const CENTROIDS = {
    BD:[23.68,90.35],IN:[20.59,78.96],US:[37.09,-95.71],CN:[35.86,104.20],
    GB:[55.38,-3.44],PK:[30.38,69.35],BR:[-14.24,-51.93],NG:[9.08,8.68],
    ID:[-0.79,113.92],AU:[-25.27,133.78],DE:[51.17,10.45],FR:[46.23,2.21],
    JP:[36.20,138.25],RU:[61.52,105.32],ZA:[-30.56,22.94],TR:[38.96,35.24],
    SA:[23.89,45.08],EG:[26.82,30.80],ET:[9.15,40.49],KE:[-0.02,37.91],
    TH:[15.87,100.99],VN:[14.06,108.28],PH:[12.88,121.77],MY:[4.21,101.98],
    MM:[19.16,95.96],KR:[35.91,127.77],ES:[40.46,-3.75],IT:[41.87,12.57],
    MX:[23.63,-102.55],AR:[-38.42,-63.62],CO:[4.57,-74.30],CA:[56.13,-106.35],
    UA:[48.38,31.17],PL:[51.92,19.15],IR:[32.43,53.69],
    MA:[31.79,-7.09],GH:[7.95,-1.02],TZ:[-6.37,34.89],MG:[-18.77,46.87],
    MZ:[-18.67,35.53],ZA:[-30.56,22.94],ZW:[-19.01,29.15],ZM:[-13.13,27.85],
    NG:[9.08,8.68],SD:[12.86,30.22],ET:[9.15,40.49],CM:[3.85,11.50],
    KE:[-0.02,37.91],UG:[1.37,32.29],TZ:[-6.37,34.89],
    SG:[1.35,103.82],NL:[52.13,5.29],BE:[50.50,4.47],CH:[46.82,8.23],
    AT:[47.52,14.55],DK:[56.26,9.50],FI:[61.92,25.75],SE:[60.13,18.64],
    NO:[60.47,8.47],PT:[39.40,-8.22],IE:[53.41,-8.24],NZ:[-40.90,174.89],
    PG:[-6.31,143.96],KZ:[48.02,66.92],UZ:[41.38,64.59],
    SY:[34.80,38.99],IQ:[33.22,43.68],IL:[31.05,34.85],JO:[30.59,36.24],
    LB:[33.89,35.50],KW:[29.31,47.48],AE:[23.42,53.85],QA:[25.35,51.18],
    YE:[15.55,48.52],OM:[21.51,55.92],AF:[33.94,67.71],
    TM:[38.97,59.56],TJ:[38.86,71.28],KG:[41.20,74.77],MN:[46.86,103.85],
    KP:[40.34,127.51],TW:[23.70,120.96],LK:[7.87,80.77],NP:[28.39,84.12],
    MM:[19.16,95.96],KH:[12.57,104.99],LA:[19.86,102.50],
    CU:[21.52,-77.78],HT:[18.97,-72.29],DO:[18.74,-70.16],
    CL:[-35.68,-71.54],BO:[-16.29,-63.59],PE:[-9.19,-75.02],PY:[-23.44,-58.44],
    UY:[-32.52,-55.77],EC:[-1.83,-78.18],VE:[6.42,-66.59],CO:[4.57,-74.30],
    GY:[4.86,-58.93],SR:[3.92,-56.03],
    DZ:[28.03,1.66],LY:[26.34,17.23],TN:[33.89,9.54],MA:[31.79,-7.09],
    EG:[26.82,30.80],SD:[12.86,30.22],SO:[5.15,46.20],DJ:[11.83,42.59],
    ER:[15.18,39.78],MR:[21.00,-10.94],ML:[17.57,-3.99],NE:[17.61,8.08],
    TD:[15.45,18.73],BF:[12.36,-1.56],SN:[14.50,-14.45],GN:[9.95,-11.61],
    SL:[8.46,-11.78],LR:[6.43,-9.43],CI:[7.54,-5.55],GH:[7.95,-1.02],
    TG:[8.62,0.82],BJ:[9.31,2.32],CM:[3.85,11.50],CF:[6.61,20.94],
    CG:[-0.23,15.83],CD:[-4.04,21.76],GA:[-0.80,11.61],AO:[-11.20,17.87],
    ZM:[-13.13,27.85],MW:[-13.25,34.30],MZ:[-18.67,35.53],ZW:[-19.01,29.15],
    BW:[-22.33,24.68],NA:[-22.96,18.49],LS:[-29.61,28.23],SZ:[-26.52,31.46],
    MG:[-18.77,46.87],MU:[-20.35,57.55],
    IS:[64.96,-19.02],SE:[60.13,18.64],NO:[60.47,8.47],FI:[61.92,25.75],
    DK:[56.26,9.50],EE:[58.60,25.01],LV:[56.88,24.60],LT:[55.17,23.88],
    BY:[53.71,27.95],UA:[48.38,31.17],MD:[47.41,28.37],RO:[45.94,24.97],
    BG:[42.73,25.49],RS:[44.02,21.01],ME:[42.71,19.37],AL:[41.15,20.17],
    MK:[41.61,21.75],BA:[44.16,17.68],HR:[45.10,15.20],SI:[46.15,14.99],
    SK:[48.67,19.70],CZ:[49.82,15.47],HU:[47.16,19.50],AT:[47.52,14.55],
    CH:[46.82,8.23],LI:[47.14,9.56],LU:[49.82,6.13],BE:[50.50,4.47],
    NL:[52.13,5.29],GR:[39.07,21.82],CY:[35.13,33.43],MT:[35.94,14.37],
    IT:[41.87,12.57],SM:[43.94,12.46],VA:[41.90,12.45],
    PL:[51.92,19.15],DE:[51.17,10.45],FR:[46.23,2.21],MC:[43.75,7.41],
    AD:[42.55,1.60],ES:[40.46,-3.75],PT:[39.40,-8.22],IE:[53.41,-8.24],
    GB:[55.38,-3.44],IS:[64.96,-19.02],NO:[60.47,8.47],
    GE:[42.32,43.36],AM:[40.07,45.04],AZ:[40.14,47.58],TR:[38.96,35.24],
    SY:[34.80,38.99],LB:[33.89,35.50],IL:[31.05,34.85],JO:[30.59,36.24],
    IQ:[33.22,43.68],KW:[29.31,47.48],SA:[23.89,45.08],YE:[15.55,48.52],
    OM:[21.51,55.92],AE:[23.42,53.85],QA:[25.35,51.18],BH:[26.07,50.55],
    IR:[32.43,53.69],AF:[33.94,67.71],PK:[30.38,69.35],
    KZ:[48.02,66.92],UZ:[41.38,64.59],TM:[38.97,59.56],TJ:[38.86,71.28],KG:[41.20,74.77],
    MN:[46.86,103.85],CN:[35.86,104.20],JP:[36.20,138.25],
    KP:[40.34,127.51],KR:[35.91,127.77],TW:[23.70,120.96],
    IN:[20.59,78.96],BD:[23.68,90.35],NP:[28.39,84.12],LK:[7.87,80.77],
    MM:[19.16,95.96],TH:[15.87,100.99],LA:[19.86,102.50],VN:[14.06,108.28],
    KH:[12.57,104.99],MY:[4.21,101.98],SG:[1.35,103.82],PH:[12.88,121.77],
    ID:[-0.79,113.92],TL:[-8.87,125.73],BN:[4.54,114.73],
    AU:[-25.27,133.78],NZ:[-40.90,174.89],PG:[-6.31,143.96],
    FJ:[-16.58,179.41],SB:[-9.65,160.16],VU:[-15.38,166.96],
    WS:[-13.76,-172.10],TO:[-21.18,-175.20],
    CA:[56.13,-106.35],US:[37.09,-95.71],MX:[23.63,-102.55],
    GT:[15.78,-90.23],BZ:[17.19,-88.49],HN:[15.20,-86.24],SV:[13.79,-88.90],
    NI:[12.87,-85.21],CR:[9.75,-83.75],PA:[8.54,-80.78],
    CU:[21.52,-77.78],JM:[18.11,-77.30],HT:[18.97,-72.29],DO:[18.74,-70.16],
    PR:[18.22,-66.59],TT:[10.69,-61.22],
    CO:[4.57,-74.30],VE:[6.42,-66.59],GY:[4.86,-58.93],SR:[3.92,-56.03],
    BR:[-14.24,-51.93],EC:[-1.83,-78.18],PE:[-9.19,-75.02],BO:[-16.29,-63.59],
    PY:[-23.44,-58.44],AR:[-38.42,-63.62],CL:[-35.68,-71.54],UY:[-32.52,-55.77]
};

// ── Color scale (visit count → fill color) ────────────────────
function getCountryColor(count, maxCount) {
    if (!count || count === 0) return '#071224';
    const t = Math.pow(count / maxCount, 0.38);
    const stops = [
        [7,18,36],    // 0  – very dark navy
        [9,40,90],    // low
        [13,71,160],  // mid-low  → #0d47a0
        [29,111,181], // mid
        [59,130,246], // #3b82f6
        [96,165,250], // #60a5fa
        [147,197,253] // #93c5fd
    ];
    const n = stops.length - 1;
    const fi = t * n;
    const i0 = Math.floor(fi), i1 = Math.min(i0 + 1, n);
    const f  = fi - i0;
    const c0 = stops[i0], c1 = stops[i1];
    const r  = Math.round(c0[0] + f*(c1[0]-c0[0]));
    const g  = Math.round(c0[1] + f*(c1[1]-c0[1]));
    const b  = Math.round(c0[2] + f*(c1[2]-c0[2]));
    return `rgb(${r},${g},${b})`;
}

// ── Init ──────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    // Only init when the countries section is opened
    let mapInitialized = false;

    function tryInit() {
        if (!mapInitialized) {
            initMap();
            mapInitialized = true;
        }
    }

    // Init on section nav click
    document.querySelectorAll('.db-nav-item').forEach(item => {
        item.addEventListener('click', function () {
            if (this.dataset.section === 'countries') {
                setTimeout(tryInit, 120);
            }
        });
    });

    // Also check if countries section is visible on load
    const sec = document.getElementById('countries');
    if (sec && !sec.classList.contains('db-section--hidden')) {
        setTimeout(tryInit, 200);
    }
});

function initMap() {
    const mapData    = (typeof DB_DATA !== 'undefined') ? (DB_DATA.countryMap  || []) : [];
    const ipByCC     = (typeof DB_DATA !== 'undefined') ? (DB_DATA.ipByCountry || {}) : {};
    const totalVisits = mapData.reduce((s,d) => s + d.count, 0) || 1;
    const maxCount    = Math.max(...mapData.map(d => d.count), 1);

    // Build lookup cc→{name,count}
    const ccInfo = {};
    mapData.forEach(d => { ccInfo[d.code.toUpperCase()] = d; });

    // ── Create Leaflet map ────────────────────────────────────
    const mapEl = document.getElementById('sv-leaflet-map');
    if (!mapEl || !window.L) {
        document.getElementById('mapStatus').textContent = 'MAP ENGINE NOT LOADED';
        document.getElementById('mapStatus').className = 'sv-status-err';
        return;
    }

    const map = L.map('sv-leaflet-map', {
        center: [20, 10],
        zoom: 2,
        minZoom: 2,
        maxZoom: 18,
        zoomControl: true,
        attributionControl: true,
        worldCopyJump: true,
    });

    // CartoDB Dark Matter — perfect dark theme, crisp borders, free, no API key
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://carto.com">CartoDB</a>',
        subdomains: 'abcd',
        maxZoom: 19,
        detectRetina: true
    }).addTo(map);

    // ── State ─────────────────────────────────────────────────
    let geojsonLayer   = null;   // choropleth layer
    let dotLayerGroup  = null;   // IP dot markers
    let currentCC      = null;   // currently zoomed country
    let choroEnabled   = true;   // choropleth toggle
    let dotsEnabled    = false;  // IP dots toggle (auto-enable on zoom)
    let tooltipEl      = null;   // floating tooltip

    // Create custom tooltip div
    tooltipEl = document.createElement('div');
    tooltipEl.className = 'sv-tooltip';
    tooltipEl.style.cssText = 'display:none;position:absolute;z-index:900;';
    document.getElementById('sv-map-wrap').appendChild(tooltipEl);

    // ── Load world GeoJSON ────────────────────────────────────
    document.getElementById('mapStatus').textContent = 'LOADING GEO DATA…';

    // Try multiple CDN sources for resilience
    const GEO_URLS = [
        'https://cdn.jsdelivr.net/npm/world-atlas@2.0.2/countries-110m.json',
        'https://unpkg.com/world-atlas@2.0.2/countries-110m.json'
    ];

    function loadGeo(urlIdx) {
        if (urlIdx >= GEO_URLS.length) {
            // Fallback: show basic map without choropleth
            document.getElementById('mapStatus').textContent = 'TILE MAP ACTIVE (NO CHOROPLETH)';
            document.getElementById('mapStatus').className = 'sv-status-live';
            addIpDots(map, ipByCC, ccInfo, totalVisits);
            return;
        }
        fetch(GEO_URLS[urlIdx])
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(topo => buildChoropleth(topo))
            .catch(() => loadGeo(urlIdx + 1));
    }

    function buildChoropleth(topo) {
        // Convert TopoJSON → GeoJSON (topojson-client is not loaded; parse manually)
        // Actually Leaflet works with GeoJSON directly.
        // world-atlas 110m uses TopoJSON. We need topojson-client OR use a GeoJSON source.
        // Let's try a GeoJSON endpoint directly.
        fetch('https://raw.githubusercontent.com/datasets/geo-countries/master/data/countries.geojson')
            .then(r => { if (!r.ok) throw new Error(); return r.json(); })
            .then(geoJson => renderChoropleth(geoJson))
            .catch(() => {
                // Use the TopoJSON with inline decoder
                decodeTopo(topo);
            });
    }

    // Minimal TopoJSON decoder (arc → ring → polygon)
    function decodeTopo(topo) {
        const obj   = topo.objects.countries;
        const arcs  = topo.arcs;
        const scale = topo.transform ? topo.transform.scale : [1,1];
        const trans = topo.transform ? topo.transform.translate : [0,0];

        function decodeArc(arcIdx) {
            const reversed = arcIdx < 0;
            const arc = arcs[reversed ? ~arcIdx : arcIdx];
            let x = 0, y = 0;
            const pts = arc.map(p => { x += p[0]; y += p[1]; return [x,y]; });
            const coords = pts.map(p => [p[0]*scale[0]+trans[0], p[1]*scale[1]+trans[1]]);
            if (reversed) coords.reverse();
            return coords;
        }

        function ringToCoords(ring) {
            let coords = [];
            ring.forEach(arcIdx => { coords = coords.concat(decodeArc(arcIdx)); });
            return coords;
        }

        const features = obj.geometries.map(geom => {
            let coordinates;
            if (geom.type === 'Polygon') {
                coordinates = geom.arcs.map(ring => ringToCoords(ring));
            } else if (geom.type === 'MultiPolygon') {
                coordinates = geom.arcs.map(poly => poly.map(ring => ringToCoords(ring)));
            }
            return {
                type: 'Feature',
                id: geom.id,
                properties: { name: geom.id },
                geometry: { type: geom.type, coordinates }
            };
        });

        // Attach ISO2 via numeric ID
        const NUM_TO_CC = {"004":"AF","008":"AL","012":"DZ","024":"AO","032":"AR","036":"AU","040":"AT",
            "050":"BD","056":"BE","064":"BT","068":"BO","076":"BR","100":"BG","104":"MM","116":"KH",
            "120":"CM","124":"CA","144":"LK","152":"CL","156":"CN","170":"CO","178":"CG","180":"CD",
            "188":"CR","191":"HR","192":"CU","196":"CY","203":"CZ","208":"DK","214":"DO","218":"EC",
            "818":"EG","222":"SV","231":"ET","233":"EE","246":"FI","250":"FR","276":"DE","288":"GH",
            "300":"GR","320":"GT","324":"GN","332":"HT","340":"HN","348":"HU","356":"IN","360":"ID",
            "364":"IR","368":"IQ","372":"IE","376":"IL","380":"IT","388":"JM","392":"JP","400":"JO",
            "398":"KZ","404":"KE","408":"KP","410":"KR","414":"KW","418":"LA","422":"LB","434":"LY",
            "440":"LT","442":"LU","458":"MY","466":"ML","484":"MX","504":"MA","508":"MZ","516":"NA",
            "524":"NP","528":"NL","554":"NZ","558":"NI","566":"NG","578":"NO","586":"PK","591":"PA",
            "598":"PG","600":"PY","604":"PE","608":"PH","616":"PL","620":"PT","634":"QA","642":"RO",
            "643":"RU","646":"RW","682":"SA","686":"SN","694":"SL","703":"SK","706":"SO","710":"ZA",
            "716":"ZW","724":"ES","729":"SD","752":"SE","756":"CH","760":"SY","762":"TJ","764":"TH",
            "792":"TR","800":"UG","804":"UA","784":"AE","826":"GB","840":"US","858":"UY","860":"UZ",
            "862":"VE","704":"VN","887":"YE","894":"ZM","288":"GH","630":"PR","132":"CV","860":"UZ",
            "051":"AM","031":"AZ","268":"GE","854":"BF","72":"BW","426":"LS","694":"SL","450":"MG",
            "466":"ML","478":"MR","430":"LR","454":"MW","454":"MW","686":"SN","690":"SC","706":"SO",
            "748":"SZ","800":"UG","834":"TZ","454":"MW","562":"NI"};

        features.forEach(f => {
            const cc = NUM_TO_CC[String(f.id).padStart(3,'0')] || '';
            f.properties.cc = cc;
            if (cc && ccInfo[cc]) {
                f.properties.name  = ccInfo[cc].name;
                f.properties.count = ccInfo[cc].count;
            } else {
                f.properties.name  = '';
                f.properties.count = 0;
            }
        });

        renderChoropleth({ type:'FeatureCollection', features });
    }

    function renderChoropleth(geoJson) {
        // Attach visitor data to GeoJSON features
        geoJson.features.forEach(f => {
            const props = f.properties || {};
            // Try ISO2 from props (various GeoJSON sources use different keys)
            const cc = (props.cc || props.ISO_A2 || props.iso_a2 || props.ADM0_A3_IS || '').toUpperCase().substring(0,2);
            if (cc && ccInfo[cc] && !props.count) {
                props.cc    = cc;
                props.name  = ccInfo[cc].name;
                props.count = ccInfo[cc].count;
            }
            f.properties = props;
        });

        if (geojsonLayer) { map.removeLayer(geojsonLayer); }

        geojsonLayer = L.geoJSON(geoJson, {
            style: feature => {
                const count = feature.properties.count || 0;
                const cc    = (feature.properties.cc||'').toUpperCase();
                const info  = ccInfo[cc];
                const fill  = info ? getCountryColor(count, maxCount) : '#071224';
                return {
                    fillColor:   fill,
                    fillOpacity: info ? 0.82 : 0.65,
                    color:       info ? '#1e3a5f' : '#0d1e30',
                    weight:      info ? 0.8 : 0.5,
                    opacity:     0.9
                };
            },
            onEachFeature: (feature, layer) => {
                const props = feature.properties || {};
                const cc    = (props.cc||'').toUpperCase();
                const info  = ccInfo[cc];

                layer.on({
                    mousemove: function(e) {
                        if (!info) return;
                        const pct = ((info.count/totalVisits)*100).toFixed(1);
                        const ips = (ipByCC[cc] || []).length;
                        tooltipEl.innerHTML =
                            `<div class="sv-tt-header">
                                <span class="sv-tt-flag">${flagEmoji(cc)}</span>
                                <span class="sv-tt-name">${info.name}</span>
                            </div>
                            <div class="sv-tt-row"><span class="sv-tt-lbl">Visits</span><span class="sv-tt-val">${info.count.toLocaleString()}</span></div>
                            <div class="sv-tt-row"><span class="sv-tt-lbl">Share</span><span class="sv-tt-pct">${pct}%</span></div>
                            <div class="sv-tt-row"><span class="sv-tt-lbl">Unique IPs</span><span class="sv-tt-val">${ips}</span></div>
                            <div class="sv-tt-hint">CLICK TO DRILL DOWN · SCROLL TO ZOOM</div>`;
                        tooltipEl.style.display = 'block';
                        // Position relative to map wrap
                        const wrap = document.getElementById('sv-map-wrap');
                        const wr   = wrap.getBoundingClientRect();
                        const mx   = e.originalEvent.clientX - wr.left;
                        const my   = e.originalEvent.clientY - wr.top;
                        tooltipEl.style.left = Math.min(mx + 16, wrap.clientWidth - 210) + 'px';
                        tooltipEl.style.top  = Math.max(my - 10, 8) + 'px';
                        // Highlight
                        layer.setStyle({ color:'#3b82f6', weight:1.5, fillOpacity:0.95 });
                    },
                    mouseout: function() {
                        tooltipEl.style.display = 'none';
                        if (geojsonLayer) geojsonLayer.resetStyle(layer);
                    },
                    click: function() {
                        if (!info) return;
                        tooltipEl.style.display = 'none';
                        zoomToCountry(cc, info, feature);
                    }
                });
            }
        }).addTo(map);

        // Status update
        document.getElementById('mapStatus').textContent =
            'FEED ACTIVE // ' + mapData.length + ' NODES TRACKED';
        document.getElementById('mapStatus').className = 'sv-status-live';

        // Add IP pulse dots for top countries at world zoom
        addTopCountryDots();
    }

    // ── Zoom to country ───────────────────────────────────────
    function zoomToCountry(cc, info, feature) {
        currentCC = cc;

        // Fly to country bounds
        try {
            const bounds = feature ? L.geoJSON(feature).getBounds() : null;
            if (bounds && bounds.isValid()) {
                map.flyToBounds(bounds, { padding:[30,30], maxZoom:10, duration:0.8 });
            } else if (CENTROIDS[cc]) {
                map.flyTo(CENTROIDS[cc], 6, { duration:0.8 });
            }
        } catch(e) {
            if (CENTROIDS[cc]) map.flyTo(CENTROIDS[cc], 6, { duration:0.8 });
        }

        // Show drill-down panel
        openDrillPanel(cc, info);

        // Show IP dots for this country
        showIpDots(cc);

        // Show back button
        document.getElementById('svBtnBack').classList.add('sv-visible');
    }

    // ── IP dots: top countries pulse markers at world zoom ────
    function addTopCountryDots() {
        if (dotLayerGroup) { map.removeLayer(dotLayerGroup); }
        dotLayerGroup = L.layerGroup().addTo(map);

        const topN = mapData.slice(0, 18);
        topN.forEach(item => {
            const cc = item.code.toUpperCase();
            const center = CENTROIDS[cc];
            if (!center) return;

            const icon = L.divIcon({
                className: '',
                html: `<div class="sv-dot-outer" title="${item.name}: ${item.count} visits">
                           <div class="sv-dot-inner"></div>
                       </div>`,
                iconSize: [16,16],
                iconAnchor: [8,8]
            });

            const marker = L.marker(center, { icon, zIndexOffset: 100 });
            marker.on('click', function() {
                if (geojsonLayer) {
                    // Find and click the layer for this CC
                    geojsonLayer.eachLayer(l => {
                        const cc2 = (l.feature?.properties?.cc||'').toUpperCase();
                        if (cc2 === cc) {
                            zoomToCountry(cc, item, l.feature);
                        }
                    });
                } else {
                    map.flyTo(center, 6, {duration:0.8});
                    openDrillPanel(cc, item);
                    showIpDots(cc);
                    document.getElementById('svBtnBack').classList.add('sv-visible');
                    currentCC = cc;
                }
            });
            dotLayerGroup.addLayer(marker);
        });
    }

    // ── IP dots: individual IP markers zoomed into country ────
    let ipDotGroup = null;

    function showIpDots(cc) {
        if (ipDotGroup) { map.removeLayer(ipDotGroup); ipDotGroup = null; }

        const ips = ipByCC[cc] || [];
        if (!ips.length) return;

        ipDotGroup = L.layerGroup().addTo(map);

        const usedCoords = {};

        ips.forEach(ipItem => {
            let coords = getCityCoords(ipItem.city, cc);

            if (!coords) {
                // Use country centroid with random scatter
                const cent = CENTROIDS[cc];
                if (!cent) return;
                coords = [
                    cent[0] + (Math.random() - 0.5) * 2.5,
                    cent[1] + (Math.random() - 0.5) * 2.5
                ];
            } else {
                // Jitter to separate same-city IPs
                const key = coords[0].toFixed(2) + ',' + coords[1].toFixed(2);
                usedCoords[key] = (usedCoords[key] || 0) + 1;
                const n = usedCoords[key];
                const angle = (n * 137.5) * Math.PI / 180;
                const r = 0.04 * Math.sqrt(n);
                coords = [coords[0] + r*Math.sin(angle), coords[1] + r*Math.cos(angle)];
            }

            const multi  = ipItem.count > 1;
            const dotColor = multi ? '#f97316' : '#22c55e';
            const ringColor = multi ? 'rgba(249,115,22,0.35)' : 'rgba(34,197,94,0.35)';

            const icon = L.divIcon({
                className: '',
                html: `<div style="
                    width:12px;height:12px;border-radius:50%;cursor:pointer;
                    background:${dotColor};
                    box-shadow:0 0 0 3px ${ringColor}, 0 0 8px ${dotColor};
                    transition:transform .15s;
                " onmouseover="this.style.transform='scale(1.6)'"
                   onmouseout="this.style.transform='scale(1)'"></div>`,
                iconSize: [12,12],
                iconAnchor: [6,6]
            });

            const marker = L.marker(coords, { icon, zIndexOffset: 200 });

            // Build popup HTML
            const city   = [ipItem.city, ipItem.region].filter(Boolean).join(', ');
            const popup  = L.popup({ offset:[0,-4] }).setContent(`
                <div class="sv-popup">
                    <div class="sv-popup-ip">${ipItem.ip}</div>
                    <div class="sv-popup-row"><span class="sv-popup-lbl">Location</span><span class="sv-popup-val">${city || 'Unknown'}</span></div>
                    <div class="sv-popup-row"><span class="sv-popup-lbl">Visits</span><span class="sv-popup-val">${ipItem.count}</span></div>
                    <div class="sv-popup-row"><span class="sv-popup-lbl">Device</span><span class="sv-popup-val">${ipItem.device||'?'}</span></div>
                    <div class="sv-popup-row"><span class="sv-popup-lbl">Browser</span><span class="sv-popup-val">${ipItem.browser||'?'}</span></div>
                    <div class="sv-popup-row"><span class="sv-popup-lbl">Last seen</span><span class="sv-popup-val">${ipItem.last ? ipItem.last.substring(0,16) : '?'}</span></div>
                    <div class="sv-popup-links">
                        <a class="sv-popup-link" href="https://ipinfo.io/${ipItem.ip}" target="_blank">ipinfo.io</a>
                        <a class="sv-popup-link" href="https://www.abuseipdb.com/check/${ipItem.ip}" target="_blank">AbuseIPDB</a>
                        <a class="sv-popup-link" href="https://who.is/whois-ip/ip-whois-lookup/${ipItem.ip}" target="_blank">WHOIS</a>
                    </div>
                </div>`);

            marker.bindPopup(popup);
            ipDotGroup.addLayer(marker);
        });
    }

    // ── Drill-down panel ──────────────────────────────────────
    function openDrillPanel(cc, info) {
        const panel = document.getElementById('svDrillPanel');
        const body  = document.getElementById('svDrillBody');
        const ips   = ipByCC[cc] || [];

        document.getElementById('svDrillFlag').textContent  = flagEmoji(cc);
        document.getElementById('svDrillTitle').textContent = info.name;
        document.getElementById('svDrillMeta').textContent  =
            info.count.toLocaleString() + ' visits · ' + ips.length + ' IPs';

        if (!ips.length) {
            body.innerHTML = '<div class="sv-drill-empty">No IP data for this country</div>';
        } else {
            body.innerHTML = ips.map(ip => {
                const city    = [ip.city, ip.region].filter(Boolean).join(', ') || 'Unknown';
                const multi   = ip.count > 1;
                return `<div class="sv-ip-row">
                    <div class="sv-ip-top">
                        <span class="sv-ip-addr">${ip.ip}</span>
                        <span class="sv-ip-badge${multi?' sv-multi':''}">${ip.count}×</span>
                    </div>
                    <div class="sv-ip-city">📍 ${city}</div>
                    <div class="sv-ip-meta">${ip.device||''} · ${ip.browser||''}</div>
                    <div class="sv-ip-links">
                        <a class="sv-ip-link" href="https://ipinfo.io/${ip.ip}" target="_blank">ipinfo</a>
                        <a class="sv-ip-link" href="https://www.abuseipdb.com/check/${ip.ip}" target="_blank">AbuseIPDB</a>
                        <a class="sv-ip-link" href="https://who.is/whois-ip/ip-whois-lookup/${ip.ip}" target="_blank">WHOIS</a>
                    </div>
                </div>`;
            }).join('');
        }

        panel.classList.add('open');
    }

    function closeDrillPanel() {
        document.getElementById('svDrillPanel').classList.remove('open');
        if (ipDotGroup) { map.removeLayer(ipDotGroup); ipDotGroup = null; }
    }

    // ── Control buttons ───────────────────────────────────────
    document.getElementById('svDrillClose').addEventListener('click', function() {
        closeDrillPanel();
        currentCC = null;
        document.getElementById('svBtnBack').classList.remove('sv-visible');
    });

    document.getElementById('svBtnBack').addEventListener('click', function() {
        closeDrillPanel();
        currentCC = null;
        map.flyTo([20,10], 2, { duration:0.7 });
        this.classList.remove('sv-visible');
        addTopCountryDots();
    });

    document.getElementById('svBtnReset').addEventListener('click', function() {
        closeDrillPanel();
        currentCC = null;
        map.flyTo([20,10], 2, { duration:0.6 });
        document.getElementById('svBtnBack').classList.remove('sv-visible');
        addTopCountryDots();
    });

    document.getElementById('svBtnChoro').addEventListener('click', function() {
        choroEnabled = !choroEnabled;
        this.classList.toggle('sv-active', choroEnabled);
        if (geojsonLayer) {
            choroEnabled ? map.addLayer(geojsonLayer) : map.removeLayer(geojsonLayer);
        }
    });

    document.getElementById('svBtnDots').addEventListener('click', function() {
        dotsEnabled = !dotsEnabled;
        this.classList.toggle('sv-active', dotsEnabled);
        if (dotsEnabled && currentCC) {
            showIpDots(currentCC);
        } else if (!dotsEnabled) {
            if (ipDotGroup) { map.removeLayer(ipDotGroup); ipDotGroup = null; }
        }
    });

    // ── Zoom level HUD ────────────────────────────────────────
    map.on('zoomend', function() {
        const z = map.getZoom();
        const el = document.getElementById('svHudZoom');
        if (el) el.textContent = 'ZOOM: ' + z;

        // Auto-show IP dots when zoomed in on a country
        if (z >= 5 && currentCC) {
            showIpDots(currentCC);
            document.getElementById('svBtnDots').classList.add('sv-active');
            dotsEnabled = true;
        } else if (z < 4) {
            if (ipDotGroup) { map.removeLayer(ipDotGroup); ipDotGroup = null; }
            if (!currentCC) addTopCountryDots();
        }
    });

    // ── HUD clock ─────────────────────────────────────────────
    function tickClock() {
        const el = document.getElementById('svHudTime');
        if (el) el.textContent = new Date().toUTCString().substring(17,25) + ' UTC';
    }
    tickClock();
    setInterval(tickClock, 1000);

    // ── Hide tooltip on map move ──────────────────────────────
    map.on('movestart', function() {
        if (tooltipEl) tooltipEl.style.display = 'none';
    });

    // ── Start loading ─────────────────────────────────────────
    loadGeo(0);

} // end initMap

})();
</script>
</body>
</html>