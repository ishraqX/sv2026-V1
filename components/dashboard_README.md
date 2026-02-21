# Visitor Counter & Analytics Dashboard

A comprehensive, modern visitor tracking system with real-time analytics dashboard. Tracks visitors silently without requiring user consent, capturing IP addresses, geolocation, browser information, and more.

## Features

### Visitor Counter (Public)
- **Compact 1-inch height section** (600px width)
- Real-time visitor count with animated counter
- Interactive pie chart showing visitor distribution by country
- Top 5 countries list with flags and visitor counts
- Fully responsive design for all devices
- Auto-refreshes every 30 seconds
- Dark/light mode support based on system preferences

### Analytics Dashboard (Admin)
- **Comprehensive visitor statistics**
  - Today, this week, this month, and all-time counts
  - Daily visitor trend chart (last 30 days)
  - Device distribution (Desktop/Mobile/Tablet)
  - Top browsers and operating systems
- **Detailed visitor table**
  - IP address, location, browser, OS, device, visit date
  - Pagination support
  - Search/filter functionality
- **Password-protected access**
- **Modern, professional UI** with animations
- **Fully responsive** for all screen sizes

## Installation

### 1. Database Setup

Create a MySQL database and update the connection details in `visitor-counter.php`:

```php
define('VC_DB_HOST', 'localhost');
define('VC_DB_NAME', 'your_database');
define('VC_DB_USER', 'your_username');
define('VC_DB_PASS', 'your_password');
```

The tables will be created automatically on first run.

### 2. Dashboard Password

Set your dashboard password in `dashboard.php`:

```php
define('DASHBOARD_PASSWORD', 'your_secure_password_here');
```

**Important:** Use a strong password for production!

### 3. File Upload

Upload all files to your web server:
- `visitor-counter.php`
- `visitor.js`
- `visitor.css`
- `dashboard.php`
- `dashboard.js`
- `dashboard.css`

### 4. Integration

Add the visitor counter to any PHP page:

```php
<?php include 'visitor-counter.php'; ?>
```

That's it! The counter will automatically:
- Track the visitor silently
- Display the counter section
- Load necessary CSS and JavaScript

## Usage

### Public Counter

The counter appears as a sleek, compact section showing:
- Total visitor count (left side)
- Country distribution pie chart (center)
- Top 5 countries list (right side)

### Admin Dashboard

Access the dashboard at: `https://yourdomain.com/dashboard.php`

1. Enter the password you set
2. View comprehensive analytics:
   - Overview statistics
   - Visitor trends
   - Technology breakdown
   - Detailed visitor logs

## API Endpoints

The system provides several API endpoints:

### Public Endpoints
- `visitor-counter.php?vc_action=get_stats` - Get visitor statistics
- `visitor-counter.php?vc_action=track` - Track a visitor

### Dashboard Endpoints (Authenticated)
- `dashboard.php?vcd_action=get_visitors&page=1` - Get visitor list
- `dashboard.php?vcd_action=get_analytics` - Get analytics data

## Geolocation

The system uses the free ipapi.co API for geolocation. Features:
- Automatic IP detection
- Country, city, region detection
- Latitude/longitude coordinates
- Country flags in the UI
- Handles local IPs gracefully

**Note:** Free tier has rate limits. For high-traffic sites, consider:
- Implementing caching
- Using a paid geolocation service
- Setting up your own GeoIP database

## Browser & Device Detection

Automatically detects:
- **Browsers:** Chrome, Firefox, Safari, Edge, Opera, IE
- **Operating Systems:** Windows, macOS, Linux, Android, iOS
- **Devices:** Desktop, Mobile, Tablet

## Security Considerations

### Important Notes
1. **GDPR Compliance:** This system tracks users without consent. Ensure compliance with:
   - GDPR (EU)
   - CCPA (California)
   - Other privacy laws in your jurisdiction
   
2. **Recommendations:**
   - Add a privacy policy
   - Implement cookie consent if required
   - Anonymize IP addresses if needed
   - Set data retention policies
   - Provide opt-out mechanisms

3. **Session Tracking:**
   - Only tracks unique sessions
   - Same visitor within 1 hour = 1 count
   - Uses session IDs to prevent duplicates

4. **Dashboard Security:**
   - Implement stronger authentication for production
   - Use HTTPS for all connections
   - Consider IP whitelisting
   - Add rate limiting
   - Use environment variables for credentials

## Customization

### Colors & Theme

Edit CSS variables in `visitor.css` and `dashboard.css`:

```css
:root {
    --vc-primary: #0ea5e9;
    --vc-secondary: #8b5cf6;
    --vc-bg-main: #0f172a;
    /* ... more variables ... */
}
```

### Fonts

The system uses:
- **Visitor Counter:** Outfit + IBM Plex Mono
- **Dashboard:** Manrope + JetBrains Mono

Change fonts in the CSS `@import` statement.

### Refresh Intervals

- **Visitor Counter:** 30 seconds (visitor.js, line 22)
- **Dashboard:** 60 seconds (dashboard.js, line 12)

### Chart Colors

Customize chart colors in:
- `visitor.js` - lines 95-112 (pie chart)
- `dashboard.js` - lines 179-243 (line & doughnut charts)

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Opera 76+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

### Optimizations
- Minimal HTTP requests
- Chart.js loaded from CDN
- CSS animations (hardware-accelerated)
- Efficient database queries with indexes
- No external dependencies except Chart.js

### Database Indexes
The system automatically creates indexes on:
- `vc_ip_address`
- `vc_visit_date`

For high-traffic sites, consider:
- Adding more indexes
- Archiving old data
- Using read replicas
- Implementing caching (Redis/Memcached)

## Troubleshooting

### Counter not displaying
- Check database connection
- Verify file permissions
- Check browser console for errors
- Ensure Chart.js is loading

### Geolocation not working
- Check ipapi.co API status
- Verify internet connectivity
- Check for rate limits
- Review PHP error logs

### Dashboard not accessible
- Verify password in dashboard.php
- Clear browser cache/cookies
- Check session configuration
- Review PHP error logs

### Charts not rendering
- Verify Chart.js CDN is accessible
- Check browser console for errors
- Ensure canvas elements exist
- Check for JavaScript conflicts

## File Structure

```
visitor-tracking-system/
├── visitor-counter.php    # Main counter & tracking logic
├── visitor.css           # Counter styles
├── visitor.js            # Counter JavaScript
├── dashboard.php         # Admin dashboard
├── dashboard.css         # Dashboard styles
├── dashboard.js          # Dashboard JavaScript
└── README.md            # This file
```

## Technical Details

### Database Schema

**Table: vc_visitors**
- vc_id (INT, PRIMARY KEY, AUTO_INCREMENT)
- vc_ip_address (VARCHAR 45)
- vc_country (VARCHAR 100)
- vc_country_code (VARCHAR 10)
- vc_city (VARCHAR 100)
- vc_region (VARCHAR 100)
- vc_latitude (DECIMAL 10,8)
- vc_longitude (DECIMAL 11,8)
- vc_user_agent (TEXT)
- vc_browser (VARCHAR 50)
- vc_os (VARCHAR 50)
- vc_device (VARCHAR 50)
- vc_referrer (TEXT)
- vc_visit_date (DATETIME)
- vc_session_id (VARCHAR 64)

### Variable Naming Convention

All variables use unique prefixes to avoid conflicts:
- **Public Counter:** `vc_` prefix (visitor counter)
- **Dashboard:** `vcd_` prefix (visitor counter dashboard)

## Credits

- **Fonts:** Google Fonts (Outfit, IBM Plex Mono, Manrope, JetBrains Mono)
- **Charts:** Chart.js v4.4.0
- **Geolocation:** ipapi.co
- **Design:** Custom implementation following modern UI/UX principles

## License

This is a custom implementation. Please ensure you have appropriate licenses for:
- Google Fonts usage
- Chart.js library
- ipapi.co API usage

## Support

For issues, feature requests, or questions:
1. Check the troubleshooting section
2. Review PHP error logs
3. Check browser console for JavaScript errors
4. Verify database connectivity

## Version History

- **v1.0.0** (2024) - Initial release
  - Visitor counter with country distribution
  - Admin dashboard with analytics
  - Responsive design
  - Dark/light mode support

## Future Enhancements

Potential improvements:
- Real-time updates via WebSockets
- Export functionality (CSV, PDF)
- Custom date range selection
- Email reports
- Multiple user accounts
- API key authentication
- More detailed analytics
- Heatmap visualizations
- Visitor journey tracking
- A/B testing integration

---

**Note:** This system tracks visitors without explicit consent. Always comply with applicable privacy laws and regulations in your jurisdiction.
