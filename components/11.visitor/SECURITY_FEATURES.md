# Sound Vision Security Analytics Dashboard - Enhanced Features

## Overview
The dashboard has been significantly upgraded with comprehensive security analysis and threat detection capabilities for website security analysts.

---

## New Security Features Implemented

### 1. **Threat Scoring & Suspicious IP Flagging** 🚨
- **Threat Score Calculation** (0-100 based on):
  - High visit frequency (5+ visits = 5 points, 10+ = 15 points, 20+ = 30 points)
  - Bot User Agent detection (25 points)
  - Suspicious referrer patterns (10 points)
  - Rapid request patterns (50+ requests/hour = 20 points)
  - Unknown/suspicious country codes (10 points)
  
- **Threat Levels**:
  - 🔴 **CRITICAL** (70-100): Immediate action required
  - 🟠 **HIGH** (50-69): Investigate suspicious behavior
  - 🟡 **MEDIUM** (30-49): Monitor closely
  - 🔵 **LOW** (15-29): Low risk, routine check
  - 🟢 **SAFE** (0-14): Benign traffic

- **Security Alerts Section**: Top 20 flagged IPs with color-coded threat levels, visit counts, country information, and bot detection status.

### 2. **User Agent Analysis & Bot Detection** 🤖
- **Bot Detection Patterns**:
  - Identifies common bot signatures: `bot`, `crawler`, `spider`, `scraper`
  - Detects HTTP clients: `curl`, `wget`, `python`, `java`, `node`, `go-http-client`
  - Recognizes search engine bots: `slurp`, `bingbot`, `googlebot`, `yandex`

- **Bot Flagging**: Each IP is flagged as bot/human in all security tables
- **Display**: 🤖 Bot badge vs ✓ Human badge in threat analysis

### 3. **Hourly Traffic Heatmap** 🔥
- **24-Hour Distribution Visualization**:
  - Visual grid showing traffic volume for each hour (00:00-23:00 UTC)
  - Color intensity represents traffic volume
  - Interactive cells with hover information
  - Peak traffic hour highlighting
  - Average and total traffic statistics

- **Line Chart**: Smooth hourly traffic trend visualization
- **Statistics Display**:
  - Peak visits this hour
  - Average visits per hour
  - Total visits in 24-hour period

### 4. **Referrer Analysis** 📊
- **Source Attribution**:
  - Identifies traffic sources (Direct, organic, referral links)
  - Top 10 referrer sources ranked by traffic volume
  - Percentage share calculation for each source

- **Visualizations**:
  - Pie chart showing traffic distribution by referrer
  - Detailed table with visit counts and distribution bars
  - Direct traffic vs external referrers breakdown

### 5. **Session Timeline Per IP** ⏱️
- **Visit History**: All timestamps when a specific IP accessed the site
- **Activity Analysis**: Session timeline shows:
  - First visit timestamp
  - Last visit timestamp
  - Total number of visits
  - Device/OS/Browser on each visit
  - Page and referrer for each access

- **Integration**: Click on IP to see complete session timeline
- **Pattern Detection**: Identify bot-like patterns (rapid consecutive requests)

### 6. **Security Alerts Panel** ⚠️
- **Real-Time Threat Monitoring**:
  - Dedicated security section with alert dashboard
  - Threat level summary cards:
    - 🔥 Critical threats count
    - ⚠️ High risk IPs count
    - 🚩 Medium risk count
    - ✓ Benign IPs count
  
- **Alert Prioritization**: Sorted by threat score (highest first)
- **Quick Actions**: Review button for each threat to see full details
- **Color-Coded Risk**: Visual indicators with threat colors and icons

### 7. **Export Log Functionality** 📥
- **Multiple Format Support**:
  - **CSV Export**: `?export=csv` - Opens Excel/Google Sheets compatible format
  - **JSON Export**: `?export=json` - Structured data for API integration
  - **Auto-naming**: Timestamped filenames (e.g., `visitor_log_2026-03-05_145230.csv`)

- **Included Data**:
  - DateTime, IP, Country, City
  - Device, OS, Browser
  - Referrer, Page visited
  
- **Quick Access**: Export buttons in top navigation bar for easy access

---

## Dashboard Sections

### Navigation Structure
```
📊 Overview
  - KPI Cards (Total Views, Unique Sessions, Today's Visits, Online Now, Countries)
  - Daily Traffic Chart (30-day trend)
  - Top 15 Countries Pie Chart
  - Device/Browser/OS Distribution

🛡️ Security Threats
  - Threat Summary Statistics
  - Security Alerts Panel (Top 20 threats)
  - Threat Score Distribution Chart
  
🔥 Traffic Heatmap
  - 24-Hour Visual Heatmap Grid
  - Hourly Traffic Line Chart
  - Traffic Statistics & Peak Analysis

🔗 Referer Analysis
  - Referrer Distribution Pie Chart
  - Top 10 Referrer Sources Table
  - Traffic Source Attribution

🌍 Countries + Map
  - World Map with Live Visitor Visualization
  - Countries Table with Statistics
  - Flag emojis for country identification

📱 Devices
  - Device Type Distribution
  - Browser Usage Statistics
  - Operating System Breakdown
  - Detailed tables for each category

👥 Visitors Log
  - Latest 100 visitor entries
  - Complete session details
  - Sortable/filterable access

🌐 IP Tracker
  - Top IPs by Threat Score
  - Top IPs by Visit Frequency
  - Complete IP analysis with threat levels
```

---

## API & Data Integration

### Exported Data Variables (JavaScript)
```javascript
DB_DATA = {
  top15: {...},              // Top 15 countries
  last30: {...},             // Last 30 days traffic
  devices: {...},            // Device statistics
  browsers: {...},           // Browser statistics
  os: {...},                 // OS statistics
  hourly: [...],             // 24-hour traffic array
  ipThreats: {...},          // IP threat scores
  threatAlerts: [...],       // Top 20 threat IPs
  topReferers: {...}         // Top 10 referrers
}
```

---

## Security Analysis Functions

### `sv_is_bot_ua(string $ua): bool`
Detects bot User Agents using regex pattern matching against known bot signatures.

### `sv_calculate_threat_score(array $ipData, array $allRows): int`
Calculates threat score (0-100) based on:
- Visit frequency
- Bot detection
- Suspicious patterns
- Referrer analysis
- Country code validation

### `sv_categorize_threat(int $score): array`
Returns threat level metadata:
```php
[
  'level' => 'CRITICAL|HIGH|MEDIUM|LOW|SAFE',
  'color' => '#ef4444|#f97316|#eab308|#3b82f6|#22c55e',
  'icon' => '⚠️|⚡|⚔️|🔍|✓'
]
```

---

## User Interface Enhancements

### Color Scheme for Threats
- 🔴 Red (#ef4444): Critical threats
- 🟠 Orange (#f97316): High risk
- 🟡 Yellow (#eab308): Medium risk
- 🔵 Blue (#3b82f6): Low risk
- 🟢 Green (#22c55e): Safe/benign

### Visual Components
- **Threat Badge**: Color-coded threat level with icon
- **Heatmap Cells**: Interactive grid with gradient colors
- **Progress Bars**: Visual representation of threat scores and traffic distribution
- **KPI Cards**: Enhanced with red/orange variants for critical/high risks

### Interactive Features
- Hover effects on heatmap cells
- Review buttons for threat details
- Export buttons in header
- Filterable/sortable tables
- Real-time online user counter

---

## Export Examples

### CSV Format
```csv
DateTime,IP,Country,City,Device,OS,Browser,Referer,Page
2026-03-05 14:52:30,192.168.1.100,United States,New York,Desktop,Windows 10/11,Chrome,https://google.com,/index.php
2026-03-05 14:53:15,203.0.113.50,United Kingdom,London,Mobile,iOS,Safari,Direct,/pages/about.php
```

### JSON Format
```json
[
  {
    "datetime": "2026-03-05 14:52:30",
    "ip": "192.168.1.100",
    "country": "United States",
    "city": "New York",
    "device": "Desktop",
    "os": "Windows 10/11",
    "browser": "Chrome",
    "referer": "https://google.com",
    "page": "/index.php"
  },
  ...
]
```

---

## Database/Data Files

The system uses text-based data files in `/components/11.visitor/data/`:
- `visitor.txt`: Total visitor count
- `user-data.txt`: Complete visitor logs (pipe-delimited)
- `online-user.txt`: Currently active users (5-minute window)

**Format**: `DateTime|IP|Country|CountryCode|City|Region|Device|OS|Browser|Referer|Page`

---

## Performance & Optimization

- **Data Processing**: Real-time calculation of threat scores
- **Lazy Rendering**: Charts only render when section is viewed
- **JSON Encoding**: Efficient data serialization for JavaScript
- **Live Updates**: 30-second polls for online user count
- **Responsive Design**: Mobile-friendly dashboard layout

---

## Security Recommendations for Analysts

1. **Monitor Critical IPs**: Review any IP with threat score ≥ 70 immediately
2. **Bot Patterns**: Investigate high-visit bot IPs for potential attacks
3. **Referrer Anomalies**: Watch for unusual referrer patterns (SQL injection attempts)
4. **Traffic Spikes**: Correlate heatmap spikes with potential DoS attempts
5. **Geographic Anomalies**: Flag unexpected traffic from suspicious regions
6. **Export & Analyze**: Use CSV/JSON export for external security tools integration

---

## Updates & Maintenance

All features are integrated into:
- ✅ `/components/11.visitor/dashboard.php` - Main dashboard logic
- ✅ `/components/11.visitor/dashboard.css` - Styling
- ✅ `/components/11.visitor/dashboard.js` - Client-side interactivity
- ✅ `/components/11.visitor/world_map.js` - Map visualization

**Last Updated**: March 5, 2026
**Version**: 2.0 (Security Enhanced)
