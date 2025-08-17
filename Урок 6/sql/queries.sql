-- Создать таблицу пользователей
CREATE TABLE Users (
                       id INTEGER PRIMARY KEY AUTOINCREMENT,
                       name TEXT,
                       email TEXT,
                       age INTEGER
);

-- Вставить пользователя
INSERT INTO Users (name, email, age) VALUES ('Alice', 'alice@example.com', 25);

-- Получить всех пользователей старше 18
SELECT name, age FROM Users WHERE age >= 18;

-- Обновить данные
UPDATE Users SET age = 30 WHERE name = 'Alice';

-- Удалить пользователя
DELETE FROM Users WHERE name = 'Alice';
