# Security Analytics Dashboard - Quick Start Guide

## Accessing the Dashboard

1. Navigate to: `/components/11.visitor/dashboard.php`
2. Enter password: `soundvision2024`
3. You're logged in for 1 hour (auto-refresh)

---

## Key Security Features - Quick Reference

### 🛡️ Security Threats Panel
**When to use**: Detect suspicious IPs and potential attacks

**What to look for**:
- ⚠️ **CRITICAL** IPs (red) → Review immediately
- 🔴 Threat Score > 70 → Potential malicious activity
- 🤖 Bot badge → Automated scanner/crawler detected

**Actions**:
- Click "Review" button to see IP details
- Export data for further analysis
- Track IP visit history

**Example**: An IP from an unknown country with 100+ visits and bot User-Agent = HIGH THREAT

---

### 🔥 Traffic Heatmap
**When to use**: Identify traffic patterns and unusual activity

**What to look for**:
- 📈 Peak hours (bright hot spots)
- 📉 Unusual drops in traffic
- ⏰ Off-hours traffic (might indicate bot activity)
- 🎯 Specific hour spikes (possible DDoS attempts)

**Actions**:
- Hover over cells to see exact visit count
- Correlate peaks with security alerts
- Monitor for sustained high-volume hours

**Example**: Huge spike at 3 AM UTC from bot + continuous traffic = DDoS risk

---

### 🔗 Referrer Analysis
**When to use**: Identify legitimate vs suspicious traffic sources

**What to look for**:
- ✓ Known search engines (Google, Bing) = Safe
- ⚠️ Unknown referrers = Investigate
- 🔗 External sites linking to you = Verify legitimacy
- ➡️ Direct traffic = Users coming directly/bookmarks

**Actions**:
- Click referrer URL to verify legitimacy
- Watch for SQL injection patterns in referrer
- Export suspicious referrers for security testing

**Example**: Referrer from "http://hack-site-injection.xyz" = BLOCK

---

### 🌐 IP Tracker
**When to use**: Deep dive into specific IP addresses

**Section 1 - Threat Scores**:
- Top IPs ranked by THREAT SCORE
- Shows threat level + score /100
- Flags bot activity
- Last seen timestamp

**Section 2 - Visit Frequency**:
- Top IPs ranked by VISIT COUNT
- Frequency distribution
- Complete device/browser info
- Threat level indicator

**How to investigate an IP**:
1. Find IP in either table
2. Note their threat score
3. Check if they're flagged as bot
4. Review visit frequency
5. Look at country and device
6. Cross-reference in Visitors Log

---

### 📊 Emergency Response

**Suspicious Activity Detected?**

1. **Go to Security Threats** → Check threat scores
2. **Look at Heatmap** → Correlate with time
3. **Check Referers** → Any injection attempts?
4. **Review IPs** → Threat scores and bot flags
5. **Export Data** → Send to security analyst
6. **Block/Monitor** → Implement firewall rules

---

## Understanding Threat Scores

### How the Score is Calculated:

```
Threat Score = Base Points + Modifiers

Base Points:
+ 30 pts: 20+ visits (very frequent)
+ 15 pts: 10-19 visits (frequent)
+ 5 pts:  5-9 visits (somewhat frequent)

Additional Points:
+ 25 pts: IF bot User-Agent detected
+ 10 pts: IF suspicious referrer pattern
+ 20 pts: IF 50+ requests/hour
+ 10 pts: IF unknown country

Maximum Score: 100 points
```

### Threat Level Mapping:

| Score | Level | Action | Icon |
|-------|-------|--------|------|
| 70-100 | CRITICAL | Block immediately | 🔴 |
| 50-69 | HIGH | Investigate & monitor | 🟠 |
| 30-49 | MEDIUM | Keep watching | 🟡 |
| 15-29 | LOW | Routine monitoring | 🔵 |
| 0-14 | SAFE | Normal traffic | 🟢 |

---

## Common Security Scenarios

### Scenario 1: High-Frequency Bot
```
IP: 192.0.2.123
Visits: 500+
Bot Flag: 🤖
Threat: CRITICAL
Action: Block immediately, check for vulnerability scan
```

### Scenario 2: International Spike
```
IP: 198.51.100.45
Country: Unknown country (XX)
Visits: 45 (from nowhere)
Threat: HIGH
Action: Monitor closely, possible compromised proxy
```

### Scenario 3: Referrer Injection Attempt
```
Referrer: /index.php?id=1' OR '1'='1
Threat: HIGH
Action: Review logs for SQL injection patterns
```

### Scenario 4: After-Hours Activity
```
Hour: 02:00 UTC (off-hours)
Visits: 50+ (unusual peak)
Bot: 🤖
Threat: MEDIUM-HIGH
Action: Possible automated attack, check server logs
```

---

## Export Data for Analysis

### Export to CSV
1. Click **CSV** button in top right
2. Opens in Excel / Google Sheets
3. Contains: DateTime, IP, Country, City, Device, OS, Browser, Referrer, Page

**Use cases:**
- Spreadsheet analysis
- Pivot tables for trends
- Sharing with team
- Integration with Excel tools

### Export to JSON
1. Click **JSON** button in top right
2. Contains complete structured data
3. Filename: `visitor_log_2026-03-05_145230.json`

**Use cases:**
- API integration
- Python/JavaScript analysis
- Security tool integration
- Automated alerts
- Data warehouse import

**Example Python Analysis:**
```python
import json
with open('visitor_log_2026-03-05_145230.json') as f:
    data = json.load(f)
    
# Find all bot IPs
bots = [row for row in data if 'bot' in row['browser'].lower()]
print(f"Total bots: {len(bots)}")

# Top 10 countries
from collections import Counter
countries = Counter(row['country'] for row in data)
print(countries.most_common(10))
```

---

## Dashboard Refresh & Updates

- **KPI Numbers**: Animate smoothly when page loads
- **Online Count**: Updates every 30 seconds (clicking sections won't reset)
- **Charts**: Load when section is activated
- **Real-time**: Data refreshes on page reload (F5)

---

## Best Practices

✅ **DO:**
- Check Security Threats weekly (or daily if active site)
- Review high-threat IPs immediately
- Export logs monthly for records
- Monitor heatmap for pattern anomalies
- Cross-reference IP threat scores with visit history

❌ **DON'T:**
- Ignore CRITICAL threat level IPs
- Block legitimate bots (Google, Bing) without verification
- Assume all automated traffic is malicious
- Only look at one metric (use multiple views)
- Forget to check server logs for context

---

## Troubleshooting

**No data showing in Security section?**
- Check if visitor.txt has data
- Verify data passed the security filter
- Try page reload (F5)

**Threat score seems wrong?**
- Check IP visit count
- Verify bot detection (check User-Agent)
- Review referrer for suspicious patterns
- Score updates in real-time

**Export not working?**
- Ensure you're logged in (not local access)
- Try different format (CSV vs JSON)
- Check browser download settings
- Verify data files exist in /data folder

---

## Tips for Analysts

1. **Bookmark this dashboard**: Add to favorites for quick access
2. **Set weekly reminders**: Check for threats every Monday
3. **Keep records**: Export data regularly for audit trail
4. **Watch for patterns**: Same IP at same time = bot
5. **Cross-reference**: Always verify with server logs
6. **Document findings**: Note threats + actions taken

---

## Need Help?

For questions about:
- **Features**: See SECURITY_FEATURES.md
- **Technical details**: Check dashboard.php code comments
- **Data interpretation**: Review this Quick Start Guide
- **Troubleshooting**: Check dashboard error logs

---

**Version**: 2.0 - Security Enhanced  
**Last Updated**: March 5, 2026  
**Dashboard Location**: `/components/11.visitor/dashboard.php`
