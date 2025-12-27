<?php
require_once 'config.php';
requireLogin();

// Get coaches
$coaches_query = "SELECT * FROM coaches ORDER BY id";
$coaches = $conn->query($coaches_query);

// Get user's bookings
$user_bookings_query = "SELECT b.*, c.name as coach_name, c.specialty 
                        FROM bookings b 
                        JOIN coaches c ON b.coach_id = c.id 
                        WHERE b.user_id = ? 
                        ORDER BY b.booking_date DESC, b.time_slot";
$stmt = $conn->prepare($user_bookings_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user_bookings = $stmt->get_result();
$stmt->close();

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_session'])) {
    $coach_id = $_POST['coach_id'] ?? 0;
    $booking_date = $_POST['booking_date'] ?? '';
    $time_slot = $_POST['time_slot'] ?? '';
    
    if ($coach_id && $booking_date && $time_slot) {
        // Check if slot is already booked
        $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE coach_id = ? AND booking_date = ? AND time_slot = ? AND status = 'approved'");
        $check_stmt->bind_param("iss", $coach_id, $booking_date, $time_slot);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $booking_error = 'This time slot is already booked';
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO bookings (user_id, coach_id, booking_date, time_slot) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("iiss", $_SESSION['user_id'], $coach_id, $booking_date, $time_slot);
            
            if ($insert_stmt->execute()) {
                $booking_success = 'Booking request submitted! Waiting for admin approval.';
                header('Location: my_bookings.php?success=1');
                exit();
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Session - Shabda's pool</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <span class="logo">🏊‍♂️</span>
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
             <a href="index.php" class="nav-link ">Home</a>
              <a href="About.php" class="nav-link">About us</a>
            <a href="book.php" class="nav-link active ">Booking Session</a>
            <a href="my_bookings.php" class="nav-link">My Bookings</a>
             <a href="Contact.php" class="nav-link">Contact_us</a>

            <?php if (isAdmin()): ?>
                <a href="admin.php" class="nav-link">Admin Panel</a>
            <?php endif; ?>
        </div>
    </nav>
    
    <main class="container main-content">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Booking request submitted successfully!</div>
        <?php endif; ?>
        
        <div class="booking-grid">
            <div class="coaches-section">
                <h2>Select a Coach</h2>
                <div class="coaches-list" id="coachesList">
                    <?php while ($coach = $coaches->fetch_assoc()): ?>
                        <div class="coach-card" data-coach-id="<?php echo $coach['id']; ?>">
                            <div class="coach-emoji"><?php echo $coach['image_emoji']; ?></div>
                            <div class="coach-info">
                                <h3><?php echo htmlspecialchars($coach['name']); ?></h3>
                                <p><?php echo htmlspecialchars($coach['specialty']); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="booking-section">
                <h2>Select Date & Time</h2>
                <form method="POST" action="" id="bookingForm" class="booking-form">
                    <input type="hidden" name="coach_id" id="coachId" required>
                    
                    <div class="form-group">
                        <label>📅 Select Date</label>
                        <input type="date" name="booking_date" id="bookingDate" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                  

                    <!-- TIME SLOT (FROM - TO) -->
                    <div class="form-group">
                        <label>⏰ Select Time</label>

                        <div style="display: flex; flex-direction: row; gap: 42px;">

                            <!-- From Time -->
                            <div style="display: flex; gap: 10px; align-items: center; margin:10px;">
                                <strong>From:</strong>

                                <select name="from_hour" required>
                                    <?php for ($h = 1; $h <= 12; $h++): ?>
                                        <option value="<?= $h ?>"><?= $h ?></option>
                                    <?php endfor; ?>
                                </select>

                                :

                                <select name="from_minute" required>
                                    <?php foreach ([0, 15, 30, 45] as $m): ?>
                                        <option value="<?= sprintf('%02d', $m) ?>"><?= sprintf('%02d', $m) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="from_ampm" required>
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>

                            <!-- To Time -->
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <strong>To:</strong>

                                <select name="to_hour" required>
                                    <?php for ($h = 1; $h <= 12; $h++): ?>
                                        <option value="<?= $h ?>"><?= $h ?></option>
                                    <?php endfor; ?>
                                </select>

                                :

                                <select name="to_minute" required>
                                    <?php foreach ([0, 15, 30, 45] as $m): ?>
                                        <option value="<?= sprintf('%02d', $m) ?>"><?= sprintf('%02d', $m) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="to_ampm" required>
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>

                        </div>

                        <input type="hidden" name="time_slot" id="timeSlot" required>
                    </div>
                    <button type="submit" name="book_session" class="btn btn-primary btn-block">Submit Booking Request</button>
                </form>
            </div>
        </div>
    </main>
 <script>
    // TIME SLOT BUILDER
    function updateTimeSlot() {
        const fh = document.querySelector("select[name='from_hour']").value;
        const fm = document.querySelector("select[name='from_minute']").value;
        const fa = document.querySelector("select[name='from_ampm']").value;

        const th = document.querySelector("select[name='to_hour']").value;
        const tm = document.querySelector("select[name='to_minute']").value;
        const ta = document.querySelector("select[name='to_ampm']").value;

        document.getElementById("timeSlot").value =
            `${fh}:${fm} ${fa} - ${th}:${tm} ${ta}`;
    }

    document.querySelectorAll("select").forEach(select => {
        select.addEventListener("change", updateTimeSlot);
    });

    updateTimeSlot();


    // ================================
    // COACH SELECTION FIXED
    // ================================
    document.querySelectorAll(".coach-card").forEach(card => {
        card.addEventListener("click", function () {
            const coachId = this.getAttribute("data-coach-id");
            document.getElementById("coachId").value = coachId;

            document.querySelectorAll(".coach-card").forEach(c => c.classList.remove("selected"));
            this.classList.add("selected");
        });
    });
    </script>
</body>
</html>