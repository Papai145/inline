-- create_db.sql

-- Создание таблицы posts
CREATE TABLE posts (
    id INT PRIMARY KEY,
    userId INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL
);

-- Создание таблицы comments
CREATE TABLE comments (
    postId INT NOT NULL,
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    FOREIGN KEY (postId) REFERENCES posts(id) ON DELETE CASCADE
);


