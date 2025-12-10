<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coach_id = $_POST['coach_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    $sql = "INSERT INTO coach_timeslots (coach_id, slot_date, slot_time) 
            VALUES ('$coach_id', '$date', '$time')";

    if ($conn->query($sql)) {
        echo "<div class='alert alert-success'>Time slot added successfully!</div>";
    } else {
        echo "<div class='alert alert-error'>Error: " . $conn->error . "</div>";
    }
}
?>