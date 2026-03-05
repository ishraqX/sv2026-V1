<?php
// ============================================================
// dashboard.php — Sound Vision Security Analytics Dashboard
// ENHANCED WITH: Threat scoring, Bot detection, Heatmap,
//                Referer analysis, Session timeline, Export logs
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

// Handle export request
if (isset($_GET['export']) && in_array($_GET['export'], ['csv', 'json'])) {
    if (empty($_SESSION['sv_admin']) && !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1','::1'], true)) {
        die('Unauthorized');
    }
    
    $userDataFile  = __DIR__ . '/data/user-data.txt';
    if (!file_exists($userDataFile)) die('No data available');
    
    $lines = file($userDataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $rows  = [];
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
    
    if ($_GET['export'] === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="visitor_log_' . date('Y-m-d_His') . '.csv"');
        echo "DateTime,IP,Country,City,Device,OS,Browser,Referer,Page\n";
        foreach ($rows as $r) {
            echo sprintf('"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $r['datetime'], $r['ip'], $r['country'], $r['city'],
                $r['device'], $r['os'], $r['browser'], $r['referer'], $r['page']
            );
        }
    } else {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="visitor_log_' . date('Y-m-d_His') . '.json"');
        echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ── Security Analysis Functions ──────────────────────────
function sv_is_bot_ua(string $ua): bool {
    $botPatterns = [
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget',
        'python', 'java(?!script)', 'perl', 'ruby', 'php', 'node',
        'go-http-client', 'slurp', 'bingbot', 'googlebot', 'yandex'
    ];
    foreach ($botPatterns as $p) {
        if (preg_match("/$p/i", $ua)) return true;
    }
    return false;
}

function sv_calculate_threat_score(array $ipData, array $allRows): int {
    $score = 0;
    $count = $ipData['count'] ?? 1;
    
    // High visit frequency
    if ($count >= 20) $score += 30;
    elseif ($count >= 10) $score += 15;
    elseif ($count >= 5) $score += 5;
    
    // Check for bot User Agent (from visitor data)
    if (isset($ipData['isBot']) && $ipData['isBot']) $score += 25;
    
    // Check for suspicious referer patterns
    if (isset($ipData['referer'])) {
        if (preg_match('/^(https?:|)\/\//i', $ipData['referer'])) $score += 10; // Bad referer
    }
    
    // Check for repeated requests in short time (scan-like behavior)
    if (isset($ipData['requests_per_hour']) && $ipData['requests_per_hour'] > 50) $score += 20;
    
    // Filter country check
    $suspicious_countries = ['xx', 'unknown'];
    if (in_array(strtolower($ipData['countryCode'] ?? 'xx'), $suspicious_countries)) $score += 10;
    
    return min($score, 100);
}

function sv_categorize_threat(int $score): array {
    if ($score >= 70) return ['level' => 'CRITICAL', 'color' => '#ef4444', 'icon' => '⚠️'];
    if ($score >= 50) return ['level' => 'HIGH', 'color' => '#f97316', 'icon' => '⚡'];
    if ($score >= 30) return ['level' => 'MEDIUM', 'color' => '#eab308', 'icon' => '⚔️'];
    if ($score >= 15) return ['level' => 'LOW', 'color' => '#3b82f6', 'icon' => '🔍'];
    return ['level' => 'SAFE', 'color' => '#22c55e', 'icon' => '✓'];
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

// ── Security Analysis: Threat Scoring & Bot Detection ──
$ipThreats = []; // IP => [score, level, isBot, threats[]]
$hourlyTraffic = []; // Hour => count
$refererData = []; // Referer => count
$threatAlerts = []; // Array of high-threat IPs
$sessionTimeline = []; // IP => [timestamps]
$userAgents = []; // UA => count

foreach ($rows as $r) {
    // Extract hour from datetime
    $dt = $r['datetime'];
    $hour = date('H', strtotime($dt));
    $hourlyTraffic[$hour] = ($hourlyTraffic[$hour] ?? 0) + 1;
    
    // Referer analysis
    $ref = $r['referer'] ?: 'Direct';
    $refererData[$ref] = ($refererData[$ref] ?? 0) + 1;
    
    // Session timeline per IP
    $ip = $r['ip'];
    if (!isset($sessionTimeline[$ip])) $sessionTimeline[$ip] = [];
    $sessionTimeline[$ip][] = $dt;
    
    // User Agent stats
    $ua = $r['browser'] . ' / ' . $r['os'];
    $userAgents[$ua] = ($userAgents[$ua] ?? 0) + 1;
}

// Calculate threat scores for each IP
foreach ($ipCounts as $ip => $info) {
    $isBot = false;
    $botCount = 0;
    
    // Check if this IP made bot requests
    foreach ($rows as $r) {
        if ($r['ip'] === $ip && sv_is_bot_ua($r['browser'] ?? '')) {
            $isBot = true;
            $botCount++;
        }
    }
    
    $ipCounts[$ip]['isBot'] = $isBot;
    $ipCounts[$ip]['requests_per_hour'] = $info['count'] > 0 ? ceil($info['count'] / 24) : 0;
    
    $threatScore = sv_calculate_threat_score($ipCounts[$ip], $rows);
    $threatLevel = sv_categorize_threat($threatScore);
    
    $ipThreats[$ip] = [
        'score' => $threatScore,
        'level' => $threatLevel['level'],
        'color' => $threatLevel['color'],
        'icon' => $threatLevel['icon'],
        'isBot' => $isBot,
        'visits' => $info['count']
    ];
    
    if ($threatScore >= 30) {
        $threatAlerts[] = [
            'ip' => $ip,
            'score' => $threatScore,
            'level' => $threatLevel['level'],
            'color' => $threatLevel['color'],
            'icon' => $threatLevel['icon'],
            'country' => $info['country'],
            'visits' => $info['count'],
            'isBot' => $isBot
        ];
    }
}

// Sort threat alerts by score descending
usort($threatAlerts, fn($a, $b) => $b['score'] <=> $a['score']);
$threatAlerts = array_slice($threatAlerts, 0, 20); // Top 20 threats

// Pad hourly traffic to 24 hours
for ($h = 0; $h < 24; $h++) {
    if (!isset($hourlyTraffic[$h])) $hourlyTraffic[$h] = 0;
}
ksort($hourlyTraffic);

// Top referers
arsort($refererData);
$topReferers = array_slice($refererData, 0, 10);

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
$hourlyJson = json_encode(array_values($hourlyTraffic), JSON_UNESCAPED_UNICODE);
$ipThreatsJson = json_encode($ipThreats, JSON_UNESCAPED_UNICODE);
$threatAlertsJson = json_encode($threatAlerts, JSON_UNESCAPED_UNICODE);

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
    <!-- Leaflet for world map (no external GeoJSON fetch needed) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <style>
    /* ── Map Section Styles ── */
    #map-container {
        position: relative;
        background: #060a12;
        border-radius: 8px;
        overflow: hidden;
        min-height: 420px;
    }
    #world-map-leaflet {
        width: 100%;
        height: 420px;
        background: #040810;
        border-radius: 6px;
        z-index: 1;
    }
    .leaflet-container { background: #040810 !important; }
    .leaflet-control-zoom a { background: #0d1f35 !important; color: #3b82f6 !important; border-color: #1a3a5c !important; }
    .leaflet-control-zoom a:hover { background: #1a3a5c !important; }
    .leaflet-control-attribution { background: rgba(4,8,16,0.75) !important; color: #334155 !important; font-size: 9px !important; }
    .leaflet-control-attribution a { color: #334155 !important; }
    .sv-map-tt .leaflet-tooltip-content, .sv-map-tt { background: transparent !important; border: none !important; box-shadow: none !important; }
    .sv-tt-inner { background: rgba(6,10,18,0.96); border: 1px solid rgba(59,130,246,0.4); border-radius: 8px; padding: 10px 14px; font-family: 'Space Grotesk',sans-serif; min-width: 160px; }
    .sv-tt-head  { font-size: 14px; font-weight: 700; color: #f1f5f9; margin-bottom: 4px; }
    .sv-tt-visits{ font-family: 'Space Mono',monospace; color: #3b82f6; font-size: 13px; }
    .sv-tt-pct   { color: #64748b; font-size: 11px; margin-top: 2px; }
    .sv-pulse { border-radius: 50%; border: 2px solid; opacity: 0; animation: sv-pulse-anim 2.2s ease-out infinite; position: absolute; }
    @keyframes sv-pulse-anim { 0% { transform: scale(0.3); opacity: 0.8; } 100% { transform: scale(1); opacity: 0; } }

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
        <a href="#overview"    class="db-nav-item active" data-section="overview">
            <i class="fas fa-gauge-high"></i><span>Overview</span>
        </a>
        <a href="#security"    class="db-nav-item" data-section="security">
            <i class="fas fa-shield-alt"></i><span>Security Threats</span>
        </a>
        <a href="#heatmap"     class="db-nav-item" data-section="heatmap">
            <i class="fas fa-fire"></i><span>Traffic Heatmap</span>
        </a>
        <a href="#referers"    class="db-nav-item" data-section="referers">
            <i class="fas fa-link"></i><span>Referer Analysis</span>
        </a>
        <a href="#countries"   class="db-nav-item" data-section="countries">
            <i class="fas fa-earth-asia"></i><span>Countries + Map</span>
        </a>
        <a href="#devices"     class="db-nav-item" data-section="devices">
            <i class="fas fa-mobile-screen-button"></i><span>Devices</span>
        </a>
        <a href="#visitors"    class="db-nav-item" data-section="visitors">
            <i class="fas fa-users"></i><span>Visitors Log</span>
        </a>
        <a href="#iptracker"   class="db-nav-item" data-section="iptracker">
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
            <div style="display:flex;gap:8px">
                <a href="dashboard.php?export=csv" class="db-back-btn" title="Export as CSV"><i class="fas fa-download"></i> CSV</a>
                <a href="dashboard.php?export=json" class="db-back-btn" title="Export as JSON"><i class="fas fa-download"></i> JSON</a>
            </div>
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
    <!-- SECTION: Security Threats & Alerts            -->
    <!-- ══════════════════════════════════════════════ -->
    <section class="db-section db-section--hidden" id="security">
        <div class="db-section-header">
            <h2><i class="fas fa-shield-alt" style="color:#ef4444;margin-right:8px"></i>Security Analysis &amp; Threat Alerts</h2>
            <span class="db-stat-pill"><?= count($threatAlerts) ?> threats detected · <?= count($ipThreats) ?> IPs analyzed</span>
        </div>

        <!-- Threat Summary Stats -->
        <div class="db-kpi-grid" style="margin-bottom:20px">
            <?php 
            $critical = count(array_filter($threatAlerts, fn($t) => $t['level'] === 'CRITICAL'));
            $high = count(array_filter($threatAlerts, fn($t) => $t['level'] === 'HIGH'));
            $medium = count(array_filter($threatAlerts, fn($t) => $t['level'] === 'MEDIUM'));
            ?>
            <div class="db-kpi db-kpi--red">
                <div class="db-kpi-icon"><i class="fas fa-fire"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num"><?= $critical ?></div>
                    <div class="db-kpi-label">Critical Threats</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-fire"></i></div>
            </div>
            <div class="db-kpi db-kpi--orange">
                <div class="db-kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num"><?= $high ?></div>
                    <div class="db-kpi-label">High Risk IPs</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
            <div class="db-kpi db-kpi--yellow">
                <div class="db-kpi-icon"><i class="fas fa-flag"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num"><?= $medium ?></div>
                    <div class="db-kpi-label">Medium Risk</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-flag"></i></div>
            </div>
            <div class="db-kpi db-kpi--green">
                <div class="db-kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="db-kpi-body">
                    <div class="db-kpi-num"><?= count($ipThreats) - count($threatAlerts) ?></div>
                    <div class="db-kpi-label">Benign IPs</div>
                </div>
                <div class="db-kpi-bg-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>

        <!-- Threat Alerts Panel -->
        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-exclamation-circle"></i> Security Alerts</h3>
                <span class="db-badge"><?= count($threatAlerts) ?> flagged IPs</span>
            </div>
            <?php if (empty($threatAlerts)): ?>
            <div style="padding:40px;text-align:center;color:#22c55e">
                <i class="fas fa-check-double" style="font-size:32px;margin-bottom:12px;display:block"></i>
                <strong>All Clear!</strong> No suspicious activity detected.
            </div>
            <?php else: ?>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>Threat Level</th>
                            <th>IP Address</th>
                            <th>Score</th>
                            <th>Visits</th>
                            <th>Country</th>
                            <th>Bot?</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($threatAlerts as $alert): ?>
                        <tr style="border-left: 4px solid <?= $alert['color'] ?>">
                            <td>
                                <span class="threat-badge" style="background: <?= $alert['color'] ?>20; color: <?= $alert['color'] ?>; border: 1px solid <?= $alert['color'] ?>40">
                                    <?= $alert['icon'] ?> <?= $alert['level'] ?>
                                </span>
                            </td>
                            <td class="db-ip-cell"><?= htmlspecialchars($alert['ip']) ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span style="font-weight:700;color:#e2e8f0"><?= $alert['score'] ?></span>
                                    <div style="width:60px;height:4px;background:rgba(255,255,255,0.1);border-radius:2px;overflow:hidden">
                                        <div style="width:<?= $alert['score'] ?>%;height:100%;background:<?= $alert['color'] ?>;transition:width 0.3s"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="db-num"><strong><?= $alert['visits'] ?></strong></td>
                            <td><?= flag($countries[array_search($alert['country'], array_column($rows, 'country'))][0] ?? 'XX') ?? '🌐' ?> <?= htmlspecialchars($alert['country']) ?></td>
                            <td><?= $alert['isBot'] ? '<span style="color:#f97316">🤖 Bot</span>' : '<span style="color:#22c55e">Human</span>' ?></td>
                            <td><button class="db-btn-small" onclick="alert('IP: ' + '<?= addslashes($alert['ip']) ?>' + '\\nVisits: ' + '<?= $alert['visits'] ?>' + '\\nThreat Score: ' + '<?= $alert['score'] ?>')">Review</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Threat Score Distribution Chart -->
        <div class="db-charts-row">
            <div class="db-chart-card db-chart-card--wide">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-bar"></i> Threat Score Distribution</h3>
                </div>
                <div class="db-chart-body" style="height:240px">
                    <canvas id="chartThreatDist"></canvas>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION: Hourly Traffic Heatmap              -->
    <!-- ══════════════════════════════════════════════ -->
    <section class="db-section db-section--hidden" id="heatmap">
        <div class="db-section-header">
            <h2><i class="fas fa-fire" style="color:#f97316;margin-right:8px"></i>Hourly Traffic Heatmap</h2>
            <span class="db-stat-pill">24-hour distribution</span>
        </div>

        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-clock"></i> Traffic by Hour (UTC)</h3>
            </div>
            <div style="padding:20px">
                <div class="heatmap-container">
                    <?php for ($h = 0; $h < 24; $h++): 
                        $val = $hourlyTraffic[$h] ?? 0;
                        $maxH = max($hourlyTraffic) ?: 1;
                        $pct = ($val / $maxH) * 100;
                        $opacity = max(0.2, $pct / 100);
                    ?>
                    <div class="heatmap-cell" style="--val: <?= $pct ?>%; --opacity: <?= $opacity ?>" title="<?= sprintf('%02d:00 - %02d submissions', $h, (int)$val) ?>">
                        <div class="heatmap-label"><?= sprintf('%02d', $h) ?></div>
                        <div class="heatmap-value"><?= $val ?></div>
                    </div>
                    <?php endfor; ?>
                </div>
                <div style="margin-top:20px;display:flex;justify-content:space-between;font-size:11px;color:#64748b">
                    <span>Peak: <strong style="color:#f97316"><?= max($hourlyTraffic) ?? 0 ?></strong> visits</span>
                    <span>Average: <strong style="color:#3b82f6"><?= ceil(array_sum($hourlyTraffic) / 24) ?></strong> visits/hour</span>
                    <span>Total: <strong style="color:#22c55e"><?= array_sum($hourlyTraffic) ?></strong> visits</span>
                </div>
            </div>
        </div>

        <!-- Heatmap Chart -->
        <div class="db-charts-row">
            <div class="db-chart-card db-chart-card--wide">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-line"></i> Hourly Traffic Chart</h3>
                </div>
                <div class="db-chart-body" style="height:280px">
                    <canvas id="chartHourly"></canvas>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION: Referer Analysis                     -->
    <!-- ══════════════════════════════════════════════ -->
    <section class="db-section db-section--hidden" id="referers">
        <div class="db-section-header">
            <h2><i class="fas fa-link" style="color:#3b82f6;margin-right:8px"></i>Referer Analysis</h2>
            <span class="db-stat-pill"><?= count($refererData) ?> unique sources</span>
        </div>

        <div class="db-charts-row">
            <div class="db-chart-card db-chart-card--wide">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-pie"></i> Traffic by Referer</h3>
                </div>
                <div class="db-chart-body" style="height:300px">
                    <canvas id="chartReferer"></canvas>
                </div>
            </div>
        </div>

        <!-- Referer Table -->
        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-list"></i> Referer Sources</h3>
                <span class="db-badge"><?= count($refererData) ?> sources</span>
            </div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Referer Source</th>
                            <th>Visits</th>
                            <th>% of Total</th>
                            <th>Distribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; $maxRef = max(array_values($refererData) ?: [1]);
                        $sortedRef = $refererData;
                        arsort($sortedRef);
                        foreach ($sortedRef as $ref => $count): ?>
                        <tr>
                            <td class="db-rank"><?= $rank++ ?></td>
                            <td style="max-width:300px;overflow:auto;font-size:12px;font-family:'Space Mono',monospace">
                                <?php if ($ref === 'Direct'): ?>
                                    <span style="color:#22c55e"><i class="fas fa-arrow-right"></i> Direct Traffic</span>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($ref) ?>" target="_blank" style="color:#3b82f6;text-decoration:none" title="<?= htmlspecialchars($ref) ?>">
                                        <?= htmlspecialchars(strlen($ref) > 50 ? substr($ref, 0, 47) . '...' : $ref) ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="db-num"><?= number_format($count) ?></td>
                            <td class="db-num"><?= $totalUnique > 0 ? round($count/$totalUnique*100, 1) : 0 ?>%</td>
                            <td style="min-width:120px">
                                <div class="db-progress">
                                    <div class="db-progress-bar db-progress-bar--blue" style="width:<?= $maxRef > 0 ? round($count/$maxRef*100) : 0 ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
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
                    <div id="world-map-leaflet"></div>
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
            <h2><i class="fas fa-network-wired" style="color:#3b82f6;margin-right:8px"></i>IP Tracker &amp; Session Analysis</h2>
            <span class="db-stat-pill"><?= count($ipCounts) ?> unique IPs · <?= count(array_filter($ipThreats, fn($t) => $t['score'] >= 30)) ?> flagged</span>
        </div>

        <!-- IP Threat Analysis -->
        <div class="db-card" style="margin-bottom:20px">
            <div class="db-card-header">
                <h3><i class="fas fa-fingerprint"></i> Top IPs by Threat Score</h3>
                <span class="db-badge"><?= count($ipThreats) ?> IPs analyzed</span>
            </div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>IP Address</th>
                            <th>Threat Score</th>
                            <th>Level</th>
                            <th>Visits</th>
                            <th>Bot?</th>
                            <th>Country</th>
                            <th>Last Seen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; 
                        $sortedThreats = array_slice(
                            array_filter($ipThreats, fn($t) => $t['score'] >= 15),
                            0, 30
                        );
                        usort($sortedThreats, fn($a, $b) => strcmp(key($sortedThreats), key($sortedThreats)));
                        
                        foreach ($ipThreats as $ip => $threat):
                            if ($threat['score'] < 15) continue;
                        ?>
                        <tr style="border-left: 3px solid <?= $threat['color'] ?>">
                            <td class="db-rank"><?= $rank++ ?></td>
                            <td class="db-ip-cell"><?= htmlspecialchars($ip) ?></td>
                            <td>
                                <span style="font-weight:700;color:<?= $threat['color'] ?>"><?= $threat['score'] ?>/100</span>
                            </td>
                            <td>
                                <span class="threat-badge" style="background: <?= $threat['color'] ?>20; color: <?= $threat['color'] ?>; border: 1px solid <?= $threat['color'] ?>40">
                                    <?= $threat['icon'] ?> <?= $threat['level'] ?>
                                </span>
                            </td>
                            <td class="db-num"><strong><?= $threat['visits'] ?></strong></td>
                            <td><?= $threat['isBot'] ? '<span style="color:#f97316">🤖</span>' : '<span style="color:#22c55e">✓</span>' ?></td>
                            <td><?= htmlspecialchars($ipCounts[$ip]['country'] ?? 'Unknown') ?></td>
                            <td class="db-mono" style="font-size:11px;color:#64748b"><?= htmlspecialchars($ipCounts[$ip]['last'] ?? 'N/A') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ipThreats)): ?>
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:#64748b">All IPs appear benign.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top IPs by Visit Count -->
        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-crown"></i> Top IPs by Visit Frequency</h3>
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
                            <th>Threat Level</th>
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
                            
                            $threatLvl = $ipThreats[$ip] ?? ['level' => 'UNKNOWN', 'color' => '#64748b', 'icon' => '?'];
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
                            <td>
                                <span style="color:<?= $threatLvl['color'] ?>;font-weight:700"><?= $threatLvl['icon'] ?> <?= $threatLvl['level'] ?></span>
                            </td>
                            <td class="db-mono" style="font-size:11px;color:#64748b"><?= htmlspecialchars($info['last']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($topIPs)): ?>
                        <tr><td colspan="10" style="text-align:center;padding:40px;color:#64748b">No IP data yet.</td></tr>
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
    countryMap: <?= $countryMapJson ?>,
    hourly:   <?= $hourlyJson ?>,
    ipThreats: <?= $ipThreatsJson ?>,
    threatAlerts: <?= $threatAlertsJson ?>,
    topReferers: <?= json_encode(array_slice($refererData, 0, 10), JSON_UNESCAPED_UNICODE) ?>
};
</script>
<script src="dashboard.js"></script>
<script src="world_map.js"></script>
</body>
</html>