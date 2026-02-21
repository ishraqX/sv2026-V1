<?php
// ============================================================
// dashboard.php — Sound Vision Analytics Dashboard
// ============================================================

// ── Dashboard Password Protection ────────────────────────────
// Change this password before deploying!
define('SV_DASH_PASS', 'soundvision2024');

if (session_status() === PHP_SESSION_NONE) session_start();

// Handle login form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sv_pass'])) {
    if ($_POST['sv_pass'] === SV_DASH_PASS) {
        $_SESSION['sv_admin']     = true;
        $_SESSION['sv_admin_exp'] = time() + 3600; // 1 hour
    } else {
        $sv_login_error = true;
    }
}

// Session expiry
if (!empty($_SESSION['sv_admin_exp']) && time() > $_SESSION['sv_admin_exp']) {
    unset($_SESSION['sv_admin'], $_SESSION['sv_admin_exp']);
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['sv_admin'], $_SESSION['sv_admin_exp']);
    header('Location: dashboard.php'); exit;
}

// Gate: show login page if not authenticated
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
$todayStr  = date('Y-m-d');
$todayCount = 0;

foreach ($rows as $r) {
    // Country
    $c = $r['country'] ?: 'Unknown';
    $countries[$c] = ($countries[$c] ?? 0) + 1;

    // Device
    $d = $r['device'] ?: 'Unknown';
    $devices[$d] = ($devices[$d] ?? 0) + 1;

    // Browser
    $b = $r['browser'] ?: 'Unknown';
    $browsers[$b] = ($browsers[$b] ?? 0) + 1;

    // OS
    $o = $r['os'] ?: 'Unknown';
    $os_list[$o] = ($os_list[$o] ?? 0) + 1;

    // Daily
    $day = substr($r['datetime'], 0, 10);
    $daily[$day] = ($daily[$day] ?? 0) + 1;
    if ($day === $todayStr) $todayCount++;
}

arsort($countries); arsort($devices); arsort($browsers); arsort($os_list);
ksort($daily);

// Top 15 countries for pie chart
$top15 = array_slice($countries, 0, 15, true);

// Last 30 days for chart
$last30 = [];
for ($i = 29; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $last30[$d] = $daily[$d] ?? 0;
}

// Recent visitors (last 50, newest first)
$recent = array_slice(array_reverse($rows), 0, 50);

// Encode for JS
$top15Json  = json_encode($top15);
$last30Json = json_encode($last30);
$devJson    = json_encode($devices);
$browJson   = json_encode($browsers);
$osJson     = json_encode($os_list);

function flag(string $cc): string {
    if (strlen($cc) !== 2) return '🌐';
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
            <i class="fas fa-earth-asia"></i><span>Countries</span>
        </a>
        <a href="#devices"   class="db-nav-item" data-section="devices">
            <i class="fas fa-mobile-screen-button"></i><span>Devices</span>
        </a>
        <a href="#visitors"  class="db-nav-item" data-section="visitors">
            <i class="fas fa-users"></i><span>Visitors Log</span>
        </a>
    </nav>

    <div class="db-sidebar-footer">
        <div class="db-live-dot"></div>
        <span><?= $onlineCount ?> online now</span>
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

    <!-- ── SECTION: Overview ── -->
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
            <!-- Daily Traffic Line Chart -->
            <div class="db-chart-card db-chart-card--wide">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-area"></i> Daily Traffic (Last 30 Days)</h3>
                </div>
                <div class="db-chart-body">
                    <canvas id="chartDaily" height="100"></canvas>
                </div>
            </div>

            <!-- Country Pie -->
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-pie"></i> Top 15 Countries</h3>
                </div>
                <div class="db-chart-body">
                    <canvas id="chartCountry" height="260"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="db-charts-row db-charts-row--3">
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fas fa-mobile-alt"></i> Devices</h3></div>
                <div class="db-chart-body">
                    <canvas id="chartDevice" height="200"></canvas>
                </div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fas fa-browser"></i> Browsers</h3></div>
                <div class="db-chart-body">
                    <canvas id="chartBrowser" height="200"></canvas>
                </div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fab fa-windows"></i> Operating Systems</h3></div>
                <div class="db-chart-body">
                    <canvas id="chartOS" height="200"></canvas>
                </div>
            </div>
        </div>

    </section>

    <!-- ── SECTION: Countries Table ── -->
    <section class="db-section db-section--hidden" id="countries">
        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-earth-asia"></i> All Countries</h3>
                <span class="db-badge"><?= count($countries) ?> countries</span>
            </div>
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Country</th>
                            <th>Visitors</th>
                            <th>Share</th>
                            <th>Bar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; $maxC = max(array_values($countries) ?: [1]);
                        foreach ($countries as $cn => $cv): ?>
                        <tr>
                            <td class="db-rank"><?= $rank++ ?></td>
                            <td>
                                <?php
                                // Find country code
                                $foundCode = 'XX';
                                foreach ($rows as $rr) { if ($rr['country'] === $cn) { $foundCode = $rr['countryCode']; break; } }
                                echo flag($foundCode) . ' ' . htmlspecialchars($cn);
                                ?>
                            </td>
                            <td class="db-num"><?= number_format($cv) ?></td>
                            <td class="db-num"><?= $totalUnique > 0 ? round($cv / $totalUnique * 100, 1) : 0 ?>%</td>
                            <td>
                                <div class="db-progress">
                                    <div class="db-progress-bar" style="width:<?= $maxC > 0 ? round($cv/$maxC*100) : 0 ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ── SECTION: Devices ── -->
    <section class="db-section db-section--hidden" id="devices">
        <div class="db-charts-row db-charts-row--3">
            <!-- Device donut -->
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fas fa-mobile-alt"></i> Device Types</h3></div>
                <div class="db-chart-body"><canvas id="chartDeviceSec" height="240"></canvas></div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fas fa-browser"></i> Browsers</h3></div>
                <div class="db-chart-body"><canvas id="chartBrowserSec" height="240"></canvas></div>
            </div>
            <div class="db-chart-card">
                <div class="db-chart-header"><h3><i class="fab fa-windows"></i> OS</h3></div>
                <div class="db-chart-body"><canvas id="chartOSSec" height="240"></canvas></div>
            </div>
        </div>

        <!-- Devices table -->
        <div class="db-charts-row">
            <div class="db-card">
                <div class="db-card-header"><h3>Browser Breakdown</h3></div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead><tr><th>Browser</th><th>Visitors</th><th>Share</th></tr></thead>
                        <tbody>
                            <?php $maxB = max(array_values($browsers) ?: [1]);
                            foreach ($browsers as $bn => $bv): ?>
                            <tr>
                                <td><?= htmlspecialchars($bn) ?></td>
                                <td class="db-num"><?= $bv ?></td>
                                <td>
                                    <div class="db-progress">
                                        <div class="db-progress-bar db-progress-bar--purple" style="width:<?= $maxB > 0 ? round($bv/$maxB*100) : 0 ?>%"></div>
                                    </div>
                                </td>
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
                        <thead><tr><th>OS</th><th>Visitors</th><th>Share</th></tr></thead>
                        <tbody>
                            <?php $maxO = max(array_values($os_list) ?: [1]);
                            foreach ($os_list as $on => $ov): ?>
                            <tr>
                                <td><?= htmlspecialchars($on) ?></td>
                                <td class="db-num"><?= $ov ?></td>
                                <td>
                                    <div class="db-progress">
                                        <div class="db-progress-bar db-progress-bar--teal" style="width:<?= $maxO > 0 ? round($ov/$maxO*100) : 0 ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- ── SECTION: Visitors Log ── -->
    <section class="db-section db-section--hidden" id="visitors">
        <div class="db-card">
            <div class="db-card-header">
                <h3><i class="fas fa-users"></i> Recent Visitors</h3>
                <span class="db-badge">Last 50 entries</span>
            </div>
            <div class="db-table-wrap">
                <table class="db-table db-table--log">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>IP</th>
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
                        <?php foreach ($recent as $r): ?>
                        <tr>
                            <td class="db-mono db-nowrap"><?= htmlspecialchars($r['datetime']) ?></td>
                            <td class="db-mono"><?= htmlspecialchars(substr($r['ip'], 0, -3) . '***') ?></td>
                            <td><?= flag($r['countryCode']) ?> <?= htmlspecialchars($r['country']) ?></td>
                            <td><?= htmlspecialchars($r['city']) ?></td>
                            <td><span class="db-tag db-tag--<?= strtolower($r['device']) ?>"><?= htmlspecialchars($r['device']) ?></span></td>
                            <td><?= htmlspecialchars($r['os']) ?></td>
                            <td><?= htmlspecialchars($r['browser']) ?></td>
                            <td class="db-truncate" title="<?= htmlspecialchars($r['referer']) ?>"><?= htmlspecialchars($r['referer']) ?></td>
                            <td class="db-truncate" title="<?= htmlspecialchars($r['page']) ?>"><?= htmlspecialchars($r['page']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent)): ?>
                        <tr><td colspan="9" style="text-align:center;padding:40px;color:#64748b;">No visitor data yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

</main>

<!-- ── Data for JS ── -->
<script>
const DB_DATA = {
    top15:   <?= $top15Json ?>,
    last30:  <?= $last30Json ?>,
    devices: <?= $devJson ?>,
    browsers:<?= $browJson ?>,
    os:      <?= $osJson ?>,
    online:  <?= $onlineCount ?>
};
</script>
<script src="dashboard.js"></script>
</body>
</html>