<?php

// Параметры подключения к базе данных
$host = "localhost";  // или IP-адрес вашего сервера MySQL
$username = "your_username";
$password = "your_password";
$database = "your_database";

// Устанавливаем соединение с базой данных
$conn = new mysqli($host, $username, $password, $database);

// Проверяем соединение
if ($conn->connect_error) {
	die("Ошибка подключения: " . $conn->connect_error);
}

// Функция для загрузки данных из API и вставки в БД
function loadAndInsertData($conn, $apiEndpoint, $table, $columns)
{
	$json = file_get_contents($apiEndpoint);
	if ($json === false) {
		die("Ошибка при получении данных из API: " . $apiEndpoint);
	}

	$data = json_decode($json, true);
	if ($data === null) {
		die("Ошибка при декодировании JSON: " . $apiEndpoint);
	}

	$count = 0;
	foreach ($data as $row) {
		$values = [];
		foreach ($columns as $col) {
			// Экранируем значения для безопасной вставки в SQL-запрос
			$values[] = "'" . $conn->real_escape_string($row[$col]) . "'";
		}

		$sql = "INSERT INTO $table (" . implode(",", $columns) . ") VALUES (" . implode(",", $values) . ")";

		if ($conn->query($sql) === TRUE) {
			$count++;
		} else {
			echo "Ошибка при вставке данных: " . $sql . "<br>" . $conn->error . "<br>";
		}
	}
	return $count;
}

// Загрузка данных из API и вставка в таблицу posts
$postsCount = loadAndInsertData($conn, "https://jsonplaceholder.typicode.com/posts", "posts", ["id", "userId", "title", "body"]);

// Загрузка данных из API и вставка в таблицу comments
$commentsCount = loadAndInsertData($conn, "https://jsonplaceholder.typicode.com/comments", "comments", ["postId", "id", "name", "email", "body"]);

// Вывод сообщения в консоль
echo "Загружено " . $postsCount . " записей и " . $commentsCount . " комментариев\n";

// Закрываем соединение с базой данных
$conn->close();
