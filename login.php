<?php
session_start();
include('connect.php'); // Подключение к базе данных

if (isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            echo "Неверный пароль!";
        }
    } else {
        echo "Пользователь не найден!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="./person.css">
</head>
<body>
    <header>
        <a class="index-btn" href="index.php"><p>Родословное дерево</p></a>
        
    </header>

    <main class="tree-container">
        
                <hr>
                <h2>Авторизация</h2>
                <form action="login.php" method="POST" enctype="multipart/form-data" class="edit-form">
                    <label for="username">Имя пользователя:<input type="text" id="username" name="username" required></label>
                    
                    <br>
                    <label for="password">Пароль:<input type="password" id="password" name="password" required></label>
                    
                    <br>
                    <button type="submit">Войти</button>
                    <br>
                   
                </form>
            
    </main>

    <footer>
        <p>Создатель сайта Дима Долгов. Спасибо за сбор информации Пищеву Сергею Георгиевичу</p>
    </footer>

</body>
</html>