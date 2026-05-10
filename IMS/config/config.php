<?php
/**
 * Application Configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ims');
define('DB_USER', 'root');
define('DB_PASS', '');

// App Settings
define('APP_NAME', 'IMS Pro');
define('APP_URL', 'http://localhost/IMS');
define('UPLOAD_PATH', __DIR__ . '/../uploads/photostorage/');
define('LOW_STOCK_THRESHOLD', 10);

// Security
define('SESSION_LIFETIME', 3600); // 1 hour
?>