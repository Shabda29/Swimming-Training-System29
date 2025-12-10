<?php
require_once 'config.php';
requireLogin();

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    
    
    if ($action === 'update') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['message'] = "✓ User updated";
        header('Location: admin_users.php');
        exit();
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin</title>
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
                    <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="admin.php" class="btn btn-primary">← Back</a>
                    <a href="logout.php" class="btn btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </header>
    
    <main class="container">
        <h2>👥 Users</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        
        <?php if ($users->num_rows === 0): ?>
            <div class="empty">No users found</div>
        <?php else: ?>
            <div class="grid">
                <?php while ($user = $users->fetch_assoc()): ?>
                    <div class="card">
                        <form method="POST" class="edit-form">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role">
                                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <small>Joined: <?php echo date('M j, Y', strtotime($user['created_at'])); ?></small>
                            </div>
                            
                            <div class="actions">
                                <button type="submit" name="action" value="update" class="btn btn-primary">Update</button>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <style>
        .grid { display: grid; gap: 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; }
        .actions { display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee; }
        .alert { background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .empty { text-align: center; padding: 3rem; color: #666; }
    </style>
</body>
</html>