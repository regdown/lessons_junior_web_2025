<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="ru">
<meta charset="utf-8">
<title>Security Demo</title>
<style>
body{font-family:system-ui,Arial;max-width:900px;margin:24px auto;padding:0 16px}
a{color:#0d6efd} code{background:#f5f5f7;padding:2px 4px;border-radius:4px}
h2{margin-top:28px}
ul{line-height:1.7}
</style>
<h1>Security Demo (локально)</h1>
<p>Набор учебных страниц, показывающих типичные проблемы безопасности (только для локального запуска).</p>

<h2>SQL Injection</h2>
<ul>
  <li><a href="sqli_vuln.php">Уязвимая версия</a> (поиск по <code>category_id</code>)</li>
  <li><a href="sqli_safe.php">Исправленная версия</a></li>
</ul>

<h2>XSS</h2>
<ul>
  <li><a href="xss_reflected_vuln.php?msg=Привет">Reflected XSS (vuln)</a></li>
  <li><a href="xss_reflected_safe.php?msg=Привет">Reflected XSS (safe)</a></li>
  <li><a href="xss_stored_vuln.php">Stored XSS (vuln)</a></li>
  <li><a href="xss_stored_safe.php">Stored XSS (safe)</a></li>
</ul>

<h2>CSRF</h2>
<ul>
  <li><a href="csrf_victim_vuln.php">Жертва без защиты (vuln)</a></li>
  <li><a href="csrf_victim_safe.php">Жертва с CSRF-токеном (safe)</a></li>
  <li><a href="csrf_attack.html">Имитация внешней страницы (локально)</a></li>
</ul>

<h2>Clickjacking</h2>
<ul>
  <li><a href="clickjack_victim_vuln.php">Жертва без защиты (vuln)</a></li>
  <li><a href="clickjack_victim_safe.php">Жертва с X-Frame-Options (safe)</a></li>
  <li><a href="clickjack_frame.html">Страница-встраиватель</a></li>
</ul>
