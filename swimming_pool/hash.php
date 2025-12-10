<?php
// hash.php
$newPassword = 'admin123'; // change this to whatever you want
echo password_hash($newPassword, PASSWORD_BCRYPT);
?>
