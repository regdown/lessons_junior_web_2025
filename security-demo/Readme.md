Домашка (короткие задания)

SQLi HW:

В sqli_vuln.php переписать на параметризованный запрос.

Добавить строгую валидацию category_id и обработку ошибок (400).

XSS HW:

В xss_reflected_vuln.php заменить вывод на htmlspecialchars.

В xss_stored_vuln.php экранировать вывод и ограничить длину комментария (например, 300 символов).

CSRF HW:

В csrf_victim_vuln.php добавить токен (как в safe), проверить, что csrf_attack.html теперь не работает.

(Опционально) Добавить проверку заголовка SameSite для cookies.

Clickjacking HW:

В clickjack_victim_vuln.php добавить X-Frame-Options: SAMEORIGIN и CSP frame-ancestors 'self'.

Объяснить разницу между DENY, SAMEORIGIN, ALLOW-FROM.


php -S localhost:8000