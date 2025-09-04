<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['csrf'];

$pdo->exec("CREATE TABLE IF NOT EXISTS settings (k TEXT PRIMARY KEY, v TEXT)");
$msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('CSRF token invalid');
    }
    $name = $_POST['shop_name'] ?? '';
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings(k,v) VALUES ('shop_name', :v)");
    $stmt->execute([':v'=>$name]);
    $msg = "Название обновлено на: " . e($name);
}
$cur = $pdo->query("SELECT v FROM settings WHERE k='shop_name'")->fetchColumn() ?: 'Мой магазин';
?>
<!doctype html><meta charset="utf-8"><title>CSRF victim (safe)</title>
<h1>CSRF victim (safe)</h1>
<?php if ($msg) echo "<p style='color:green'>$msg</p>"; ?>
<form method="post">
  <input type="hidden" name="csrf" value="<?= e($token) ?>">
  <label>Название магазина:
    <input name="shop_name" value="<?= e((string)$cur) ?>">
  </label>
  <button>Сохранить</button>
</form>
<p>Добавлен CSRF-токен, посторонняя страница не сможет выполнить POST.</p>
