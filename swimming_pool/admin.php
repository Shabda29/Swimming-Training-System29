<?php
require_once 'config.php';
requireLogin();

if (!isAdmin()) {
   require_once 'config.php';
requireLogin();

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

    exit();
}

// Handle booking actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $booking_id = $_POST['booking_id'] ?? 0;
    $action = $_POST['action'];
    
    if ($action === 'approve' || $action === 'reject') {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $booking_id);
        $stmt->execute();
        $stmt->close();
        header('Location: admin.php?updated=1');
        exit();
    }
}

// Get all bookings
$bookings_query = "SELECT b.*, u.name as user_name, c.name as coach_name 
                   FROM bookings b 
                   JOIN users u ON b.user_id = u.id
                   JOIN coaches c ON b.coach_id = c.id 
                   ORDER BY b.created_at DESC";
$bookings = $conn->query($bookings_query);

// Count pending bookings
$pending_count = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Shabda ko Pool</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <span class="logo">🏊‍♂</span>
                    <h1>Shabda ko Pool</h1>
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

        <?php if (isAdmin()): ?>
            <!-- Show only Admin Panel for admin -->
            <a href="admin.php" class="nav-link active">
                Admin Panel
                <?php if ($pending_count > 0): ?>
                    <span class="notification-badge"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="admin_users.php" class="nav-link active">Manage users</a>
        <?php else: ?>
            <!-- Show normal user nav -->
            <a href="index.php" class="nav-link">Book Session</a>
            <a href="my_bookings.php" class="nav-link">My Bookings</a>
        <?php endif; ?>

    </div>
</nav>

    
    <main class="container main-content">
        <h2>Admin Panel - Booking Requests</h2>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Booking status updated successfully!</div>
        <?php endif; ?>
        
        <?php if ($bookings->num_rows === 0): ?>
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <p>No booking requests yet.</p>
            </div>
        <?php else: ?>
            <div class="bookings-list bookings-grid">
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <div class="booking-card admin-card">
                        <div class="booking-info">
                            <div class="user-name">
                                <span class="user-icon">👤</span>
                                <h3><?php echo htmlspecialchars($booking['user_name']); ?></h3>
                            </div>
                            <div class="booking-details">
                                <p><strong>Coach:</strong> <?php echo htmlspecialchars($booking['coach_name']); ?></p>
                                <p>📅 <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                                <p>🕐 <?php echo htmlspecialchars($booking['time_slot']); ?></p>
                            </div>
                        </div>
                        <div class="booking-actions">
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">✓ Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">✗ Reject</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>