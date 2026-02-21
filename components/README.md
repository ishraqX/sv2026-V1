# Sound Vision Website

A premium, modular PHP website for Sound Vision startup featuring modern design, smooth animations, and mobile responsiveness.

## 🚀 Features

- **Modular Architecture**: Each section is a separate PHP file for easy maintenance
- **Hero Banner**: Auto-sliding banner with 3 slides and manual controls
- **SDG Integration**: Showcase commitment to UN SDGs 3, 4, and 11
- **Features Section**: Highlight key solutions and offerings
- **App Download**: Play Store integration with product mockup
- **About Section**: Company information with statistics
- **Awards Carousel**: Auto-sliding awards with seamless loop
- **Testimonials**: Rotating customer testimonials
- **Visitor Counter**: Real-time today's visitor tracking
- **Responsive Design**: Mobile-first, works on all devices
- **Modern UI/UX**: Premium design with smooth animations

## 📁 File Structure

```
sound-vision/
│
├── index.php                 # Main file that includes all components
├── visitor_count.txt         # Auto-generated visitor counter file
│
├── components/               # All modular PHP components
│   ├── navigation.php       # Top navigation bar
│   ├── hero.php            # Hero banner with slides
│   ├── sdg.php             # SDG goals section
│   ├── features.php        # Solutions/features grid
│   ├── app-download.php    # App download with Play Store
│   ├── about.php           # Who we are section
│   ├── awards.php          # Awards carousel
│   ├── testimonials.php    # Testimonials slider
│   ├── visitor-counter.php # Today's visitor counter
│   └── footer.php          # Footer with copyright
│
├── assets/
│   ├── css/
│   │   └── style.css       # Complete stylesheet
│   ├── js/
│   │   └── main.js         # All JavaScript functionality
│   └── images/             # Image directory (create and add images)
│       ├── sdg-3.png
│       ├── sdg-4.png
│       ├── sdg-11.png
│       ├── app-mockup.png
│       ├── qr-code.png
│       ├── about-team.jpg
│       ├── award-1.png to award-6.png
│       └── user-1.jpg to user-4.jpg
│
└── README.md               # This file
```

## 🛠️ Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Quick Start

1. **Upload Files**
   ```bash
   # Upload all files to your web server
   # Ensure directory structure is maintained
   ```

2. **Set Permissions**
   ```bash
   # Make sure the root directory is writable for visitor counter
   chmod 755 /path/to/website
   chmod 666 visitor_count.txt  # Will be created automatically
   ```

3. **Add Images**
   - Create `assets/images/` directory
   - Add required images:
     - SDG logos (sdg-3.png, sdg-4.png, sdg-11.png)
     - App mockup (app-mockup.png)
     - QR code (qr-code.png)
     - Team photo (about-team.jpg)
     - Award images (award-1.png through award-6.png)
     - User avatars (user-1.jpg through user-4.jpg)

4. **Access Website**
   ```
   http://yourdomain.com/index.php
   # or
   http://localhost/sound-vision/index.php
   ```

## 🎨 Customization Guide

### Changing Colors
Edit `assets/css/style.css` - update CSS variables:
```css
:root {
    --primary-color: #2563eb;      /* Main brand color */
    --secondary-color: #7c3aed;    /* Secondary accent */
    --accent-color: #f59e0b;       /* Highlight color */
    --success-color: #10b981;      /* Success indicators */
}
```

### Modifying Content

**Hero Slides** (`components/hero.php`):
- Edit slide titles, subtitles, and button links
- Add/remove slides (update indicators accordingly)

**SDG Section** (`components/sdg.php`):
- Update SDG descriptions and targets
- Change SDG numbers if needed

**Features** (`components/features.php`):
- Modify feature titles and descriptions
- Change icons (Font Awesome classes)

**App Download** (`components/app-download.php`):
- Update Play Store link
- Modify download statistics
- Change app mockup image

**About Section** (`components/about.php`):
- Edit company description
- Update statistics (users, countries, rating)
- Modify values and mission

**Awards** (`components/awards.php`):
- Add/remove award cards
- Update award titles and descriptions

**Testimonials** (`components/testimonials.php`):
- Add/edit customer testimonials
- Change ratings and user info

**Footer** (`components/footer.php`):
- Update contact information
- Modify social media links
- Change footer links and resources

### Adding New Sections

1. Create new PHP file in `components/` directory
2. Add content with proper HTML structure
3. Include in `index.php`:
   ```php
   <?php include 'components/your-new-section.php'; ?>
   ```
4. Add corresponding CSS in `style.css`
5. Add JavaScript if needed in `main.js`

## 🔧 Advanced Features

### Visitor Counter
- Automatically tracks today's visitors
- Stored in `visitor_count.txt`
- For production, consider using a database:

```php
// Example MySQL integration
$conn = new mysqli("localhost", "username", "password", "database");
$result = $conn->query("UPDATE visitors SET count = count + 1 WHERE date = CURDATE()");
```

### Database Integration
Replace file-based counter with database:

```sql
CREATE TABLE visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE UNIQUE,
    count INT DEFAULT 0
);
```

### Form Submission
Add contact form handling in `components/footer.php` or create new component:

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    // Process form data
    // Send email, save to database, etc.
}
?>
```

## 📱 Mobile Responsiveness

The website is fully responsive with breakpoints:
- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: < 768px
- Small mobile: < 480px

## 🎯 SEO Optimization

1. Add meta tags in `index.php`:
```php
<meta name="description" content="Sound Vision - Your description">
<meta name="keywords" content="health, education, technology">
<meta property="og:title" content="Sound Vision">
<meta property="og:image" content="assets/images/og-image.jpg">
```

2. Create `sitemap.xml`
3. Add Google Analytics tracking code
4. Implement schema markup for rich snippets

## 🚀 Performance Optimization

1. **Image Optimization**
   - Compress images before upload
   - Use WebP format when possible
   - Implement lazy loading (already included)

2. **CSS/JS Minification**
   ```bash
   # Use tools like:
   npm install -g clean-css-cli uglify-js
   cleancss -o style.min.css style.css
   uglifyjs main.js -o main.min.js
   ```

3. **Caching**
   Add to `.htaccess`:
   ```apache
   <IfModule mod_expires.c>
       ExpiresActive On
       ExpiresByType image/jpg "access plus 1 year"
       ExpiresByType image/jpeg "access plus 1 year"
       ExpiresByType image/png "access plus 1 year"
       ExpiresByType text/css "access plus 1 month"
       ExpiresByType application/javascript "access plus 1 month"
   </IfModule>
   ```

## 🔒 Security Considerations

1. **Input Validation**
   - Always sanitize user inputs
   - Use prepared statements for database queries

2. **File Permissions**
   ```bash
   # Set proper permissions
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   ```

3. **HTTPS**
   - Always use SSL certificate
   - Redirect HTTP to HTTPS

## 🐛 Troubleshooting

**Visitor counter not working:**
- Check file permissions on root directory
- Ensure PHP can create/write files

**Images not loading:**
- Verify image paths are correct
- Check file names match (case-sensitive)

**Animations not working:**
- Clear browser cache
- Check JavaScript console for errors
- Verify main.js is loaded properly

**Mobile menu not working:**
- Ensure JavaScript is enabled
- Check for console errors

## 📞 Support

For questions or issues:
- Email: info@soundvision.com
- Website: https://soundvision.com

## 📄 License

Copyright © 2026 Sound Vision. All Rights Reserved.

## 🙏 Credits

- Icons: Font Awesome
- Fonts: Google Fonts (Poppins)
- Design: Sound Vision Team

---

**Made with ❤️ by Sound Vision Team**
