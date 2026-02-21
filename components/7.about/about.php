<!-- about.php -->
<section class="about-section" id="about">
    <div class="container">
        <div class="about-wrapper">
            <div class="about-image">
                <!-- FIXED: Added proper path and fallback -->
                <?php
                /*
                 * ROBUST IMAGE PATH — works regardless of include depth or OS.
                 *
                 * Strategy:
                 *   1. Get absolute server path of THIS file's directory (__DIR__)
                 *   2. Get absolute server path of the web root (DOCUMENT_ROOT)
                 *   3. Both are normalized with realpath() + forward slashes
                 *      so Windows backslashes and symlinks don't break the match.
                 *   4. Subtract root from dir → web-root-relative URL path.
                 *   5. Append the filename.
                 *
                 * Result example:
                 *   __DIR__       = /var/www/html/components/about
                 *   DOCUMENT_ROOT = /var/www/html
                 *   $about_img_path = /components/about/aboutus.jpg   ✓
                 */
                $_ab_dir  = rtrim(str_replace('\\', '/', realpath(__DIR__)), '/');
                $_ab_root = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '/');
                $about_img_path = str_replace($_ab_root, '', $_ab_dir) . '/aboutus.jpg';

                // Check if the file actually exists on disk (helps with debugging)
                $_ab_file_exists = file_exists(__DIR__ . '/aboutus.jpg');
                ?>
                <!-- DEBUG (remove after confirming image loads):
                     File exists on disk: <?= $_ab_file_exists ? 'YES ✓' : 'NO ✗ — aboutus.jpg not found in ' . __DIR__ ?>
                     Resolved src: <?= htmlspecialchars($about_img_path) ?>
                -->
                <img src="<?= htmlspecialchars($about_img_path) ?>"
                     alt="Sound Vision Team"
                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22600%22 height=%22400%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%230d1220%22/%3E%3Ctext x=%2250%25%22 y=%2246%25%22 font-family=%22sans-serif%22 font-size=%2222%22 fill=%22%23D4AF37%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3ESound Vision Team%3C/text%3E%3Ctext x=%2250%25%22 y=%2256%25%22 font-family=%22sans-serif%22 font-size=%2213%22 fill=%22%237A6F5A%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EImage not found%3C/text%3E%3C/svg%3E'">
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

<!-- Add Font Awesome if not already included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">