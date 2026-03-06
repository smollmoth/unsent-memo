<?php
// POST /api/approve.php — approve a pending memo (admin only)
require_once __DIR__ . '/../config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonOut(['error' => 'Method not allowed'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
$id   = (int)($body['id'] ?? 0);

if (!$id) {
    jsonOut(['error' => 'Invalid id'], 422);
}

$pdo  = getDB();
$stmt = $pdo->prepare("
    UPDATE memos SET status = 'approved', approved_at = NOW()
    WHERE id = ? AND status = 'pending'
");
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    jsonOut(['error' => 'Memo not found or already actioned.'], 404);
}
jsonOut(['success' => true]);
