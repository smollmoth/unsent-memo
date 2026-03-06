<?php
// ============================================================
//  UNSENT MEMO — Database & Admin Configuration
//  Fill in your details from Hostinger hPanel then save.
// ============================================================

// --- DATABASE CREDENTIALS ---
// In Hostinger: hPanel → Databases → MySQL Databases
define('DB_HOST', 'localhost');       // Almost always "localhost" on Hostinger
define('DB_NAME', '');                // e.g. u123456789_unsentmemo
define('DB_USER', '');                // e.g. u123456789_admin
define('DB_PASS', '');                // The password you set for the DB user

// --- ADMIN PASSWORD ---
// This is the password you'll use to log into /admin.html
// Change this to something strong before going live!
define('ADMIN_PASSWORD', 'changeme123');

// ============================================================
//  Helper functions — no need to edit below this line
// ============================================================

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database connection failed. Check config.php.']);
            exit;
        }
    }
    return $pdo;
}

function requireAuth() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_logged_in'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

function jsonOut($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
