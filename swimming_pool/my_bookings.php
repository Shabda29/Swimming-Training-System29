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
    <title>My Bookings - Shabda ko pool</title>
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
             <a href="index.php" class="nav-link active">Home</a>
           <a href="About.php" class="nav-link">About us</a>
            <a href="book.php" class="nav-link ">Booking Session</a>
           <a href="my_bookings.php" class="nav-link active">My Bookings</a>
        
             <a href="Contact.php" class="nav-link">Contact_us</a>
            
            <?php if (isAdmin()): ?>
                <a href="admin.php" class="nav-link">Admin Panel</a>
            <?php endif; ?>
        </div>
    </nav>
    
    <main class="container main-content">
        <h2>My Bookings</h2>
        
        <?php if ($bookings->num_rows === 0): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <p>No bookings yet. Book your first session!</p>
            </div>
        <?php else: ?>
            <div class="bookings-list bookings-grid">
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <div class="booking-card">
                        <div class="booking-info">
                            <h3><?php echo htmlspecialchars($booking['coach_name']); ?></h3>
                            <p class="specialty"><?php echo htmlspecialchars($booking['specialty']); ?></p>
                            <div class="booking-details">
                                <p>📅 <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                                <p>🕐 <?php echo htmlspecialchars($booking['time_slot']); ?></p>
                            </div>
                        </div>
                        <div class="booking-status">
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>