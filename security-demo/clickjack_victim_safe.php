<?php
declare(strict_types=1);
// Защита: запрет встраивания
header('X-Frame-Options: DENY');
// Дополнительно (современный подход):
header("Content-Security-Policy: frame-ancestors 'none'");
?>
<!doctype html><meta charset="utf-8"><title>Clickjack victim (safe)</title>
<h1>Clickjacking victim (safe)</h1>
<p>Эта страница не должна встраиваться в iframe (DENY/CSP).</p>
<button onclick="alert('Ок')">Кнопка</button>
