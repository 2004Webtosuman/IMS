<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IMS Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        body { background-color: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; }
        .register-card { width: 100%; max-width: 500px; background: white; padding: 2.5rem; border-radius: 20px; box-shadow: var(--shadow); }
        .register-header { text-align: center; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Create Account</h1>
            <p class="text-muted">Join IMS Pro to manage your business</p>
        </div>

        <?php echo display_flash(); ?>

        <form action="insertNewAccount.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username" required minlength="4" maxlength="20">
                <small class="text-muted">4-20 characters</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Create a strong password" required minlength="8">
                <small class="text-muted">Min 8 characters, at least 1 number</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="retype_password" class="form-control" placeholder="Repeat your password" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem; margin-top: 1rem;">
                <i class="fas fa-user-plus"></i> Register Now
            </button>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <span class="text-muted">Already have an account?</span>
                <a href="loginpage.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Sign In</a>
            </div>
        </form>
    </div>
</body>
</html>
