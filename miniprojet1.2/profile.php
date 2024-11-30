<?php
require_once 'includes/session_manager.php';
require_once 'includes/config.php';
initializeSession();
requireAuth();

// profile parametre dyawlo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = Database::getInstance();
        $table = isCoordinator() ? 'coordinators' : 'admins';
        
        //update query
        $stmt = $pdo->prepare("
            UPDATE $table 
            SET name = :name,
                email = :email
            WHERE email = :current_email
        ");
        
        $stmt->execute([
            'name' => $_POST['username'],
            'email' => $_POST['email'],
            'current_email' => $_SESSION['email']
        ]);

        // Update password 
        if (!empty($_POST['new_password'])) {
            $stmt = $pdo->prepare("
                UPDATE $table 
                SET password = :password
                WHERE email = :email
            ");
            
            $stmt->execute([
                'password' => password_hash($_POST['new_password'], PASSWORD_DEFAULT),
                'email' => $_SESSION['email']
            ]);
        }

        $_SESSION['username'] = $_POST['username'];
        $_SESSION['email'] = $_POST['email'];
        $success_message = "Profile updated successfully!";
        
    } catch (PDOException $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}

// jbed data dyal user
try {
    $pdo = Database::getInstance();
    $table = isCoordinator() ? 'coordinators' : 'admins';
    
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = :email");
    $stmt->execute(['email' => $_SESSION['email']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception("User not found");
    }
    
} catch (Exception $e) {
    $error_message = "Error fetching user data: " . $e->getMessage();
}

include 'includes/header.php';
?>

<div class="main-content" style="margin-left: 50px; width:auto">
    <div class="profile-container">
        <?php if (isset($success_message)): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h2>Profile Settings</h2>
        <form id="profileForm" method="POST" class="profile-form">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?php echo isCoordinator() ? 'Coordinator' : 'Super Admin'; ?>" readonly>
            </div>

            <?php if (isCoordinator()): ?>
            <div class="form-group">
                <label>Program</label>
                <input type="text" value="<?php echo htmlspecialchars($user['program'] ?? ''); ?>" readonly>
            </div>
            <?php endif; ?>

            <div class="password-section">
                <h3>Change Password</h3>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" minlength="6">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
.profile-container {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: bold;
    color: #333;
}

.form-group input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group input[readonly] {
    background-color: #f5f5f5;
}

.password-section {
    border-top: 1px solid #eee;
    padding-top: 20px;
    margin-top: 20px;
}

.form-actions {
    margin-top: 20px;
}

.save-btn {
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.save-btn:hover {
    background: #45a049;
}

.alert {
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const newPassword = this.elements['new_password'].value;
    const confirmPassword = this.elements['confirm_password'].value;

    if (newPassword || confirmPassword) {
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return;
        }
        if (newPassword.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long!');
            return;
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>