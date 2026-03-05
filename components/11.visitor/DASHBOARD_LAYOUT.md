# Security Analytics Dashboard - Visual Navigation Guide

## 📊 Dashboard Map & Structure

```
┌─────────────────────────────────────────────────────────────────────┐
│  🔐 SOUND VISION ANALYTICS - Security Enhanced v2.0                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────┐   ┌─────────────────────────────────────────────────┐ │
│  │ SIDEBAR │   │ TOP BAR                                         │ │
│  │         │   │ Last updated: TODAY · HH:MM                     │ │
│  │ 📊 Over │   │                                                 │ │
│  │ 🛡️ Sec  │   │ [CSV] [JSON] [← Back] [Logout]                 │ │
│  │ 🔥 Heat │   │                                                 │ │
│  │ 🔗 Ref  │   └─────────────────────────────────────────────────┘ │
│  │ 🌍 Coun │   ┌─────────────────────────────────────────────────┐ │
│  │ 📱 Dev  │   │ MAIN CONTENT AREA                               │ │
│  │ 👥 Vis  │   │ (Changes based on menu selection)               │ │
│  │ 🌐 IP   │   │                                                 │ │
│  │         │   │ Populated dynamically                           │ │
│  │ [●] N   │   │                                                 │ │
│  │ online  │   └─────────────────────────────────────────────────┘ │
│  │ now     │                                                     │
│  └─────────┘                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🗂️ Section Breakdown

### 1️⃣ **OVERVIEW** (📊)
```
┌─ KPI Cards Row (5 cards) ────────────────────────────────┐
│  [Total Views]  [Unique Sessions]  [Today]  [Online]    │
│  [Countries]                                              │
└──────────────────────────────────────────────────────────┘

┌─ Charts Row 1 ───────────────────────────────────────────┐
│  [Daily Traffic Chart (30 days)]  [Top 15 Countries]     │
└──────────────────────────────────────────────────────────┘

┌─ Charts Row 2 ───────────────────────────────────────────┐
│  [Devices]  [Browsers]  [Operating Systems]              │
└──────────────────────────────────────────────────────────┘
```

**Features:**
- Live KPI updates
- Interactive charts
- Historical data (30 days)

---

### 2️⃣ **SECURITY THREATS** (🛡️) - NEW
```
┌─ Threat Summary Stats ────────────────────────────────────┐
│  [🔴 Critical]  [🟠 High]  [🟡 Medium]  [🟢 Benign]       │
│   Threats        Risk IPs    Risk        Good              │
└──────────────────────────────────────────────────────────┘

┌─ Security Alerts Table ───────────────────────────────────┐
│  Level  │ IP Address │ Score │ Visits │ Country │ Bot   │
│ 🔴 CRIT │ 192.0.2.1  │ 92/100│  523   │ Unknown │ 🤖    │
│ 🟠 HIGH │ 203.0.113.2│ 68/100│  142   │ China   │ ✓     │
│ [... top 20 flagged IPs ...]                              │
└──────────────────────────────────────────────────────────┘

┌─ Threat Distribution Chart ───────────────────────────────┐
│  [Bar Chart: CRITICAL | HIGH | MEDIUM | LOW | SAFE]      │
│  Shows count of IPs in each threat level                  │
└──────────────────────────────────────────────────────────┘
```

**Features:**
- Real-time threat calculation
- 5-level threat classification
- Bot detection flags
- Quick review buttons

---

### 3️⃣ **TRAFFIC HEATMAP** (🔥) - NEW
```
┌─ 24-Hour Heatmap Grid ────────────────────────────────────┐
│  [00] [01] [02] [03] [04] [05] [06] [07] [08] [09] ...   │
│  ░░░  ░░░  ░░░  ░░░  ░░░  ░░░  ░░░  ░░░  ▓▓▓  ▓▓▓       │
│   0    1    2    3    4    5    6    7   150  200         │
│                                                            │
│  [...continues 0-23 hours...]                            │
│                                                            │
│  Peak: 250 visits | Average: 45 visits/hour | Total: 1080 │
└──────────────────────────────────────────────────────────┘

┌─ Hourly Traffic Chart ────────────────────────────────────┐
│  [Smooth line graph showing 24-hour trend]                │
│  X-axis: Hours (00:00 - 23:00 UTC)                        │
│  Y-axis: Number of visits                                 │
└──────────────────────────────────────────────────────────┘
```

**Features:**
- Visual intensity = traffic volume
- Interactive hover tooltips
- Peak hour highlighting
- Trend analysis

---

### 4️⃣ **REFERER ANALYSIS** (🔗) - NEW
```
┌─ Traffic by Referer Pie Chart ───────────────────────────┐
│              Direct Traffic                              │
│           /                \                              │
│        Google               \    Bing                    │
│       /       \               \  /                        │
│    Facebook   Other Sites..     \/                        │
│                                                           │
│  (Visual pie chart with color legend)                     │
└──────────────────────────────────────────────────────────┘

┌─ Referrer Sources Table ──────────────────────────────────┐
│ # │ Referer Source           │ Visits │ % Share │ Bar    │
│ 1 │ ➡️ Direct Traffic        │  450   │ 42.1%  │ █████░ │
│ 2 │ 🔗 google.com            │  280   │ 26.2%  │ ███░░░ │
│ 3 │ 🔗 facebook.com          │  156   │ 14.6%  │ ██░░░░ │
│ 4 │ 🔗 twitter.com           │   89   │ 8.3%   │ █░░░░░ │
│ ... (top 10 sources)                                     │
└──────────────────────────────────────────────────────────┘
```

**Features:**
- Traffic source attribution
- Clickable referrer URLs
- Percentage distribution
- Direct vs external breakdown

---

### 5️⃣ **COUNTRIES + MAP** (🌍)
```
┌─ Live World Map ──────────────────────────────────────────┐
│  [Leaflet Interactive Map]                                │
│  • Live pulse markers (green dots)                         │
│  • Hacker-style scan lines                                │
│  • HUD status displays                                    │
│  • Hover for country stats                                │
│                                                            │
│  Low ===→ Color Intensity ===→ High                       │
│  Legend: ● Active | ■ No data                             │
└──────────────────────────────────────────────────────────┘

┌─ All Countries Table ─────────────────────────────────────┐
│ # │ Country      │ Visitors │ Share % │ Distribution    │
│ 1 │ 🇺🇸 USA      │  3,920   │ 36.7%   │ ██████████░░░░ │
│ 2 │ 🇬🇧 UK       │  1,850   │ 17.3%   │ █████░░░░░░░░░ │
│ 3 │ 🇨🇦 Canada   │  1,240   │ 11.6%   │ ███░░░░░░░░░░░ │
│ ... (all countries)                                       │
└──────────────────────────────────────────────────────────┘
```

**Features:**
- Interactive map
- Country statistics
- Visit distribution
- Geographic insights

---

### 6️⃣ **DEVICES** (📱)
```
┌─ Device Distribution ─────────────────────────────────────┐
│  [Doughnut Chart]  [Doughnut Chart]  [Bar Chart]          │
│  Devices            Browsers          OS                  │
└──────────────────────────────────────────────────────────┘

┌─ Browser Breakdown Table ─────────────────────────────────┐
│ Browser    │ Visitors │ Share % │ Distribution           │
│ Chrome     │  4,850   │ 45.4%   │ ██████████░░░░░░░░░░  │
│ Safari     │  2,340   │ 21.9%   │ █████░░░░░░░░░░░░░░░░ │
│ Firefox    │  1,680   │ 15.7%   │ ███░░░░░░░░░░░░░░░░░░ │
│ Edge       │  1,290   │ 12.1%   │ ██░░░░░░░░░░░░░░░░░░░ │
│ Others     │   340    │ 3.2%    │ ░░░░░░░░░░░░░░░░░░░░░ │
└──────────────────────────────────────────────────────────┘

┌─ OS Breakdown Table ──────────────────────────────────────┐
│ OS              │ Visitors │ Share %                     │
│ Windows 10/11   │  5,200   │ 48.7%   │ ██████████░░░░░  │
│ macOS           │  2,100   │ 19.7%   │ ████░░░░░░░░░░░  │
│ iOS             │  1,850   │ 17.3%   │ ███░░░░░░░░░░░░░ │
│ Android         │  1,340   │ 12.5%   │ ██░░░░░░░░░░░░░░ │
│ Linux           │   190    │ 1.8%    │ ░░░░░░░░░░░░░░░░ │
└──────────────────────────────────────────────────────────┘
```

**Features:**
- Device type analytics
- Browser compatibility
- OS distribution
- Detailed breakdowns

---

### 7️⃣ **VISITORS LOG** (👥)
```
┌─ Visitor Log Table ───────────────────────────────────────┐
│ DateTime │ IP Address │ Visits │ Country │ City │ Device │
│ 14:52:30 │ 192.0.2.1  │ 3x     │ 🇺🇸 US  │ NYC  │ Mobile │
│ 14:53:15 │ 203.0.113.5│ 1x     │ 🇬🇧 UK  │ LON  │ Desktop│
│ ... (Latest 100 entries)                                 │
│                                                           │
│ [More columns: OS | Browser | Referer | Page visited]   │
└──────────────────────────────────────────────────────────┘
```

**Features:**
- Complete visitor details
- Latest 100 entries
- Sortable columns
- Full session information

---

### 8️⃣ **IP TRACKER** (🌐) - ENHANCED
```
┌─ Top IPs by Threat Score ─────────────────────────────────┐
│ # │ IP Address │ Score │ Level │ Visits │ Country │ Threat
│ 1 │ 192.0.2.1  │ 92    │ 🔴    │  523   │ Unknown │ ⚠️ BOT
│ 2 │ 203.0.113.2│ 68    │ 🟠    │  142   │ China   │ ✓ Human
│ 3 │ 198.51.100 │ 45    │ 🟡    │   87   │ Russia  │ ⚠️ Bot
│ ... (IPs with threat score >= 15)                        │
└──────────────────────────────────────────────────────────┘

┌─ Top IPs by Visit Frequency ──────────────────────────────┐
│ # │ IP Address │ Visits │ Bar │ Country │ Browser │ Threat
│ 1 │ 10.0.0.50  │ 450    │ ██× │ USA     │ Chrome  │ 🟢
│ 2 │ 172.16.0.1 │ 340    │ ██  │ Canada  │ Safari  │ 🟢
│ 3 │ 192.168.1. │ 250    │ █░  │ UK      │ Firefox │ 🔵
│ ... (Top 50 IPs by frequency)                            │
└──────────────────────────────────────────────────────────┘
```

**Features:**
- Threat score analysis
- Visit frequency ranking
- Complete IP details
- Session timeline access

---

## 🎨 Color & Icon Legend

### Threat Levels
```
🔴 CRITICAL (Score 70-100) - RED (#ef4444)
   Action: Block immediately, investigate malicious activity
   
🟠 HIGH (Score 50-69) - ORANGE (#f97316)
   Action: Investigate suspicious behavior, monitor closely
   
🟡 MEDIUM (Score 30-49) - YELLOW (#eab308)
   Action: Monitor activity, watch for patterns
   
🔵 LOW (Score 15-29) - BLUE (#3b82f6)
   Action: Routine monitoring, track trends
   
🟢 SAFE (Score 0-14) - GREEN (#22c55e)
   Action: Normal traffic, benign user activity
```

### Traffic Types
```
➡️  Direct Traffic    - Users coming directly/bookmarks
🔗  Referrer Links   - External sites linking to you
🤖  Bot Activity     - Automated crawlers/scanners
✓   Human User       - Real visitor/legitimate user
🌐  Country Flag     - Geographic origin of visit
📱  Device Icon      - Mobile/Desktop/Tablet
```

### Data Points
```
📊 Dashboard      - Main overview section
🛡️  Security       - Threat analysis
🔥  Fire/Heatmap   - Hourly traffic patterns
🔗  Link/Referrer  - Traffic source analysis
🌍  Map/Countries  - Geographic data
📱  Mobile/Device  - Device type analytics
👥  Users/Visitors - Visitor log details
🌐  Network/IP     - IP address tracking
```

---

## 📱 Responsive Behavior

### Desktop (1200px+)
- Full sidebar visible
- 3-column layouts
- Full-width charts
- Side-by-side comparisons

### Tablet (768px - 1199px)
- Sidebar visible but compact
- 2-column layouts
- Stacked charts
- Scrollable tables

### Mobile (< 768px)
- Sidebar hidden (menu icon)
- Single column layouts
- Full-width elements
- Optimized for touch
- Scrollable data tables

---

## ⌨️ Keyboard Navigation

| Key | Action |
|-----|--------|
| `1` | Go to Overview |
| `2` | Go to Security |
| `3` | Go to Heatmap |
| `4` | Go to Referers |
| `5` | Go to Countries |
| `6` | Go to Devices |
| `7` | Go to Visitors |
| `8` | Go to IP Tracker |
| `F5` | Refresh dashboard |
| `ESC` | Close modals |

---

## 🔄 Data Refresh Cycle

```
Page Load
    ↓
[Load visitor data from files]
    ↓
[Calculate threat scores]
    ↓
[Aggregate hourly traffic]
    ↓
[Rank referrers]
    ↓
[Extract threat alerts]
    ↓
[Pass data to JavaScript]
    ↓
[Render charts & tables]
    ↓
[Start 30-second refresh timer]
    ↓
[Update online count every 30s]
    ↓
[Full refresh on F5 or nav click]
```

---

## 🚀 Quick Access Commands

**Direct URL Navigation:**
```
/dashboard.php                    - Overview (default)
/dashboard.php?section=overview   - Overview
/dashboard.php?section=security   - Security Threats
/dashboard.php?section=heatmap    - Traffic Heatmap
/dashboard.php?section=referers   - Referer Analysis
/dashboard.php?section=countries  - Countries Map
/dashboard.php?section=devices    - Device Analytics
/dashboard.php?section=visitors   - Visitor Log
/dashboard.php?section=iptracker  - IP Tracker

/dashboard.php?export=csv         - Export as CSV
/dashboard.php?export=json        - Export as JSON
/dashboard.php?logout=1           - Logout session
```

---

## 📞 Support Resources

**Documentation Files:**
1. `IMPLEMENTATION_SUMMARY.md` - Complete project overview
2. `SECURITY_FEATURES.md` - Technical feature details
3. `SECURITY_QUICK_START.md` - Analyst quick reference
4. `FEATURE_VERIFICATION.md` - Feature verification checklist

**In-Dashboard Help:**
- Hover over sections for tooltips
- Click [?] icons for more info
- Use "Review" buttons for details
- Refer to legend for symbols

---

**Dashboard Version**: 2.0 - Security Enhanced  
**Last Updated**: March 5, 2026  
**Status**: ✅ Production Ready
