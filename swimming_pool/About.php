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
    <title>My Bookings - habda ko pool</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <span class="logo">🏊‍♂</span>
                    <h1>Shabda ko pool</h1>
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
            <a href="index.php" class="nav-link ">Home</a>
           <a href="About.php" class="nav-link active">About us</a>
            <a href="book.php" class="nav-link ">Booking Session</a>
            <a href="my_bookings.php" class="nav-link">My Bookings</a>
             <a href="Contact.php" class="nav-link">Contact_us</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php" class="nav-link">Admin Panel</a>
            <?php endif; ?>
        </div>
    </nav>
    
        
<section class="features" id="features">
        <div class="features-container">
            <h2 class="section-title">Why Choose Swimming Pool?</h2>
            <p class="section-subtitle">Experience the best swimming training with our premium features</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">👨‍🏫</div>
                    <h3>Expert Coaches</h3>
                    <p>Learn from certified professional coaches with years of experience in competitive swimming.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📅</div>
                    <h3>Flexible Booking</h3>
                    <p>Book your sessions anytime, anywhere with our easy-to-use online booking system.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🏊‍♂</div>
                    <h3>World-Class Pool</h3>
                    <p>Train in our Olympic-standard swimming pool with the latest facilities and equipment.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⏰</div>
                    <h3>Convenient Hours</h3>
                    <p>Multiple time slots available from early morning to evening to fit your schedule.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>All Skill Levels</h3>
                    <p>From beginners to advanced swimmers, we have programs tailored for everyone.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">✅</div>
                    <h3>Instant Confirmation</h3>
                    <p>Get quick approval for your bookings and start your swimming journey immediately.</p>
                </div>
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
            <p>&copy; 2025 Swimming Pool. All rights reserved.</p>
        </div>
    </footer>

      </main>
</body>
</html>