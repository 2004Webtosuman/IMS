<?php
/**
 * Logout Handler
 */
require_once 'includes/functions.php';

session_start();
$_SESSION = [];
session_destroy();

if (isset($_COOKIE['username'])) {
    setcookie("username", "", time() - 3600, "/");
}

header("Location: loginpage.php");
exit;
?>