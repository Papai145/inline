<!DOCTYPE html>
<html>

<head>
  <title>Поиск записей по комментариям</title>
</head>

<body>
  <h1>Поиск записей по тексту комментария</h1>
  <form method="GET" action="search.php">
    <input type="text" name="search_text" placeholder="Введите текст для поиска (минимум 3 символа)" value="<?php echo isset($_GET['search_text']) ? htmlspecialchars($_GET['search_text']) : ''; ?>">
    <button type="submit">Найти</button>
  </form>

  <?php
  // Параметры подключения к базе данных (замените на ваши)
  $host = "localhost";
  $username = "your_username";
  $password = "your_password";
  $database = "your_database";

  // Устанавливаем соединение с базой данных
  $conn = new mysqli($host, $username, $password, $database);

  // Проверяем соединение
  if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
  }

  // Выполняем поиск, если текст введен
  if (isset($_GET['search_text']) && strlen($_GET['search_text']) >= 3) {
    $searchText = $conn->real_escape_string($_GET['search_text']); // Экранируем для безопасности

    $sql = "SELECT
                    posts.id AS post_id,
                    posts.title AS post_title,
                    comments.body AS comment_body
                FROM
                    posts
                INNER JOIN
                    comments ON posts.id = comments.postId
                WHERE
                    comments.body LIKE '%$searchText%'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      echo "<h2>Результаты поиска:</h2>";
      while ($row = $result->fetch_assoc()) {
        echo "<p><strong>Запись #" . htmlspecialchars($row["post_id"]) . ": " . htmlspecialchars($row["post_title"]) . "</strong><br>";
        echo "Комментарий: " . highlightSearchTerm(htmlspecialchars($row["comment_body"]), htmlspecialchars($searchText)) . "</p>";
      }
    } else {
      echo "<p>Ничего не найдено.</p>";
    }
  } elseif (isset($_GET['search_text']) && strlen($_GET['search_text']) < 3) {
    echo "<p>Введите минимум 3 символа для поиска.</p>";
  }

  // Функция для выделения поискового запроса в тексте
  function highlightSearchTerm($text, $searchTerm)
  {
    return str_replace($searchTerm, "<span style='background-color:yellow;'>$searchTerm</span>", $text);
  }

  // Закрываем соединение с базой данных
  $conn->close();
  ?>
</body>

</html>