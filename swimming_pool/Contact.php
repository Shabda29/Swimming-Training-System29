<?php
require_once 'config.php';
requireLogin();

$bookings_query = "SELECT b.*, c.name as coach_name, c.specialty 
                   FROM bookings b 
                   JOIN coaches c ON b.coach_id = c.id 
                   WHERE b.user_id = ? 
                   ORDER BY b.booking_date DESC, b.time_slot";
$stmt = $conn->prepare($bookings_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - shabda's pool</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <span class="logo">🏊‍♂</span>
                    <h1>Shabda's pool</h1>
                </div>
                <div class="user-section">
                    <div class="user-info">
                        <p>Welcome back,</p>
                        <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                        <span class="badge"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                    </div>
                    <a href="logout.php" class="btn btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </header>
    
    <nav class="nav">
        <div class="container">
            <a href="index.php" class="nav-link">Home</a>
           <a href="About.php" class="nav-link">About us</a>
            <a href="book.php" class="nav-link ">Booking Session</a>
            <a href="my_bookings.php" class="nav-link">My Bookings</a>
             <a href="Contact.php" class="nav-link active">Contact_us</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php" class="nav-link">Admin Panel</a>
            <?php endif; ?>
        </div>
    </nav>
    
  <main class="contact-container">
<section class="contact-card">
<h2>Contact Us</h2>
<p class="description">Reach out to us — we're always happy to help!</p>


<div class="contact-info">
<div class="info-item">
<i class="fas fa-map-marker-alt"></i>
<div>
<strong>Address</strong>
<p> kirtipur, kathmandu</p>
</div>
</div>


<div class="info-item">
<i class="fas fa-phone"></i>
<div>
<strong>Phone</strong>
<a href="tel:+9779741831161">+977 9741831161</a>
</div>
</div>


<div class="info-item">
<i class="fas fa-envelope"></i>
<div>
<strong>Email</strong>
<a href="mailto:admin@pool.com">admin@pool.com</a>
</div>
</div>


<div>
<strong class="social-title">Follow us</strong>
<div class="social-links">
<a href="https://www.facebook.com/" title="Facebook"><i class="fab fa-facebook-f"></i></a>
<a href="https://www.twitter.com/" title="Twitter"><i class="fab fa-twitter"></i></a>
<a href="https://www.instagram.com/" title="Instagram"><i class="fab fa-instagram"></i></a>
<a href="https://www.linkedin.com/" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
</div>
</div>


</div>
<footer class="footer-note">We typically respond within 24 hours.</footer>
</section>
</main>  




<!-- footer -->
 <footer>
        <div class="footer-container">
            <div class="footer-about">
                <h3>🏊‍♂ Swimming Pool</h3>
                <p>Your premier destination for professional swimming training. We're committed to helping you achieve your swimming goals with expert guidance.</p>
            </div>
            
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#coaches">Coaches</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-links">
                <h4>Resources</h4>
                <ul>
                    <li><a href="#">Schedule</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
            
            <div class="footer-links">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cancellation Policy</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Swimming Pool. All rights reserved. </p>
        </div>
    </footer>

      </main>
</body>
</html> 