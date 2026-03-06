<?php
// GET /api/stats.php — public counts
require_once __DIR__ . '/../config.php';

$pdo     = getDB();
$approved = (int)$pdo->query("SELECT COUNT(*) FROM memos WHERE status='approved'")->fetchColumn();
$pending  = (int)$pdo->query("SELECT COUNT(*) FROM memos WHERE status='pending'")->fetchColumn();

jsonOut(['approved' => $approved, 'pending' => $pending]);
