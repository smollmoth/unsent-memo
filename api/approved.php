<?php
// GET /api/approved.php — returns approved/published memos (admin only)
require_once __DIR__ . '/../config.php';
requireAuth();

$pdo  = getDB();
$stmt = $pdo->query("
    SELECT id, memo_text AS `text`, memo_to AS `to`, company, color, submitted_at
    FROM memos
    WHERE status = 'approved'
    ORDER BY submitted_at DESC
");
jsonOut($stmt->fetchAll());
