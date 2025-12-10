<?php
include 'config.php';

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($check) > 0){
        $error = "Username already exists!";
    } else {
        mysqli_query($conn, "INSERT INTO users (username,password) VALUES ('$username','$password')");
        $success = "Registration successful! <a href='login.php'>Login here</a>";
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Register</button>
</form>
<?php
if(isset($error)) echo "<p style='color:red'>$error</p>";
if(isset($success)) echo "<p style='color:green'>$success</p>";
?>
