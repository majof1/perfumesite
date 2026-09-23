<?php
// config/db.php - Database Configuration & PDO Connection

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perfume_db');
define('DB_PORT', '3306');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Check if server connects without database selection
            try {
                $dsn_nodb = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
                $pdo_nodb = new PDO($dsn_nodb, DB_USER, DB_PASS);
                // Redirect to setup page if DB doesn't exist
                if (basename($_SERVER['PHP_SELF']) !== 'setup.php') {
                    header("Location: setup.php");
                    exit();
                }
            } catch (PDOException $ex) {
                die("<div style='font-family:sans-serif; padding:40px; background:#0f172a; color:#fff; border-radius:12px;'>
                    <h2 style='color:#ef4444;'>Database Connection Failed</h2>
                    <p>" . htmlspecialchars($e->getMessage()) . "</p>
                    <p>Please check your MySQL server settings (WAMP / XAMPP) or run <a href='setup.php' style='color:#3b82f6;'>setup.php</a>.</p>
                </div>");
            }
        }
    }
    return $pdo;
}

// Naira currency formatting helper
function formatNaira($amount) {
    return '₦' . number_format($amount, 2);
}

// Sanitization helper
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
