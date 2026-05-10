<?php
/**
 * Global Helper Functions
 */
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * CSRF Protection
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Flash Messages
 */
function set_flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function display_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return "<div class='toast toast-{$flash['type']}' id='flash-toast'>
                    <div class='toast-content'>
                        <span class='toast-message'>{$flash['message']}</span>
                    </div>
                </div>";
    }
    return '';
}

/**
 * Auth Helpers
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($path) {
    header("Location: " . APP_URL . "/" . ltrim($path, '/'));
    exit;
}

/**
 * Data Sanitization
 */
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * File Upload Helper
 */
function upload_photo($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return ['error' => 'Invalid file type. Only JPG, PNG, GIF are allowed.'];
    }
    
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['error' => 'File size exceeds 2MB limit.'];
    }
    
    $new_name = uniqid('img_', true) . '.' . $ext;
    $dest = UPLOAD_PATH . $new_name;
    
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => $new_name];
    }
    
    return ['error' => 'Failed to move uploaded file.'];
}
?>
