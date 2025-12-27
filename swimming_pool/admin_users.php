<?php
require_once 'config.php';
requireLogin();

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $action  = $_POST['action'] ?? '';

    /* ========= UPDATE USER ========= */
    if ($action === 'update') {
        $name  = trim($_POST['name']);
        $email = trim($_POST['email']);
        $role  = $_POST['role'];

        $stmt = $conn->prepare(
            "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?"
        );
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "✓ User updated successfully";
        header("Location: admin_users.php");
        exit();
    }

    /* ========= DELETE USER (FK SAFE) ========= */
    if ($action === 'delete' && $user_id != $_SESSION['user_id']) {

    // DELETE USER BOOKINGS FIRST
    $stmt = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // DELETE USER
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "✓ User and bookings deleted";
    header("Location: admin_users.php");
    exit();
}

}


$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$pending_count = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE status='pending'")
                      ->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

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
    <a href="admin.php" class="nav-link">
        Admin Panel
        <?php if ($pending_count > 0): ?>
            <span class="notification-badge"><?php echo $pending_count; ?></span>
        <?php endif; ?>
    </a>
    <a href="admin_users.php" class="nav-link active">Manage Users</a>
</div>
</nav>

<main class="container">
<h2 style="margin-top:20px; margin-bottom:20px;">👥 Manage Users</h2>

<?php if (isset($_SESSION['message'])): ?>
<div class="alert">
    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
</div>
<?php endif; ?>

<table class="users-table" style="border-collapse:collapse; height:70%;">
<thead style="height:60px;">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Joined</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>

<?php while ($user = $users->fetch_assoc()): ?>
<tr style="text-align:left;">
    <!-- ✅ FORM IS INSIDE TD (VALID HTML) -->
    <td>
        <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    </td>

    <td>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </td>

    <td>
        <select name="role" required>
            <option value="user" <?php if ($user['role']=='user') echo 'selected'; ?>>User</option>
            <option value="admin" <?php if ($user['role']=='admin') echo 'selected'; ?>>Admin</option>
        </select>
    </td>

    <td>
        <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
    </td>

    <td class="actions">
        <button type="submit" name="action" value="update">
            Update
        </button>

        <?php if ($user['id'] != $_SESSION['user_id']): ?>
            <button type="submit" name="action" value="delete" style="background-color:red;"
                onclick="return confirm('Are you sure you want to delete this user?');">
                Delete
            </button>
        <?php endif; ?>
        </form>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</main>

<style>
.users-table{width:100%;border-collapse:collapse;margin-top:20px;background:#fff}
.users-table th,.users-table td{padding:10px;border-bottom:1px solid #eee}
.users-table th{background:#f5f5f5}
.users-table input,.users-table select{width:100%;padding:6px}
.actions{display:flex;gap:6px}
.alert{background:#d4edda;padding:10px;margin-bottom:10px}
</style>

</body>
</html>