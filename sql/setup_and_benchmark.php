<?php
/**
 * setup_and_benchmark.php
 *
 * Запуск:
 *   php setup_and_benchmark.php [db_path] [scale]
 *     db_path: путь к файлу БД (по умолчанию demo.sqlite)
 *     scale:   множитель данных (1=быстро, 3=много; по умолчанию 1)
 *
 * Что делает:
 *  - Создаёт SQLite-базу со связанными таблицами (users, products, orders, order_items)
 *  - Наполняет большим количеством данных
 *  - Показывает бенчмарк до/после индексации
 *  - Печатает EXPLAIN QUERY PLAN некоторых запросов
 */

$path  = $argv[1] ?? 'demo.sqlite';
$scale = max(1, intval($argv[2] ?? 1));

if (file_exists($path)) unlink($path);

function tlog($msg) { echo '['.date('H:i:s')."] $msg\n"; }

try {
    // --- Подключение и PRAGMA
    $pdo = new PDO("sqlite:$path");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("PRAGMA foreign_keys = ON;");
    $pdo->exec("PRAGMA journal_mode = WAL;");
    $pdo->exec("PRAGMA synchronous = NORMAL;");
    $pdo->exec("PRAGMA temp_store = MEMORY;");
    $pdo->exec("PRAGMA mmap_size = 134217728;"); // 128 MB
    $pdo->exec("PRAGMA cache_size = -100000;");   // ~100 MB под кэш страниц

    // --- Схема
    tlog("Создаю таблицы…");
    $pdo->exec("
    CREATE TABLE users (
      id INTEGER PRIMARY KEY,
      email TEXT NOT NULL UNIQUE,
      name TEXT,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE products (
      id INTEGER PRIMARY KEY,
      name TEXT NOT NULL,
      price_cents INTEGER NOT NULL,
      sku TEXT UNIQUE
    );

    CREATE TABLE orders (
      id INTEGER PRIMARY KEY,
      user_id INTEGER NOT NULL,
      total_cents INTEGER NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE order_items (
      order_id   INTEGER NOT NULL,
      product_id INTEGER NOT NULL,
      qty        INTEGER NOT NULL CHECK (qty > 0),
      price_cents INTEGER NOT NULL,           -- цена на момент покупки (снимок)
      PRIMARY KEY (order_id, product_id),
      FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
      FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
    );
  ");

    // --- Параметры объёма данных
    $N_USERS    = 20000 * $scale;     // 20k пользователей x scale
    $N_PRODUCTS = 5000  * $scale;     // 5k товаров x scale
    $N_ORDERS   = 60000 * $scale;     // 60k заказов x scale
    $MAX_ITEMS_PER_ORDER = 5;

    // --- Заполнение users
    tlog("Заполняю users ($N_USERS)…");
    $pdo->beginTransaction();
    $u = $pdo->prepare("INSERT INTO users (id, email, name, created_at) VALUES (?, ?, ?, ?)");
    for ($i=1; $i<=$N_USERS; $i++) {
        $email = "user{$i}@example.com";
        $name  = "User #$i";
        $dt    = date('Y-m-d H:i:s', time() - random_int(0, 86400*365));
        $u->execute([$i, $email, $name, $dt]);
    }
    $pdo->commit();

    // --- Заполнение products
    tlog("Заполняю products ($N_PRODUCTS)…");
    $pdo->beginTransaction();
    $p = $pdo->prepare("INSERT INTO products (id, name, price_cents, sku) VALUES (?, ?, ?, ?)");
    for ($i=1; $i<=$N_PRODUCTS; $i++) {
        $name  = "Product ".str_pad((string)$i, 5, '0', STR_PAD_LEFT);
        $price = random_int(100, 50000);
        $sku   = "SKU-".bin2hex(random_bytes(4))."-".$i;
        $p->execute([$i, $name, $price, $sku]);
    }
    $pdo->commit();

    // --- Кэш цен продуктов в память (ускорение для большого N_ORDERS)
    tlog("Кэширую цены продуктов в память…");
    $priceMap = [];
    $stmt = $pdo->query("SELECT id, price_cents FROM products");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $priceMap[(int)$row[0]] = (int)$row[1];
    }

    // --- Заполнение orders и order_items (исправлено: сначала orders, потом позиции)
    tlog("Заполняю orders ($N_ORDERS) и order_items…");
    $pdo->beginTransaction();

    $oInsert  = $pdo->prepare(
        "INSERT INTO orders (id, user_id, total_cents, created_at) VALUES (?, ?, ?, ?)"
    );
    $oUpdate  = $pdo->prepare(
        "UPDATE orders SET total_cents = ? WHERE id = ?"
    );
    $oiInsert = $pdo->prepare(
        "INSERT INTO order_items (order_id, product_id, qty, price_cents) VALUES (?, ?, ?, ?)"
    );

    for ($i=1; $i<=$N_ORDERS; $i++) {
        $user_id = random_int(1, $N_USERS);
        $created = date('Y-m-d H:i:s', time() - random_int(0, 86400*365));

        // 1) создаём заказ с временной суммой
        $oInsert->execute([$i, $user_id, 0, $created]);

        // 2) генерируем позиции заказа
        $items        = random_int(1, $MAX_ITEMS_PER_ORDER);
        $total_cents  = 0;
        $usedProducts = [];

        for ($k=0; $k<$items; $k++) {
            $pid = random_int(1, $N_PRODUCTS);
            if (isset($usedProducts[$pid])) { $k--; continue; } // первичный ключ (order_id, product_id)
            $usedProducts[$pid] = true;

            $qty   = random_int(1, 3);
            $price = $priceMap[$pid]; // из кэша
            $total_cents += $qty * $price;

            $oiInsert->execute([$i, $pid, $qty, $price]);
        }

        // 3) обновляем итог заказа
        $oUpdate->execute([$total_cents, $i]);

        // Немного визуальной обратной связи на больших объёмах
        if ($i % 20000 === 0) { tlog("…создано заказов: $i"); }
    }
    $pdo->commit();

    // --- Функция бенчмарка
    function bench(PDO $pdo, $label, $sql, $params = []) {
        $t0 = microtime(true);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) { $count++; if ($count>=50) break; }
        $dt = (microtime(true)-$t0);
        tlog(sprintf("%s: %.3f s (первые %d строк)", $label, $dt, $count));
    }

    // --- Бенчмарк без индексов
    tlog("Бенчмарк без индексов…");
    bench($pdo, "Поиск пользователя по email (LIKE)",
        "SELECT id,email FROM users WHERE email LIKE ? LIMIT 50", ["user1999%@example.com"]);
    bench($pdo, "Заказы пользователя (JOIN)",
        "SELECT o.id, o.created_at, o.total_cents
       FROM orders o JOIN users u ON u.id = o.user_id
      WHERE u.email = ? ORDER BY o.created_at DESC LIMIT 50", ["user1000@example.com"]);
    bench($pdo, "Поиск товаров по name (prefix)",
        "SELECT id,name FROM products WHERE name LIKE ? LIMIT 50", ["Product 000%"]);
    bench($pdo, "Топ товаров по числу продаж (JOIN+GROUP)",
        "SELECT p.id, p.name, SUM(oi.qty) qty
       FROM order_items oi JOIN products p ON p.id = oi.product_id
      GROUP BY p.id ORDER BY qty DESC LIMIT 50");

    // --- Создание индексов
    tlog("Создаю индексы…");
    $pdo->exec("
    CREATE INDEX idx_users_email            ON users(email);
    CREATE INDEX idx_orders_user_created    ON orders(user_id, created_at);
    CREATE INDEX idx_products_name          ON products(name);
    CREATE INDEX idx_order_items_product    ON order_items(product_id);
  ");
    $pdo->exec("ANALYZE;");

    // --- Бенчмарк после индексов
    tlog("Бенчмарк с индексами…");
    bench($pdo, "Поиск пользователя по email (LIKE)",
        "SELECT id,email FROM users WHERE email LIKE ? LIMIT 50", ["user1999%@example.com"]);
    bench($pdo, "Заказы пользователя (JOIN)",
        "SELECT o.id, o.created_at, o.total_cents
       FROM orders o JOIN users u ON u.id = o.user_id
      WHERE u.email = ? ORDER BY o.created_at DESC LIMIT 50", ["user1000@example.com"]);
    bench($pdo, "Поиск товаров по name (prefix)",
        "SELECT id,name FROM products WHERE name LIKE ? LIMIT 50", ["Product 000%"]);
    bench($pdo, "Топ товаров по числу продаж (JOIN+GROUP)",
        "SELECT p.id, p.name, SUM(oi.qty) qty
       FROM order_items oi JOIN products p ON p.id = oi.product_id
      GROUP BY p.id ORDER BY qty DESC LIMIT 50");

    // --- Планы выполнения
    tlog("EXPLAIN QUERY PLAN (после индексов):");
    foreach ([
                 "SELECT id,email FROM users WHERE email = 'user500@example.com' LIMIT 1",
                 "SELECT o.* FROM orders o WHERE o.user_id = 123 ORDER BY created_at DESC LIMIT 10",
                 "SELECT * FROM products WHERE name LIKE 'Product 001%';"
             ] as $q) {
        $plan = $pdo->query("EXPLAIN QUERY PLAN $q")->fetchAll(PDO::FETCH_NUM);
        echo "\n$q\n";
        foreach ($plan as $row) {
            echo "  - ".implode(' | ', $row)."\n";
        }
    }

    tlog("Готово. Файл БД: $path");

} catch (Throwable $e) {
    fwrite(STDERR, "Ошибка: ".$e->getMessage()."\n");
    exit(1);
}
