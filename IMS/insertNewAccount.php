<?php
/**
 * Registration Handler
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash("Invalid security token.", "danger");
        redirect('newaccountregistration.php');
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $retype_password = $_POST['retype_password'] ?? '';

    // Validation
    if (strlen($username) < 4 || strlen($username) > 20) {
        set_flash("Username must be between 4 and 20 characters.", "danger");
        redirect('newaccountregistration.php');
    }

    if (strlen($password) < 8 || !preg_match("/[0-9]/", $password)) {
        set_flash("Password must be at least 8 characters and include at least one number.", "danger");
        redirect('newaccountregistration.php');
    }

    if ($password !== $retype_password) {
        set_flash("Passwords do not match.", "danger");
        redirect('newaccountregistration.php');
    }

    try {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM newaccountregistration WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            set_flash("Username already taken.", "danger");
            redirect('newaccountregistration.php');
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO newaccountregistration (username, password_hash, status, role) VALUES (?, ?, 'pending', 'user')");
        $stmt->execute([$username, $password_hash]);

        set_flash("Registration successful! Your account is pending activation by admin.", "success");
        redirect('loginpage.php');

    } catch (PDOException $e) {
        error_log($e->getMessage());
        set_flash("An error occurred during registration.", "danger");
        redirect('newaccountregistration.php');
    }
} else {
    redirect('newaccountregistration.php');
}
?>