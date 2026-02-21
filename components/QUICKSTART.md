# QUICK START GUIDE - Sound Vision Website

## 🎯 Step-by-Step Deployment

### Step 1: Prepare Your Server
- Ensure PHP 7.4+ is installed
- Apache/Nginx web server running
- FTP/SFTP access to your hosting

### Step 2: Upload Files
```
Upload all files maintaining this structure:
/
├── index.php
├── .htaccess
├── components/
│   └── (all .php files)
└── assets/
    ├── css/
    ├── js/
    └── images/
```

### Step 3: Add Your Images
Create the following images and place them in `assets/images/`:

**Required Images:**
1. **SDG Logos** (download from UN website or create):
   - sdg-3.png (Good Health and Well-being)
   - sdg-4.png (Quality Education)
   - sdg-11.png (Sustainable Cities)
   
2. **App Mockup**:
   - app-mockup.png (phone screenshot showing your app)
   - qr-code.png (QR code linking to your app)

3. **About Section**:
   - about-team.jpg (team photo or office image)

4. **Awards**:
   - award-1.png through award-6.png (award logos/badges)

5. **Testimonials**:
   - user-1.jpg through user-4.jpg (user avatars)

### Step 4: Customize Content

**1. Update Company Information:**
Edit `components/footer.php`:
```php
// Change contact details
<span>123 Innovation Street<br>Tech City, TC 12345</span>
<span>+1 (555) 123-4567</span>
<span>info@soundvision.com</span>
```

**2. Update Hero Messages:**
Edit `components/hero.php`:
```php
// Change slide titles and content
<h1 class="hero-title">Your Custom Message</h1>
```

**3. Update SDG Content:**
Edit `components/sdg.php`:
```php
// Customize descriptions for each SDG
```

**4. Update Features:**
Edit `components/features.php`:
```php
// Modify feature titles and descriptions
```

**5. Update Play Store Link:**
Edit `components/app-download.php`:
```php
<a href="YOUR_PLAY_STORE_URL" target="_blank">
```

**6. Update Social Media Links:**
Edit `components/footer.php`:
```php
<a href="YOUR_FACEBOOK_URL">
<a href="YOUR_TWITTER_URL">
// etc.
```

### Step 5: Test Website
1. Open browser: `http://yourdomain.com`
2. Test all sections:
   - ✓ Navigation works
   - ✓ Hero slider auto-plays
   - ✓ All images load
   - ✓ Download buttons work
   - ✓ Testimonials rotate
   - ✓ Mobile menu functions
   - ✓ Visitor counter increments

### Step 6: Mobile Testing
Test on different devices:
- iPhone (Safari)
- Android (Chrome)
- iPad (Safari)
- Various screen sizes

### Step 7: SEO Setup

**Add to index.php head section:**
```php
<meta name="description" content="Sound Vision - Your description here">
<meta name="keywords" content="health, education, technology, sustainable">
<meta name="author" content="Sound Vision">

<!-- Open Graph for social media -->
<meta property="og:title" content="Sound Vision">
<meta property="og:description" content="Your description">
<meta property="og:image" content="https://yourdomain.com/assets/images/og-image.jpg">
<meta property="og:url" content="https://yourdomain.com">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Sound Vision">
<meta name="twitter:description" content="Your description">
<meta name="twitter:image" content="https://yourdomain.com/assets/images/twitter-image.jpg">
```

### Step 8: Analytics Setup

**Add Google Analytics:**
Before `</head>` in index.php:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### Step 9: SSL Certificate
1. Obtain SSL certificate (Let's Encrypt is free)
2. Install on your server
3. Uncomment HTTPS redirect in .htaccess

### Step 10: Performance Check
1. Test speed: https://pagespeed.web.dev/
2. Compress images if needed
3. Enable caching (already in .htaccess)

## 🎨 Customization Quick Reference

### Change Primary Color
File: `assets/css/style.css`
```css
:root {
    --primary-color: #YOUR_COLOR;
}
```

### Add New Section
1. Create `components/your-section.php`
2. Add to `index.php`:
   ```php
   <?php include 'components/your-section.php'; ?>
   ```
3. Add CSS to `style.css`

### Change Fonts
File: `index.php` (head section)
```html
<link href="https://fonts.googleapis.com/css2?family=YOUR_FONT:wght@300;400;700&display=swap" rel="stylesheet">
```

File: `assets/css/style.css`
```css
body {
    font-family: 'YOUR_FONT', sans-serif;
}
```

## 🔧 Common Issues & Solutions

**Issue: Visitor counter not incrementing**
Solution: Check file permissions
```bash
chmod 666 visitor_count.txt
```

**Issue: Images not showing**
Solution: 
- Check file paths are correct
- Verify image files exist
- Check file permissions

**Issue: Mobile menu not working**
Solution:
- Clear browser cache
- Check JavaScript console for errors
- Verify main.js is loaded

**Issue: Slow loading**
Solution:
- Compress images (use TinyPNG or similar)
- Enable caching (check .htaccess)
- Consider CDN for assets

## 📱 Contact for Support

Email: info@soundvision.com
Phone: +1 (555) 123-4567

## ✅ Pre-Launch Checklist

- [ ] All images uploaded and loading
- [ ] Company information updated
- [ ] Play Store link working
- [ ] Social media links updated
- [ ] Contact information correct
- [ ] Mobile responsive tested
- [ ] All animations working
- [ ] Forms tested (if any)
- [ ] SSL certificate installed
- [ ] Analytics tracking active
- [ ] SEO meta tags added
- [ ] 404 error page created
- [ ] Backup created
- [ ] Performance optimized

## 🚀 You're Ready to Launch!

Once you've completed all steps, your Sound Vision website is ready to go live!

Remember to:
- Monitor analytics
- Update content regularly
- Backup regularly
- Keep PHP and plugins updated
- Test after any changes

Good luck with your launch! 🎉
