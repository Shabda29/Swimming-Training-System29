<?php
include 'config.php';
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

// Admin can delete any booking, user can delete their own
if($_SESSION['role']=='admin'){
    mysqli_query($conn, "DELETE FROM bookings WHERE id='$id'");
} else {
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM bookings WHERE id='$id' AND user_id='$user_id'");
}

header("Location: index.php");
exit;
?>
