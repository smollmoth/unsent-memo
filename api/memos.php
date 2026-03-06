<?php
// GET /api/memos.php — returns all approved memos (public)
require_once __DIR__ . '/../config.php';

$pdo  = getDB();
$stmt = $pdo->query("
    SELECT id, memo_text AS `text`, memo_to AS `to`, company, color
    FROM memos
    WHERE status = 'approved'
    ORDER BY approved_at DESC
");
jsonOut($stmt->fetchAll());
