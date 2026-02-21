<?php
// ============================================================
// visitor_counter.php — Sound Vision Visitor Tracker
// FIX: Counts EVERY page load (not session-gated)
// FIX: Stores full IP address
// ============================================================

$sv_visitorFile  = __DIR__ . '/data/visitor.txt';
$sv_userDataFile = __DIR__ . '/data/user-data.txt';
$sv_onlineFile   = __DIR__ . '/data/online-user.txt';

if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// ── Helpers ──────────────────────────────────────────────────

function sv_get_ip(): string {
    foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $k) {
        if (!empty($_SERVER[$k])) {
            $ip = trim(explode(',', $_SERVER[$k])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '127.0.0.1';
}

function sv_is_local_ip(string $ip): bool {
    return in_array($ip, ['127.0.0.1', '::1'], true)
        || (bool)preg_match('/^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.)/', $ip);
}

function sv_get_country(string $ip): array {
    if (sv_is_local_ip($ip)) {
        return ['country' => 'Localhost', 'countryCode' => 'LO', 'city' => 'Local', 'region' => 'Dev'];
    }
    $ctx  = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
    $json = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,city,regionName", false, $ctx);
    if ($json) {
        $d = json_decode($json, true);
        if (is_array($d) && ($d['status'] ?? '') === 'success') {
            return [
                'country'     => $d['country']     ?? 'Unknown',
                'countryCode' => $d['countryCode'] ?? 'XX',
                'city'        => $d['city']        ?? 'Unknown',
                'region'      => $d['regionName']  ?? 'Unknown',
            ];
        }
    }
    return ['country' => 'Unknown', 'countryCode' => 'XX', 'city' => 'Unknown', 'region' => 'Unknown'];
}

function sv_parse_ua(string $ua): array {
    $device = 'Desktop';
    if      (preg_match('/tablet|ipad|playbook|silk/i', $ua))                                 $device = 'Tablet';
    elseif  (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|iemobile/i', $ua)) $device = 'Mobile';

    $os = 'Unknown';
    if      (preg_match('/windows nt 10/i', $ua))    $os = 'Windows 10/11';
    elseif  (preg_match('/windows nt 6\.3/i', $ua))  $os = 'Windows 8.1';
    elseif  (preg_match('/windows/i', $ua))           $os = 'Windows';
    elseif  (preg_match('/mac os x/i', $ua))          $os = 'macOS';
    elseif  (preg_match('/iphone|ipad/i', $ua))       $os = 'iOS';
    elseif  (preg_match('/android/i', $ua))           $os = 'Android';
    elseif  (preg_match('/linux/i', $ua))             $os = 'Linux';

    $browser = 'Unknown';
    if      (preg_match('/edg\//i', $ua))    $browser = 'Edge';
    elseif  (preg_match('/opr\//i', $ua))    $browser = 'Opera';
    elseif  (preg_match('/chrome/i', $ua))   $browser = 'Chrome';
    elseif  (preg_match('/safari/i', $ua))   $browser = 'Safari';
    elseif  (preg_match('/firefox/i', $ua))  $browser = 'Firefox';

    return compact('device', 'os', 'browser');
}

function sv_read_lines(string $file): array {
    if (!file_exists($file)) return [];
    return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
}

function sv_write_file(string $file, string $content): void {
    file_put_contents($file, $content, LOCK_EX);
}

function sv_update_online(string $file, string $ip): int {
    $now = time(); $window = 300;
    $lines = sv_read_lines($file);
    $active = []; $found = false;
    foreach ($lines as $l) {
        [$lip, $ts] = array_pad(explode('|', $l, 2), 2, 0);
        if ($now - (int)$ts < $window) {
            if ($lip === $ip) { $active[] = "{$ip}|{$now}"; $found = true; }
            else              { $active[] = $l; }
        }
    }
    if (!$found) $active[] = "{$ip}|{$now}";
    sv_write_file($file, implode("\n", $active) . "\n");
    return count($active);
}

function sv_get_total(string $file): int {
    if (!file_exists($file)) return 0;
    $c = trim(file_get_contents($file));
    return is_numeric($c) ? (int)$c : 0;
}

function sv_increment_total(string $file): int {
    $n = sv_get_total($file) + 1;
    sv_write_file($file, (string)$n);
    return $n;
}

function sv_flag(string $cc): string {
    $cc = strtoupper(trim($cc));
    if (strlen($cc) !== 2 || in_array($cc, ['XX','LO'], true)) return '🌐';
    return implode('', array_map(fn($c) => mb_chr(0x1F1E0 + ord($c) - ord('A')), str_split($cc)));
}

// ── Session: safe start ──────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sv_ip      = sv_get_ip();
$sv_ua      = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$sv_referer = $_SERVER['HTTP_REFERER']    ?? 'Direct';
$sv_page    = $_SERVER['REQUEST_URI']     ?? '/';
$sv_now     = date('Y-m-d H:i:s');

// ── Update online tracker ────────────────────────────────────
$sv_onlineCount = sv_update_online($sv_onlineFile, $sv_ip);

// ── Count EVERY visit (not session-gated) ────────────────────
// Cache geo in session to avoid repeated IP-API calls on every page load
if (empty($_SESSION['sv_geo']) || empty($_SESSION['sv_geo_ip']) || $_SESSION['sv_geo_ip'] !== $sv_ip) {
    $sv_geo = sv_get_country($sv_ip);
    $_SESSION['sv_geo']    = $sv_geo;
    $_SESSION['sv_geo_ip'] = $sv_ip;
} else {
    $sv_geo = $_SESSION['sv_geo'];
}

$sv_dev = sv_parse_ua($sv_ua);

// Increment total on every page load
$sv_totalCount = sv_increment_total($sv_visitorFile);

// Log every visit with FULL IP
$sv_row = implode('|', [
    $sv_now,
    $sv_ip,                                           // FULL IP - no masking
    $sv_geo['country'],
    $sv_geo['countryCode'],
    $sv_geo['city'],
    $sv_geo['region'],
    $sv_dev['device'],
    $sv_dev['os'],
    $sv_dev['browser'],
    preg_replace('/\|/', ' ', $sv_referer),
    preg_replace('/\|/', ' ', $sv_page),
]);
file_put_contents($sv_userDataFile, $sv_row . "\n", FILE_APPEND | LOCK_EX);

// Display values
$sv_flag        = sv_flag($sv_geo['countryCode']);
$sv_countryDisp = htmlspecialchars($sv_geo['country']);
$sv_isLocal     = sv_is_local_ip($sv_ip);

// Dashboard visibility: localhost (dev) OR users with sv_admin session
$sv_showDash = $sv_isLocal || !empty($_SESSION['sv_admin']);
?>
<link rel="stylesheet" href="components/visitor/visitor_counter.css">

<div class="sv-ribbon" id="svRibbon">
    <div class="sv-ribbon-inner">

        <div class="sv-ribbon-brand">
            <span class="sv-ribbon-dot"></span>
            <span class="sv-ribbon-brand-text">LIVE STATS</span>
        </div>

        <div class="sv-ribbon-divider"></div>

        <!-- Total Visitors -->
        <div class="sv-ribbon-stat">
            <span class="sv-ribbon-icon">👁️</span>
            <div class="sv-ribbon-stat-body">
                <span class="sv-ribbon-num" id="svTotalCount"><?= number_format($sv_totalCount) ?></span>
                <span class="sv-ribbon-lbl">Total Visitors</span>
            </div>
        </div>

        <div class="sv-ribbon-divider"></div>

        <!-- Online Now -->
        <div class="sv-ribbon-stat">
            <span class="sv-ribbon-icon sv-ribbon-icon--pulse">
                <span class="sv-pulse"></span>
            </span>
            <div class="sv-ribbon-stat-body">
                <span class="sv-ribbon-num sv-online-num" id="svOnlineCount"><?= $sv_onlineCount ?></span>
                <span class="sv-ribbon-lbl">Online Now</span>
            </div>
        </div>

        <!-- Country (hidden on small screens) -->
        <div class="sv-ribbon-divider sv-hide-sm"></div>
        <div class="sv-ribbon-stat sv-hide-sm">
            <span class="sv-ribbon-icon">🌍</span>
            <div class="sv-ribbon-stat-body">
                <span class="sv-ribbon-num">
                    <?= $sv_flag ?> <?= $sv_isLocal ? 'Localhost <small style="opacity:.5;font-size:10px">(dev)</small>' : $sv_countryDisp ?>
                </span>
                <span class="sv-ribbon-lbl">Your Location</span>
            </div>
        </div>

        <!-- Date (hidden on medium screens) -->
        <div class="sv-ribbon-divider sv-hide-md"></div>
        <div class="sv-ribbon-stat sv-hide-md">
            <span class="sv-ribbon-icon">📅</span>
            <div class="sv-ribbon-stat-body">
                <span class="sv-ribbon-num"><?= date('d M Y') ?></span>
                <span class="sv-ribbon-lbl"><?= date('l') ?></span>
            </div>
        </div>

        <!-- Dashboard button: only visible to localhost/admin -->
        <?php if ($sv_showDash): ?>
        <div class="sv-ribbon-right">
            <a href="components/visitor/dashboard.php" target="_blank" class="sv-dash-btn">
                📊 <span class="sv-dash-txt">Dashboard</span>
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>

<script src="components/visitor/visitor_counter.js"></script>
