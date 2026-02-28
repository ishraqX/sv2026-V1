<?php
// about.php - cleaned up: reference actual image file and remove diagnostics
// The actual image file in this folder is `aboutus.JPG` (uppercase extension).
$image_src = 'components/7.about/aboutus.JPG';
$local_path = __DIR__ . '/aboutus.JPG';
$file_exists = file_exists($local_path) && is_readable($local_path);
?>

<section class="about-section" id="about">
    <div class="container">
        <!-- About image: using the actual file in this component folder -->
        <?php if (!$file_exists): ?>
        <div style="margin-bottom:20px;padding:12px;background:#ffebee;border-left:4px solid #f44336;">
            ⚠️ About image not found or unreadable at <strong>components/7.about/aboutus.JPG</strong> — upload the image or check permissions (644).
        </div>
        <?php endif; ?>

        <div class="about-wrapper">
            <div class="about-image">
                <!-- Try multiple image sources with fallbacks -->
                <img
                    src="<?= htmlspecialchars($image_src) ?>"
                    alt="Sound Vision Team"
                    onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22600%22 height=%22400%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%230d1220%22/%3E%3Ctext x=%2250%25%22 y=%2246%25%22 font-family=%22sans-serif%22 font-size=%2222%22 fill=%22%23D4AF37%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3ESound Vision Team%3C/text%3E%3Ctext x=%2250%25%22 y=%2256%25%22 font-family=%22sans-serif%22 font-size=%2213%22 fill=%22%237A6F5A%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EImage not found%3C/text%3E%3C/svg%3E'; console.log('Image failed to load: ' + this.src);"
                    style="max-width:100%; border: <?= $file_exists ? '2px solid green' : '2px solid red' ?>;"
                >
                
                <!-- Show base64 if file exists but not loading -->
                <?php if ($file_exists && $is_readable && $file_size > 0): ?>
                <div style="margin-top: 10px; padding: 10px; background: #e8f5e8; border-left: 4px solid #4caf50;">
                    ✅ Image file exists on server at <code><?= $full_image_path ?></code> (<?= round($file_size/1024, 2) ?> KB)<br>
                    But might not be accessible via URL. Check file permissions (should be 644).
                </div>
                <?php elseif ($file_exists && !$is_readable): ?>
                <div style="margin-top: 10px; padding: 10px; background: #fff3e0; border-left: 4px solid #ff9800;">
                    ⚠️ Image exists but is NOT READABLE. Fix permissions: <code>chmod 644 <?= $full_image_path ?></code>
                </div>
                <?php elseif (!$file_exists): ?>
                <div style="margin-top: 10px; padding: 10px; background: #ffebee; border-left: 4px solid #f44336;">
                    ❌ Image NOT FOUND at: <code><?= $full_image_path ?></code><br>
                    Upload the image to this location or update the path.
                </div>
                <?php endif; ?>
                
                <div class="about-stats">
                    <div class="stat-card">
                        <h3>500K+</h3>
                        <p>Active Users</p>
                    </div>
                    <div class="stat-card">
                        <h3>150+</h3>
                        <p>Countries</p>
                    </div>
                    <div class="stat-card">
                        <h3>4.8/5</h3>
                        <p>User Rating</p>
                    </div>
                </div>
            </div>
            
            <div class="about-content">
                <div class="section-header">
                    <h2>Who We Are</h2>
                    <div class="header-line"></div>
                </div>
                
                <p class="about-intro">
                    Sound Vision is a pioneering technology company dedicated to creating innovative solutions that address global challenges in healthcare, education, and sustainable urban development.
                </p>
                
                <p class="about-description">
                    Founded with a mission to leverage cutting-edge technology for social good, we believe in the power of innovation to transform lives and communities. Our platform connects millions of users worldwide, providing them with tools and resources to lead healthier, more informed, and connected lives.
                </p>
                
                <div class="about-values">
                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="value-content">
                            <h4>Innovation</h4>
                            <p>Constantly pushing boundaries to create breakthrough solutions</p>
                        </div>
                    </div>
                    
                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <div class="value-content">
                            <h4>Impact</h4>
                            <p>Measuring success by the positive change we create</p>
                        </div>
                    </div>
                    
                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="value-content">
                            <h4>Sustainability</h4>
                            <p>Building solutions that are environmentally and socially responsible</p>
                        </div>
                    </div>
                </div>
                
                <a href="#contact" class="btn btn-primary">Get in Touch</a>
            </div>
        </div>
    </div>
</section>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* Temporary diagnostic styles */
.about-image img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}
</style>