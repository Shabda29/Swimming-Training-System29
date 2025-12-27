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
    <title>My Bookings - Shabda's pool</title>
    <link rel="stylesheet" href="style.css">
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
            <a href="index.php" class="nav-link active">Home</a>
           <a href="About.php" class="nav-link">About us</a>
            <a href="book.php" class="nav-link ">Booking Session</a>
            <a href="my_bookings.php" class="nav-link">My Bookings</a>
             <a href="Contact.php" class="nav-link">Contact_us</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php" class="nav-link">Admin Panel</a>
            <?php endif; ?>
        </div>
    </nav>
    
        
<section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Dive Into Excellence</h1>
                <p>Book your perfect swimming session with expert coaches. Professional training, flexible schedules, world-class facilities.</p>
                <div class="hero-buttons">
                    <a href="book.php" class="btn-primary">Book Now</a>
                    <a href="About.php" class="btn-secondary">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
                <div class="pool-illustration">🏊‍♀</div>
            </div>
        </div>
    </section>



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