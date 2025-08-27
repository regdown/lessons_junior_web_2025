#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Применяет миграции из ./migrations по порядку.
Каждая миграция — отдельный .py-файл вида 001_*.py, 002_*.py, ...

Журнал применённых миграций хранится в таблице schema_migrations в ../database.db
(или поменяйте путь db_path ниже под ваш).
"""

import re
import sys
import sqlite3
import subprocess
from pathlib import Path
from typing import List, Set, Tuple

# ---- пути ----
BASE_DIR = Path(__file__).resolve().parent
MIGR_DIR = BASE_DIR / "migrations"
DB_PATH = (BASE_DIR.parent / "database.db").resolve()   # ../database.db

# ---- утилиты ----
MIGRATION_RE = re.compile(r"^(\d+)_.*\.py$")

def list_migration_files() -> List[Path]:
    """Вернуть список файлов-меграций, отсортированный по префиксу-номеру."""
    files = []
    for p in MIGR_DIR.glob("*.py"):
        m = MIGRATION_RE.match(p.name)
        if m:
            num = int(m.group(1))
            files.append((num, p.name, p))
    files.sort(key=lambda t: (t[0], t[1]))
    return [p for _, _, p in files]

def ensure_log_table(conn: sqlite3.Connection) -> None:
    conn.execute("""
        CREATE TABLE IF NOT EXISTS schema_migrations (
            name TEXT PRIMARY KEY,
            applied_at TEXT DEFAULT CURRENT_TIMESTAMP
        )
    """)
    conn.commit()

def fetch_applied(conn: sqlite3.Connection) -> Set[str]:
    rows = conn.execute("SELECT name FROM schema_migrations").fetchall()
    return {r[0] for r in rows}

def mark_applied(conn: sqlite3.Connection, name: str) -> None:
    conn.execute("INSERT INTO schema_migrations(name) VALUES (?)", (name,))
    conn.commit()

def run_migration(script_path: Path) -> Tuple[int, str, str]:
    """
    Запустить миграцию отдельным процессом Python.
    cwd = MIGR_DIR, чтобы относительные пути вида '../database.db' работали.
    Возвращает (returncode, stdout, stderr).
    """
    proc = subprocess.Popen(
        [sys.executable, str(script_path.name)],
        cwd=str(MIGR_DIR),
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True,
    )
    out, err = proc.communicate()
    return proc.returncode, out, err

def main() -> int:
    if not MIGR_DIR.is_dir():
        print(f"[ERR] Нет папки миграций: {MIGR_DIR}")
        return 1

    print(f"[INFO] База: {DB_PATH}")
    print(f"[INFO] Папка миграций: {MIGR_DIR}")

    # Подключимся к БД (создастся файл, если его нет)
    conn = sqlite3.connect(str(DB_PATH))
    try:
        ensure_log_table(conn)
        applied = fetch_applied(conn)

        all_files = list_migration_files()
        if not all_files:
            print("[INFO] Миграций не найдено.")
            return 0

        pending = [p for p in all_files if p.name not in applied]
        if not pending:
            print("[OK] Все миграции уже применены.")
            return 0

        print("[INFO] К применению:")
        for p in pending:
            print("  -", p.name)

        for script in pending:
            print(f"[RUN] {script.name} ...")
            code, out, err = run_migration(script)
            if out.strip():
                print(out.strip())
            if code != 0:
                print(f"[FAIL] {script.name} завершилась с ошибкой (код {code}).")
                if err.strip():
                    print(err.strip())
                return code

            # зафиксируем успешную миграцию
            mark_applied(conn, script.name)
            print(f"[OK] {script.name} применена.")

        print("[DONE] Все новые миграции применены.")
        return 0
    finally:
        conn.close()

if __name__ == "__main__":
    sys.exit(main())
