<?php
// POST /api/login.php — admin login
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonOut(['error' => 'Method not allowed'], 405);
}

if (session_status() === PHP_SESSION_NONE) session_start();

$body     = json_decode(file_get_contents('php://input'), true);
$password = $body['password'] ?? '';

if ($password === ADMIN_PASSWORD) {
    $_SESSION['admin_logged_in'] = true;
    jsonOut(['success' => true]);
} else {
    // Small delay to slow brute-force
    sleep(1);
    jsonOut(['error' => 'Incorrect password.'], 401);
}
