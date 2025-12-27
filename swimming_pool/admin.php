<?php
session_start();

/* Prevent cached page access */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* Auth check */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
require_once 'config.php';
requireLogin();

if (!isAdmin()) {
    header("Location: index.php");
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
        header("Location: admin.php?updated=1");
        exit();
    }

    if ($action === 'update') {
        $booking_date = $_POST['booking_date'];
        $time_slot    = $_POST['time_slot'];
        $coach_id     = $_POST['coach_id'];
        $status       = $_POST['status'];

        $stmt = $conn->prepare("
            UPDATE bookings 
            SET booking_date = ?, time_slot = ?, coach_id = ?, status = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssisi", $booking_date, $time_slot, $coach_id, $status, $booking_id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php?updated=1");
        exit();
    }

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php?deleted=1");
        exit();
    }
}

// Fetch bookings
$bookings = $conn->query("
    SELECT b.*, u.name AS user_name, c.name AS coach_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN coaches c ON b.coach_id = c.id
    ORDER BY b.created_at DESC
");

// Coaches for update
$coaches = $conn->query("SELECT id, name FROM coaches");

// Pending count
$pending_count = $conn->query("
    SELECT COUNT(*) AS count FROM bookings WHERE status = 'pending'
")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel - AquaPool</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>


<header class="header">
    <div class="container header-content">
        <div class="logo-section">
            <span class="logo">🏊‍♂️</span>
            <h1>Shabda's Pool</h1>
        </div>
        <div class="user-section">
            <div class="user-info">
                <p>Welcome back,</p>
                <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                <span class="badge">Admin</span>
            </div>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
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
                <a href="admin_users.php" class="nav-link">Manage users</a>
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
        <div class="alert alert-success">Booking updated successfully!</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Booking deleted successfully!</div>
    <?php endif; ?>

    <div class="bookings-list bookings-grid">
        <?php while ($booking = $bookings->fetch_assoc()): ?>
        <div class="booking-card">

            <div class="booking-info">
                <h3>👤 <?php echo htmlspecialchars($booking['user_name']); ?></h3>

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
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <button class="btn btn-success btn-sm" name="action" value="approve">✓ Approve</button>
                    <button class="btn btn-danger btn-sm" name="action" value="reject">✗ Reject</button>
                </form>
                <?php endif; ?>

                <button type="button"
                        class="btn btn-warning btn-sm"
                        onclick="toggleEdit(<?php echo $booking['id']; ?>)">
                    ✏️ Update
                </button>

                <form method="POST" style="display:inline;"
                      onsubmit="return confirm('Delete this booking?');">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <button class="btn btn-danger btn-sm" name="action" value="delete">🗑 Delete</button>
                </form>
            </div>

            <!-- UPDATE FORM -->
            <form method="POST"
                  id="edit-<?php echo $booking['id']; ?>"
                  class="edit-form"
                  style="display:none;">
                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                <input type="hidden" name="action" value="update">

                <label>Date</label>
                <input type="date" name="booking_date"
                       value="<?php echo $booking['booking_date']; ?>" required>

                <label>Time Slot</label>
                <input type="text" name="time_slot"
                       value="<?php echo htmlspecialchars($booking['time_slot']); ?>" required>

                <label>Coach</label>
                <select name="coach_id">
                    <?php
                    $coaches->data_seek(0);
                    while ($coach = $coaches->fetch_assoc()):
                    ?>
                        <option value="<?php echo $coach['id']; ?>"
                            <?php if ($coach['id'] == $booking['coach_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($coach['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Status</label>
                <select name="status">
                    <option value="pending"  <?php if ($booking['status']=='pending') echo 'selected'; ?>>Pending</option>
                    <option value="approved" <?php if ($booking['status']=='approved') echo 'selected'; ?>>Approved</option>
                    <option value="rejected" <?php if ($booking['status']=='rejected') echo 'selected'; ?>>Rejected</option>
                </select>

                <button class="btn btn-success btn-sm" type="submit">💾 Save</button>
            </form>

        </div>
        <?php endwhile; ?>
    </div>
</main>

<script>
function toggleEdit(id) {
    const form = document.getElementById('edit-' + id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

</body>
</html>