# -*- coding: utf-8 -*-
import sqlite3

conn = sqlite3.connect('../database.db')
conn.execute('PRAGMA foreign_keys = ON;')
cur = conn.cursor()

# --- USERS ---
cur.executescript("""
CREATE TABLE IF NOT EXISTS users (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    name         TEXT,
    surname      TEXT,
    patronymic   TEXT,
    age          INTEGER,
    email        TEXT UNIQUE,                     -- уникальный e-mail
    gender       TEXT CHECK (gender IN ('male','female','other') OR gender IS NULL),
    phone        TEXT,
    consent      INTEGER NOT NULL DEFAULT 0,      -- согласие на обработку персональных данных (0/1)
    created_at   TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Адреса пользователя (много адресов на одного пользователя)
CREATE TABLE IF NOT EXISTS addresses (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL,
    address    TEXT    NOT NULL,                  -- свободная текстовая форма адреса (как на вашей схеме)
    is_default INTEGER NOT NULL DEFAULT 0,        -- 0/1
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Справочники статусов/способов оплаты (простые "enum"-таблицы)
CREATE TABLE IF NOT EXISTS order_statuses (
    code TEXT PRIMARY KEY, -- new, processing, done, cancelled
    name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS payment_statuses (
    code TEXT PRIMARY KEY, -- pending, paid, failed, refund
    name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS payment_methods (
    code TEXT PRIMARY KEY, -- cash, card, online
    name TEXT NOT NULL
);

-- Заказы
CREATE TABLE IF NOT EXISTS orders (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id         INTEGER NOT NULL,
    address_id      INTEGER,                      -- куда доставлять (опционально)
    payment_method  TEXT    NOT NULL,             -- FK -> payment_methods.code
    order_status    TEXT    NOT NULL,             -- FK -> order_statuses.code
    payment_status  TEXT    NOT NULL,             -- FK -> payment_statuses.code
    total           REAL    NOT NULL DEFAULT 0,
    created_at      TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)        REFERENCES users(id),
    FOREIGN KEY (address_id)     REFERENCES addresses(id),
    FOREIGN KEY (payment_method) REFERENCES payment_methods(code),
    FOREIGN KEY (order_status)   REFERENCES order_statuses(code),
    FOREIGN KEY (payment_status) REFERENCES payment_statuses(code)
);

-- Позиции заказа
CREATE TABLE IF NOT EXISTS order_items (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id   INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    price      REAL    NOT NULL,
    qty        INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
""")

# Наполним справочники начальными значениями
cur.executemany("INSERT OR IGNORE INTO order_statuses(code, name) VALUES (?,?)", [
    ('new',        'Новый'),
    ('processing', 'В обработке'),
    ('done',       'Завершён'),
    ('cancelled',  'Отменён'),
])

cur.executemany("INSERT OR IGNORE INTO payment_statuses(code, name) VALUES (?,?)", [
    ('pending', 'Ожидает оплаты'),
    ('paid',    'Оплачен'),
    ('failed',  'Ошибка оплаты'),
    ('refund',  'Возврат'),
])

cur.executemany("INSERT OR IGNORE INTO payment_methods(code, name) VALUES (?,?)", [
    ('cash',   'Наличные'),
    ('card',   'Карта курьеру'),
    ('online', 'Онлайн-оплата'),
])

conn.commit()
conn.close()
print("Migration 003 applied: users, addresses, orders, order_items, dictionaries.")
