<?php
// Start session for visitor counter
session_start();

// Simple visitor counter (you'll want to use a database in production)
$counter_file = 'visitor_count.txt';
if (!file_exists($counter_file)) {
    file_put_contents($counter_file, '0');
}
$current_count = (int)file_get_contents($counter_file);
$current_count++;
file_put_contents($counter_file, $current_count);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sound Vision - Transforming Lives Through Innovation</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Modular Stylesheets -->
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/navigation.css">
    <link rel="stylesheet" href="assets/css/hero.css">
    <link rel="stylesheet" href="assets/css/sdg.css">
    <link rel="stylesheet" href="assets/css/features.css">
    <link rel="stylesheet" href="assets/css/app-download.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/awards.css">
    <link rel="stylesheet" href="assets/css/testimonials.css">
    <link rel="stylesheet" href="assets/css/visitor-counter.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'components/navigation.php'; ?>
    
    <!-- Hero Banner -->
    <?php include 'components/hero.php'; ?>
    
    <!-- SDG Section -->
    <?php include 'components/sdg.php'; ?>
    
    <!-- Solutions/Features -->
    <?php include 'components/features.php'; ?>
    
    <!-- App Download -->
    <?php include 'components/app-download.php'; ?>
    
    <!-- Who We Are -->
    <?php include 'components/about.php'; ?>
    
    <!-- Awards -->
    <?php include 'components/awards.php'; ?>
    
    <!-- Testimonials -->
    <?php include 'components/testimonials.php'; ?>
    
    <!-- Visitor Counter -->
    <?php include 'components/visitor-counter.php'; ?>
    
    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>
