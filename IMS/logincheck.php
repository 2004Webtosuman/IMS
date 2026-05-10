<?php
/**
 * Login Handler
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash("Invalid security token.", "danger");
        redirect('loginpage.php');
    }

    $username = trim($_POST['Username'] ?? '');
    $password = $_POST['Password'] ?? '';

    if (empty($username) || empty($password)) {
        set_flash("Username and password are required.", "danger");
        redirect('loginpage.php');
    }

    try {
        // Query user - check both password_hash (new) and password (old - for migration)
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role, status FROM newaccountregistration WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user['status'] !== 'active') {
                set_flash("Your account is " . $user['status'] . ". Please contact admin.", "warning");
                redirect('loginpage.php');
            }

            // Verify password (using password_verify)
            if (password_verify($password, $user['password_hash'])) {
                // Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if (isset($_POST['remember'])) {
                    setcookie("username", $username, time() + (86400 * 30), "/"); // 30 days
                }

                set_flash("Welcome back, " . $username . "!");
                redirect('home.php');
            } else {
                set_flash("Invalid username or password.", "danger");
                redirect('loginpage.php');
            }
        } else {
            set_flash("Invalid username or password.", "danger");
            redirect('loginpage.php');
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        set_flash("An error occurred during login.", "danger");
        redirect('loginpage.php');
    }
} else {
    redirect('loginpage.php');
}
?>