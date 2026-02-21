<?php
// ping.php — lightweight AJAX endpoint: returns online count + total
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache');

$onlineFile  = __DIR__ . '/data/online-user.txt';
$visitorFile = __DIR__ . '/data/visitor.txt';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get IP
$ip = '127.0.0.1';
foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $k) {
    if (!empty($_SERVER[$k])) {
        $candidate = trim(explode(',', $_SERVER[$k])[0]);
        if (filter_var($candidate, FILTER_VALIDATE_IP)) { $ip = $candidate; break; }
    }
}

$now    = time();
$window = 300;
$lines  = [];
$found  = false;

if (file_exists($onlineFile)) {
    $raw = file($onlineFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($raw as $l) {
        [$lip, $ts] = array_pad(explode('|', $l, 2), 2, 0);
        if ($now - (int)$ts < $window) {
            if ($lip === $ip) { $lines[] = "{$ip}|{$now}"; $found = true; }
            else              { $lines[] = $l; }
        }
    }
}
if (!$found) $lines[] = "{$ip}|{$now}";
file_put_contents($onlineFile, implode("\n", $lines) . "\n", LOCK_EX);

$total = file_exists($visitorFile) ? (int)trim(file_get_contents($visitorFile)) : 0;

echo json_encode(['online' => count($lines), 'total' => $total]);
