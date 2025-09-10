# -*- coding: utf-8 -*-
import sqlite3
from pathlib import Path

DB_PATH = (Path(__file__).resolve().parent.parent / "database.db")

conn = sqlite3.connect(str(DB_PATH))
conn.execute('PRAGMA foreign_keys = ON;')
cur = conn.cursor()

def table_exists(name: str) -> bool:
    row = cur.execute(
        "SELECT 1 FROM sqlite_master WHERE type='table' AND name=?",
        (name,)
    ).fetchone()
    return row is not None

def column_exists(table: str, column: str) -> bool:
    if not table_exists(table):
        return False
    cols = cur.execute(f"PRAGMA table_info({table})").fetchall()
    return any(c[1] == column for c in cols)  # c[1] -> name

def create_index_safe(sql: str, table: str) -> None:
    # Создаём индекс только если таблица существует
    if table_exists(table):
        cur.execute(sql)

try:
    # --- Каталог ---
    if table_exists('products'):
        if column_exists('products', 'category_id'):
            create_index_safe(
                "CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id)",
                'products'
            )
        if column_exists('products', 'brand_id'):
            create_index_safe(
                "CREATE INDEX IF NOT EXISTS idx_products_brand ON products(brand_id)",
                'products'
            )

    if table_exists('categories') and column_exists('categories', 'name'):
        create_index_safe(
            "CREATE INDEX IF NOT EXISTS idx_categories_name ON categories(name)",
            'categories'
        )

    if table_exists('brands') and column_exists('brands', 'name'):
        create_index_safe(
            "CREATE INDEX IF NOT EXISTS idx_brands_name ON brands(name)",
            'brands'
        )

    # --- Пользователи/заказы ---
    if table_exists('addresses'):
        create_index_safe(
            "CREATE INDEX IF NOT EXISTS idx_addresses_user ON addresses(user_id, is_default)",
            'addresses'
        )

    if table_exists('orders'):
        create_index_safe(
            "CREATE INDEX IF NOT EXISTS idx_orders_user ON orders(user_id)",
            'orders'
        )
        create_index_safe(
            "CREATE INDEX IF NOT EXISTS idx_orders_statuses ON orders(order_status, payment_status)",
            'orders'
        )
        if column_exists('orders', 'address_id'):
            create_index_safe(
                "CREATE INDEX IF NOT EXISTS idx_orders_address ON orders(address_id)",
                'orders'
            )

    if table_exists('order_items'):
        create_index_safe(
            "CREATE INDEX IF NOT EXISTS idx_order_items_order ON order_items(order_id)",
            'order_items'
        )
        create_index_safe(
            "CREATE INDEX IF NOT EXISTS idx_order_items_product ON order_items(product_id)",
            'order_items'
        )

    conn.commit()
    print("Migration 004 applied: indexes created where tables/columns exist.")
except Exception as e:
    conn.rollback()
    raise
finally:
    conn.close()
