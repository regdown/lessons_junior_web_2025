<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';

$q = $_GET['category_id'] ?? '1';

$sql = "SELECT id,name,price FROM products WHERE category_id = $q";
$result = [];
$error = null;

try {
    $stmt = $pdo->query($sql); // уязвимо: исполняем «как есть»
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!doctype html><meta charset="utf-8"><title>SQLi demo (vuln)</title>
<h1>SQL Injection (vuln)</h1>
<form>
  <label>category_id:
    <input name="category_id" value="<?= e((string)$q) ?>">
  </label>
  <button>Поиск</button>
</form>
<p><b>Выполненный SQL:</b> <code><?= e($sql) ?></code></p>
<?php if ($error): ?>
<p style="color:#b00">Ошибка: <?= e($error) ?></p>
<?php endif; ?>
<table border="1" cellpadding="6" cellspacing="0">
  <tr><th>ID</th><th>Название</th><th>Цена</th></tr>
  <?php foreach ($result as $row): ?>
    <tr>
      <td><?= e((string)$row['id']) ?></td>
      <td><?= e((string)$row['name']) ?></td>
      <td><?= e((string)$row['price']) ?> ₽</td>
    </tr>
  <?php endforeach; ?>
</table>
<p style="color:#b00"><b>Внимание:</b> эта страница специально уязвима, не используйте в продакшене.</p>
