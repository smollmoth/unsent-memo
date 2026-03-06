<?php
// GET /api/pending.php — returns pending memos (admin only)
require_once __DIR__ . '/../config.php';
requireAuth();

$pdo  = getDB();
$stmt = $pdo->query("
    SELECT id, memo_text AS `text`, memo_to AS `to`, company, color, submitted_at
    FROM memos
    WHERE status = 'pending'
    ORDER BY submitted_at ASC
");
jsonOut($stmt->fetchAll());
