<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';

// создадим таблицу для комментариев
$pdo->exec("CREATE TABLE IF NOT EXISTS comments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  text TEXT NOT NULL
)");

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $text = $_POST['text'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO comments(text) VALUES (:t)");
    $stmt->execute([':t'=>$text]); // сохраняем без фильтрации
    header('Location: xss_stored_vuln.php'); exit;
}
$rows = $pdo->query("SELECT id,text FROM comments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><meta charset="utf-8"><title>Stored XSS (vuln)</title>
<h1>Stored XSS (vuln)</h1>
<form method="post">
  <textarea name="text" rows="3" cols="50" placeholder="Комментарий"></textarea><br>
  <button>Сохранить</button>
</form>
<ul>
  <?php foreach ($rows as $r): ?>
    <li><?= $r['text'] /* уязвимо: без htmlspecialchars */ ?></li>
  <?php endforeach; ?>
</ul>
<p style="color:#b00">Контент выводится без экранирования.</p>
