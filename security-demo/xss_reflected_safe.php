<?php
declare(strict_types=1);
function e($s){return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
$msg = $_GET['msg'] ?? '';
?>
<!doctype html><meta charset="utf-8"><title>Reflected XSS (safe)</title>
<h1>Reflected XSS (safe)</h1>
<form>
  <input name="msg" placeholder="Ваш текст" value="<?= e($msg) ?>">
  <button>Показать</button>
</form>
<p>Вы ввели: <?= e($msg) ?></p>
