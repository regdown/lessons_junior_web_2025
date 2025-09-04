<?php
declare(strict_types=1);
// Эхоим значение без экранирования (уязвимо)
$msg = $_GET['msg'] ?? '';
?>
<!doctype html><meta charset="utf-8"><title>Reflected XSS (vuln)</title>
<h1>Reflected XSS (vuln)</h1>
<form>
  <input name="msg" placeholder="Ваш текст" value="<?= $msg ?>">
  <button>Показать</button>
</form>
<p>Вы ввели: <?= $msg ?></p>
<p style="color:#b00">Страница специально уязвима (нет экранирования).</p>
