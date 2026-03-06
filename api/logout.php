<?php
// POST /api/logout.php — destroy admin session
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
session_destroy();
jsonOut(['success' => true]);
