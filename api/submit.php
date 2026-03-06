<?php
// POST /api/submit.php — save a new memo as "pending"
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonOut(['error' => 'Method not allowed'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);

$text    = trim($body['text']    ?? '');
$to      = trim($body['to']      ?? '');
$company = trim($body['company'] ?? 'Anonymous');
$color   = trim($body['color']   ?? '#1A1A1A');

// Validate
if (mb_strlen($text) < 20) {
    jsonOut(['error' => 'Message too short (minimum 20 characters).'], 422);
}
if (mb_strlen($text) > 600) {
    jsonOut(['error' => 'Message too long (maximum 600 characters).'], 422);
}
if (empty($to)) {
    jsonOut(['error' => '"To" field is required.'], 422);
}

// Sanitise lengths
$to      = mb_substr($to, 0, 120);
$company = mb_substr($company ?: 'Anonymous', 0, 120);
$color   = preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : '#1A1A1A';

$pdo  = getDB();
$stmt = $pdo->prepare("
    INSERT INTO memos (memo_text, memo_to, company, color, status)
    VALUES (?, ?, ?, ?, 'pending')
");
$stmt->execute([$text, $to, $company, $color]);

jsonOut(['success' => true, 'message' => 'Memo received. It will appear after review.']);
