<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
$pdo->exec("CREATE TABLE IF NOT EXISTS comments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  text TEXT NOT NULL
)");
function e($s){return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $text = trim((string)($_POST['text'] ?? ''));
    if ($text !== '') {
        $stmt = $pdo->prepare("INSERT INTO comments(text) VALUES (:t)");
        $stmt->execute([':t'=>$text]);
    }
    header('Location: xss_stored_safe.php'); exit;
}
$rows = $pdo->query("SELECT id,text FROM comments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><meta charset="utf-8"><title>Stored XSS (safe)</title>
<h1>Stored XSS (safe)</h1>
<form method="post">
  <textarea name="text" rows="3" cols="50" placeholder="Комментарий"></textarea><br>
  <button>Сохранить</button>
</form>
<ul>
  <?php foreach ($rows as $r): ?>
    <li><?= e((string)$r['text']) ?></li>
  <?php endforeach; ?>
</ul>
