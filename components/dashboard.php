<?php
// dashboard.php - Visitor Analytics Dashboard
session_start();

// Include the visitor counter functions
require_once 'visitor-counter.php';

// Simple authentication (you should implement proper authentication)
define('DASHBOARD_PASSWORD', 'your_secure_password_here'); // Change this!

$vcd_is_authenticated = isset($_SESSION['vcd_authenticated']) && $_SESSION['vcd_authenticated'] === true;

// Handle login
if (isset($_POST['vcd_login'])) {
    if ($_POST['vcd_password'] === DASHBOARD_PASSWORD) {
        $_SESSION['vcd_authenticated'] = true;
        $vcd_is_authenticated = true;
    } else {
        $vcd_error = 'Invalid password';
    }
}

// Handle logout
if (isset($_GET['vcd_logout'])) {
    session_destroy();
    header('Location: dashboard.php');
    exit;
}

// API endpoints for dashboard
if ($vcd_is_authenticated && isset($_GET['vcd_action'])) {
    header('Content-Type: application/json');
    $vcd_pdo = vc_get_db_connection();
    
    switch ($_GET['vcd_action']) {
        case 'get_visitors':
            $vcd_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $vcd_per_page = 20;
            $vcd_offset = ($vcd_page - 1) * $vcd_per_page;
            
            $vcd_total = $vcd_pdo->query("SELECT COUNT(*) as count FROM vc_visitors")->fetch()['count'];
            
            $vcd_stmt = $vcd_pdo->prepare("SELECT * FROM vc_visitors 
                                           ORDER BY vc_visit_date DESC 
                                           LIMIT ? OFFSET ?");
            $vcd_stmt->execute([$vcd_per_page, $vcd_offset]);
            $vcd_visitors = $vcd_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'visitors' => $vcd_visitors,
                'total' => $vcd_total,
                'page' => $vcd_page,
                'pages' => ceil($vcd_total / $vcd_per_page)
            ]);
            break;
            
        case 'get_analytics':
            // Today's visitors
            $vcd_today = $vcd_pdo->query("SELECT COUNT(*) as count FROM vc_visitors 
                                          WHERE DATE(vc_visit_date) = CURDATE()")->fetch()['count'];
            
            // This week's visitors
            $vcd_week = $vcd_pdo->query("SELECT COUNT(*) as count FROM vc_visitors 
                                         WHERE vc_visit_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch()['count'];
            
            // This month's visitors
            $vcd_month = $vcd_pdo->query("SELECT COUNT(*) as count FROM vc_visitors 
                                          WHERE MONTH(vc_visit_date) = MONTH(CURRENT_DATE()) 
                                          AND YEAR(vc_visit_date) = YEAR(CURRENT_DATE())")->fetch()['count'];
            
            // Browser stats
            $vcd_browsers = $vcd_pdo->query("SELECT vc_browser, COUNT(*) as count 
                                             FROM vc_visitors 
                                             GROUP BY vc_browser 
                                             ORDER BY count DESC 
                                             LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            
            // OS stats
            $vcd_os = $vcd_pdo->query("SELECT vc_os, COUNT(*) as count 
                                       FROM vc_visitors 
                                       GROUP BY vc_os 
                                       ORDER BY count DESC 
                                       LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            
            // Device stats
            $vcd_devices = $vcd_pdo->query("SELECT vc_device, COUNT(*) as count 
                                            FROM vc_visitors 
                                            GROUP BY vc_device")->fetchAll(PDO::FETCH_ASSOC);
            
            // Daily visitors for chart (last 30 days)
            $vcd_daily = $vcd_pdo->query("SELECT DATE(vc_visit_date) as date, COUNT(*) as count 
                                          FROM vc_visitors 
                                          WHERE vc_visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                                          GROUP BY DATE(vc_visit_date) 
                                          ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'today' => $vcd_today,
                'week' => $vcd_week,
                'month' => $vcd_month,
                'browsers' => $vcd_browsers,
                'os' => $vcd_os,
                'devices' => $vcd_devices,
                'daily' => $vcd_daily
            ]);
            break;
    }
    exit;
}

// If not authenticated, show login page
if (!$vcd_is_authenticated) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Login</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="vcd-login-container">
        <div class="vcd-login-card">
            <div class="vcd-login-header">
                <h1>Visitor Analytics</h1>
                <p>Dashboard Login</p>
            </div>
            <form method="POST" class="vcd-login-form">
                <?php if (isset($vcd_error)): ?>
                    <div class="vcd-error-message"><?php echo htmlspecialchars($vcd_error); ?></div>
                <?php endif; ?>
                <div class="vcd-form-group">
                    <label for="vcd_password">Password</label>
                    <input type="password" id="vcd_password" name="vcd_password" required autofocus>
                </div>
                <button type="submit" name="vcd_login" class="vcd-login-button">
                    <span>Access Dashboard</span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
exit;
}

// Dashboard page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Analytics Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="vcd-dashboard">
        <!-- Sidebar -->
        <aside class="vcd-sidebar">
            <div class="vcd-sidebar-header">
                <h1 class="vcd-logo">Analytics</h1>
            </div>
            <nav class="vcd-nav">
                <a href="#" class="vcd-nav-item vcd-active" data-vcd-section="overview">
                    <span class="vcd-nav-icon">📊</span>
                    <span>Overview</span>
                </a>
                <a href="#" class="vcd-nav-item" data-vcd-section="visitors">
                    <span class="vcd-nav-icon">👥</span>
                    <span>Visitors</span>
                </a>
                <a href="#" class="vcd-nav-item" data-vcd-section="geography">
                    <span class="vcd-nav-icon">🌍</span>
                    <span>Geography</span>
                </a>
                <a href="#" class="vcd-nav-item" data-vcd-section="technology">
                    <span class="vcd-nav-icon">💻</span>
                    <span>Technology</span>
                </a>
            </nav>
            <div class="vcd-sidebar-footer">
                <a href="?vcd_logout=1" class="vcd-logout-link">
                    <span class="vcd-nav-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="vcd-main">
            <!-- Header -->
            <header class="vcd-header">
                <h2 class="vcd-page-title">Dashboard Overview</h2>
                <div class="vcd-header-actions">
                    <button class="vcd-refresh-btn" id="vcdRefreshBtn">
                        <span>🔄</span>
                        Refresh
                    </button>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="vcd-stats-grid">
                <div class="vcd-stat-card">
                    <div class="vcd-stat-icon vcd-stat-icon-blue">📈</div>
                    <div class="vcd-stat-content">
                        <div class="vcd-stat-label">Today</div>
                        <div class="vcd-stat-value" id="vcdStatToday">-</div>
                    </div>
                </div>
                <div class="vcd-stat-card">
                    <div class="vcd-stat-icon vcd-stat-icon-purple">📅</div>
                    <div class="vcd-stat-content">
                        <div class="vcd-stat-label">This Week</div>
                        <div class="vcd-stat-value" id="vcdStatWeek">-</div>
                    </div>
                </div>
                <div class="vcd-stat-card">
                    <div class="vcd-stat-icon vcd-stat-icon-green">📊</div>
                    <div class="vcd-stat-content">
                        <div class="vcd-stat-label">This Month</div>
                        <div class="vcd-stat-value" id="vcdStatMonth">-</div>
                    </div>
                </div>
                <div class="vcd-stat-card">
                    <div class="vcd-stat-icon vcd-stat-icon-orange">🌐</div>
                    <div class="vcd-stat-content">
                        <div class="vcd-stat-label">All Time</div>
                        <div class="vcd-stat-value" id="vcdStatTotal">-</div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="vcd-charts-grid">
                <div class="vcd-chart-card">
                    <h3 class="vcd-chart-title">Visitor Trend (Last 30 Days)</h3>
                    <div class="vcd-chart-container">
                        <canvas id="vcdDailyChart"></canvas>
                    </div>
                </div>
                <div class="vcd-chart-card">
                    <h3 class="vcd-chart-title">Device Distribution</h3>
                    <div class="vcd-chart-container">
                        <canvas id="vcdDeviceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Technology Stats -->
            <div class="vcd-tech-grid">
                <div class="vcd-tech-card">
                    <h3 class="vcd-tech-title">Top Browsers</h3>
                    <div class="vcd-tech-list" id="vcdBrowserList">
                        <div class="vcd-loading">Loading...</div>
                    </div>
                </div>
                <div class="vcd-tech-card">
                    <h3 class="vcd-tech-title">Operating Systems</h3>
                    <div class="vcd-tech-list" id="vcdOsList">
                        <div class="vcd-loading">Loading...</div>
                    </div>
                </div>
            </div>

            <!-- Visitors Table -->
            <div class="vcd-table-card">
                <div class="vcd-table-header">
                    <h3 class="vcd-table-title">Recent Visitors</h3>
                    <div class="vcd-table-actions">
                        <input type="text" class="vcd-search-input" placeholder="Search..." id="vcdSearchInput">
                    </div>
                </div>
                <div class="vcd-table-container">
                    <table class="vcd-table">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>Browser</th>
                                <th>OS</th>
                                <th>Device</th>
                                <th>Visit Date</th>
                            </tr>
                        </thead>
                        <tbody id="vcdVisitorsTable">
                            <tr>
                                <td colspan="6" class="vcd-loading">Loading visitors...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="vcd-pagination" id="vcdPagination">
                    <!-- Pagination will be inserted here -->
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>
