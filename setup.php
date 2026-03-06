<?php
// ============================================================
//  UNSENT MEMO — One-time Setup Script
//
//  1. Fill in config.php first
//  2. Visit yourwebsite.com/setup.php in your browser
//  3. Once you see "Setup complete!" — DELETE this file!
// ============================================================

require_once 'config.php';

$errors   = [];
$messages = [];

// Check config is filled in
if (empty(DB_NAME) || empty(DB_USER) || empty(DB_PASS)) {
    $errors[] = 'config.php is not filled in. Open config.php and add your database credentials.';
}

if (ADMIN_PASSWORD === 'changeme123') {
    $errors[] = 'You must change ADMIN_PASSWORD in config.php before running setup.';
}

if (empty($errors)) {
    try {
        $pdo = getDB();

        // Create memos table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS memos (
                id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                memo_text    TEXT        NOT NULL,
                memo_to      VARCHAR(120) NOT NULL,
                company      VARCHAR(120) NOT NULL DEFAULT 'Anonymous',
                color        VARCHAR(10)  NOT NULL DEFAULT '#1A1A1A',
                status       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                submitted_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                approved_at  DATETIME    NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        $messages[] = '✅ Table <code>memos</code> created (or already existed).';

        // Insert seed memos so the archive isn't empty
        $count = $pdo->query("SELECT COUNT(*) FROM memos WHERE status='approved'")->fetchColumn();
        if ($count == 0) {
            $seed = [
                ["You asked for feedback but punished everyone who gave it. I stopped being honest after the third person got reassigned.", "My Manager", "Acme Corp", "#C1121F"],
                ["You went to bat for me when I didn't even know I needed it. I got that promotion because you said something in a room I wasn't in. Thank you.", "My Manager", "Stripe", "#2D6A4F"],
                ["I haven't taken a real day off in nine months. I reply to Slack on weekends. I wake up anxious. And you just sent a 7am Monday invite.", "HR", "Meta", "#2B2D42"],
                ["Every idea I brought to you ended up in your mouth in the next all-hands. No credit. No mention. I watched you get promoted on the back of my work.", "The CEO", "Acme Corp", "#1438BD"],
                ["I don't think you realize how much calmer everyone is when you're out of the office. That's not a compliment.", "My Manager", "Goldman Sachs", "#6B2D8B"],
                ["When I was falling apart after my dad died, you covered for me without making it a big deal. You just said 'take what you need.' I've never forgotten that.", "Sarah from Finance", "Shopify", "#0A7373"],
                ["I've been quietly applying for other jobs for four months. I feel guilty but mostly I feel relieved every time I get a callback.", "Myself", "McKinsey", "#7D2239"],
                ["I still think the idea I pitched in Q2 was better than what we actually built. The data proved it six months later. Nobody ever said anything.", "My Team", "Spotify", "#1438BD"],
                ["I cried in the bathroom after that feedback session. Not because you were wrong. Because you were right and I wasn't ready to hear it in front of everyone.", "My Manager", "Deloitte", "#E76F51"],
                ["You gave me a shot when my CV shouldn't have made the shortlist. I've been trying to justify that decision every single day since.", "The CEO", "Stripe", "#386641"],
                ["The 'open door policy' was never open. Every time someone walked through it, something bad happened to them. That door was a trap.", "HR", "Uber", "#C1121F"],
                ["I told myself I'd speak up in the next meeting. I've told myself that 47 times. I'm still waiting for the right moment.", "Myself", "Amazon", "#2B2D42"],
                ["You quietly mentored me for two years without calling it that. You answered my dumb questions at 9pm. You made me a better engineer.", "David K.", "Notion", "#0A7373"],
                ["The culture deck says we value work-life balance. The culture deck is fiction.", "The CEO", "WeWork", "#6B2D8B"],
                ["I actually love this job. I don't say that because I'm afraid it'll disappear. I come home excited. I don't take that for granted.", "Myself", "Basecamp", "#2D6A4F"],
                ["You fired someone two days before their stock vested. Everyone saw it. Nobody believed it was a coincidence. We just couldn't prove it.", "The CEO", "Acme Corp", "#7D2239"],
            ];
            $stmt = $pdo->prepare("
                INSERT INTO memos (memo_text, memo_to, company, color, status, approved_at)
                VALUES (?, ?, ?, ?, 'approved', NOW())
            ");
            foreach ($seed as $row) {
                $stmt->execute($row);
            }
            $messages[] = '✅ Seeded ' . count($seed) . ' example memos.';
        } else {
            $messages[] = 'ℹ️ Skipped seeding — approved memos already exist.';
        }

        $messages[] = '<strong>🎉 Setup complete! Delete this file (setup.php) now.</strong>';

    } catch (Exception $e) {
        $errors[] = 'Database error: ' . htmlspecialchars($e->getMessage());
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Setup — Unsent Memo</title>
<style>
  body { font-family: monospace; max-width: 640px; margin: 60px auto; padding: 0 24px; background: #f5f5f5; }
  h1 { font-size: 24px; margin-bottom: 24px; }
  .msg  { background: #d4edda; border: 1px solid #c3e6cb; padding: 12px 16px; margin: 8px 0; border-radius: 4px; }
  .err  { background: #f8d7da; border: 1px solid #f5c6cb; padding: 12px 16px; margin: 8px 0; border-radius: 4px; color: #721c24; }
  .warn { background: #fff3cd; border: 1px solid #ffeeba; padding: 12px 16px; margin: 16px 0; border-radius: 4px; }
  a { color: #1438BD; }
</style>
</head>
<body>
<h1>Unsent Memo — Setup</h1>
<?php foreach ($errors as $e): ?>
  <div class="err">❌ <?= $e ?></div>
<?php endforeach; ?>
<?php foreach ($messages as $m): ?>
  <div class="msg"><?= $m ?></div>
<?php endforeach; ?>
<?php if (!empty($errors)): ?>
  <div class="warn">
    Fix the errors above, then reload this page.<br><br>
    <strong>How to find your Hostinger DB credentials:</strong><br>
    1. Log into <a href="https://hpanel.hostinger.com" target="_blank">hpanel.hostinger.com</a><br>
    2. Click your website → <strong>Databases</strong> → <strong>MySQL Databases</strong><br>
    3. Create a database + user if you haven't yet<br>
    4. Copy: Database Name, Username, Password into <code>config.php</code><br>
    5. Host is almost always <code>localhost</code>
  </div>
<?php endif; ?>
<?php if (empty($errors)): ?>
  <div class="warn">
    ⚠️ <strong>Important:</strong> Delete <code>setup.php</code> from your server now that setup is done.<br>
    Go to <a href="index.html">index.html</a> or <a href="admin.html">admin.html</a>
  </div>
<?php endif; ?>
</body>
</html>
