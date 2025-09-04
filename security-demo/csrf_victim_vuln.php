<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';

// «важное действие»: смена названия магазина (условная настройка)
$pdo->exec("CREATE TABLE IF NOT EXISTS settings (k TEXT PRIMARY KEY, v TEXT)");
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = $_POST['shop_name'] ?? '';
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings(k,v) VALUES ('shop_name', :v)");
    $stmt->execute([':v'=>$name]); // НЕТ проверки происхождения запроса (уязвимо)
    $msg = "Название обновлено на: " . e($name);
}
$cur = $pdo->query("SELECT v FROM settings WHERE k='shop_name'")->fetchColumn() ?: 'Мой магазин';
?>
<!doctype html><meta charset="utf-8"><title>CSRF victim (vuln)</title>
<h1>CSRF victim (vuln)</h1>
<?php if (!empty($msg)) echo "<p style='color:green'>$msg</p>"; ?>
<form method="post">
  <label>Название магазина:
    <input name="shop_name" value="<?= e((string)$cur) ?>">
  </label>
  <button>Сохранить</button>
</form>
<p>Нет CSRF-защиты. Любая страница может отправить сюда POST.</p>
