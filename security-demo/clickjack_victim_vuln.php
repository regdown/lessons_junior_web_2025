<?php
declare(strict_types=1);
// НЕТ X-Frame-Options — страницу можно встраивать в iframe
?>
<!doctype html><meta charset="utf-8"><title>Clickjack victim (vuln)</title>
<h1>Clickjacking victim (vuln)</h1>
<p>Эта страница может быть встроена в сторонний сайт и перекрыта прозрачными элементами.</p>
<button onclick="alert('Важное действие выполнено')">Важная кнопка</button>
