# CSS Customization Guide - Sound Vision Website

## 📁 Modular CSS Structure

Your website styles are now split into **12 separate CSS files** for easy customization!

```
assets/css/
├── base.css                # Reset, variables, common styles
├── navigation.css          # Navigation bar styles
├── hero.css               # Hero banner section
├── sdg.css                # SDG section
├── features.css           # Features section
├── app-download.css       # App download section
├── about.css              # About section
├── awards.css             # Awards carousel
├── testimonials.css       # Testimonials section
├── visitor-counter.css    # Visitor counter
├── footer.css             # Footer section
├── responsive.css         # Mobile responsive
└── style.css              # OLD file (can be deleted)
```

## 🎨 Quick Customization Guide

### 1. Change Brand Colors

**File:** `base.css`
**Lines:** 10-17

```css
:root {
    --primary-color: #2563eb;      /* Change main brand color */
    --secondary-color: #7c3aed;    /* Change secondary color */
    --accent-color: #f59e0b;       /* Change accent/highlight color */
    --success-color: #10b981;      /* Change success indicators */
    --text-dark: #1f2937;          /* Change dark text color */
    --text-light: #6b7280;         /* Change light text color */
    --bg-light: #f9fafb;           /* Change light background */
    --bg-white: #ffffff;           /* Change white background */
}
```

**Example - Change to Blue & Orange Theme:**
```css
:root {
    --primary-color: #0066cc;
    --secondary-color: #ff6600;
    --accent-color: #ffcc00;
}
```

### 2. Customize Navigation

**File:** `navigation.css`

**Change logo size:**
```css
.logo h1 {
    font-size: 2rem;  /* Change from 1.8rem */
}
```

**Change navigation background:**
```css
.navbar {
    background: rgba(0, 0, 0, 0.9);  /* Dark background */
    /* OR */
    background: white;  /* Solid white */
}
```

**Change menu link colors:**
```css
.nav-menu a {
    color: #ffffff;  /* White text */
}
```

### 3. Customize Hero Banner

**File:** `hero.css`

**Change slide gradients:**
```css
.hero-slide {
    background: linear-gradient(135deg, #YOUR_COLOR1, #YOUR_COLOR2);
}
```

**Change hero text size:**
```css
.hero-title {
    font-size: 4rem;  /* Larger title */
}

.hero-subtitle {
    font-size: 1.8rem;  /* Larger subtitle */
}
```

**Change slide transition speed:**
```css
.hero-slide {
    transition: opacity 2s ease-in-out;  /* Slower: 2s, Faster: 0.5s */
}
```

### 4. Customize SDG Cards

**File:** `sdg.css`

**Change card background:**
```css
.sdg-card {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
}
```

**Change hover effect:**
```css
.sdg-card:hover {
    transform: scale(1.05);  /* Scale up instead of lift */
}
```

**Change border color:**
```css
.sdg-card {
    border-top: 5px solid #YOUR_COLOR;
}
```

### 5. Customize Features Section

**File:** `features.css`

**Change icon background:**
```css
.feature-icon {
    background: #YOUR_SOLID_COLOR;
    /* OR keep gradient */
    background: linear-gradient(135deg, #COLOR1, #COLOR2);
}
```

**Change card layout:**
```css
.features-grid {
    grid-template-columns: repeat(3, 1fr);  /* 3 columns instead of auto-fit */
}
```

**Change hover animation:**
```css
.feature-card:hover {
    transform: scale(1.05);  /* Scale instead of lift */
}
```

### 6. Customize App Download Section

**File:** `app-download.css`

**Change background gradient:**
```css
.app-download {
    background: linear-gradient(135deg, #YOUR_COLOR1, #YOUR_COLOR2);
}
```

**Change button style:**
```css
.store-button {
    background: white;
    color: var(--primary-color);
    border: none;
}
```

**Hide QR code:**
```css
.qr-code {
    display: none;
}
```

### 7. Customize About Section

**File:** `about.css`

**Change statistics card colors:**
```css
.stat-card h3 {
    color: #YOUR_COLOR;
}
```

**Change value icon colors:**
```css
.value-icon {
    background: #YOUR_SOLID_COLOR;
}
```

### 8. Customize Awards Carousel

**File:** `awards.css`

**Change scroll speed:**
```css
.awards-slider {
    animation: slideAwards 20s linear infinite;  /* Faster: 20s, Slower: 40s */
}
```

**Change card width:**
```css
.award-card {
    flex: 0 0 250px;  /* Smaller cards */
}
```

### 9. Customize Testimonials

**File:** `testimonials.css`

**Change card background:**
```css
.testimonial-card {
    background: white;
    border: 2px solid var(--border-color);
}
```

**Change avatar border:**
```css
.testimonial-avatar {
    border: 4px solid #YOUR_COLOR;
}
```

**Change quote icon color:**
```css
.quote-icon {
    color: #YOUR_COLOR;
}
```

### 10. Customize Visitor Counter

**File:** `visitor-counter.css`

**Change background:**
```css
.visitor-counter {
    background: #YOUR_SOLID_COLOR;
    /* OR */
    background: linear-gradient(90deg, #COLOR1, #COLOR2);
}
```

**Change counter number size:**
```css
.counter-number {
    font-size: 4rem;  /* Larger */
}
```

### 11. Customize Footer

**File:** `footer.css`

**Change footer background:**
```css
.footer {
    background: #000000;  /* Pure black */
    /* OR */
    background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
}
```

**Change social icons hover color:**
```css
.footer-social a:hover {
    background: #YOUR_COLOR;
}
```

**Change link hover effect:**
```css
.footer-links a:hover {
    color: var(--primary-color);
    padding-left: 10px;
}
```

### 12. Mobile Responsive Adjustments

**File:** `responsive.css`

**Change mobile breakpoint:**
```css
@media (max-width: 992px) {  /* Change from 768px to 992px */
    /* Your mobile styles */
}
```

**Adjust mobile padding:**
```css
@media (max-width: 768px) {
    .container {
        padding: 0 20px;  /* More padding */
    }
}
```

## 🎯 Common Customization Scenarios

### Scenario 1: Make Everything Rounded
Add to `base.css`:
```css
.sdg-card,
.feature-card,
.award-card,
.testimonial-card,
.stat-card {
    border-radius: 30px !important;
}
```

### Scenario 2: Add Shadow to All Cards
Add to `base.css`:
```css
.sdg-card,
.feature-card,
.award-card,
.testimonial-card {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
}
```

### Scenario 3: Change All Fonts
Edit `index.php` head section:
```html
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
```

Then in `base.css`:
```css
body {
    font-family: 'Roboto', sans-serif;
}
```

### Scenario 4: Remove Animations
Add to `base.css`:
```css
* {
    animation: none !important;
    transition: none !important;
}
```

### Scenario 5: Dark Mode Theme
Create `dark-mode.css`:
```css
:root {
    --primary-color: #3b82f6;
    --text-dark: #ffffff;
    --text-light: #d1d5db;
    --bg-light: #1f2937;
    --bg-white: #111827;
}

body {
    background: #0f172a;
}

.navbar,
.features-section,
.about-section,
.testimonials-section {
    background: #1f2937;
}
```

## 📝 Tips for Customization

1. **Always Backup:** Keep a copy of original CSS files before making changes
2. **Test Changes:** Check each change in browser before moving to next
3. **Use Browser DevTools:** Press F12 to test styles in real-time
4. **Mobile First:** Always test mobile view after changes
5. **CSS Variables:** Change colors in `base.css` variables for site-wide updates
6. **!important Flag:** Use sparingly, only when absolutely needed
7. **Comment Your Changes:** Add comments to remember why you changed something

## 🔧 Troubleshooting

**Problem:** Changes not showing
- Clear browser cache (Ctrl + Shift + R)
- Check file path is correct
- Verify CSS file is linked in index.php

**Problem:** Mobile view broken
- Check `responsive.css` breakpoints
- Test with browser DevTools mobile view
- Verify media queries syntax

**Problem:** Colors not changing
- Make sure you're editing CSS variables in `base.css`
- Check if color is overridden elsewhere
- Use browser inspector to find which CSS is applied

## 🚀 Advanced Customization

### Add Custom Animations
Create `animations.css`:
```css
@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.custom-animation {
    animation: slideInLeft 1s ease-out;
}
```

### Add Gradient Text
```css
.gradient-text {
    background: linear-gradient(90deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
```

## 📋 Quick Reference

| File | What to Edit | Common Changes |
|------|-------------|----------------|
| base.css | Colors, fonts, global styles | Brand colors, typography |
| navigation.css | Header, menu | Logo size, nav color |
| hero.css | Banner slides | Gradients, text size |
| sdg.css | SDG cards | Card style, hover effects |
| features.css | Feature cards | Icons, layout |
| app-download.css | Download section | Buttons, background |
| about.css | About section | Stats, values |
| awards.css | Awards slider | Speed, card size |
| testimonials.css | Reviews | Card style, avatars |
| visitor-counter.css | Counter display | Background, numbers |
| footer.css | Footer | Background, links |
| responsive.css | Mobile views | Breakpoints, spacing |

## ✅ Best Practices

1. Edit only one CSS file at a time
2. Save frequently and test changes
3. Keep mobile responsive in mind
4. Use consistent spacing (rem units)
5. Maintain contrast for accessibility
6. Test in multiple browsers
7. Document your changes

Happy Customizing! 🎨
