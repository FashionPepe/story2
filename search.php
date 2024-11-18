<?php
include 'connect.php';
    function search($mysqli){
       

        $search = $_GET['search'];

// Разделяем строку на массив
    $nameParts = preg_split('/\s+/', $search);


        // Дополняем массив до трех элементов
        $nameParts = array_pad($nameParts, 3, "NULL");

        // Присваиваем значения переменным
        $surname = $nameParts[0];
        $name = $nameParts[1];
        $additional_name = $nameParts[2];
        
      
    
        
        // Инициализация переменной для результата
        $personResult = null;
        
        // Проверяем количество непустых параметров
        $paramCount = 0;
        if ($surname != "NULL") $paramCount++;
        if ($name != "NULL") $paramCount++;
        if ($additional_name != "NULL") $paramCount++;
        
        // Формируем запрос в зависимости от количества параметров
        if ($paramCount == 1) {
            // Поиск по одному параметру
            if (!empty($surname)) {
                $personResult = $mysqli->query("SELECT DISTINCT * FROM humans WHERE surname = '$surname' OR name = '$surname' OR additional_name = '$surname'");
            } elseif (!empty($name)) {
                $personResult = $mysqli->query("SELECT DISTINCT * FROM humans WHERE surname = '$name' OR name = '$name' OR additional_name = '$name'");
            } elseif (!empty($additional_name)) {
                $personResult = $mysqli->query("SELECT DISTINCT * FROM humans WHERE surname = '$additional_name' OR name = '$additional_name' OR additional_name = '$additional_name'");
            }
        } elseif ($paramCount == 2) {
            // Поиск по двум параметрам
            if (!empty($surname) && !empty($name)) {
                $personResult = $mysqli->query("SELECT DISTINCT * FROM humans WHERE (surname = '$surname' OR name = '$surname' OR additional_name = '$surname') AND (surname = '$name' OR name = '$name' OR additional_name = '$name')");
            } elseif (!empty($surname) && !empty($additional_name)) {
                $personResult = $mysqli->query("SELECT DISTINCT * FROM humans WHERE (surname = '$surname' OR name = '$surname' OR additional_name = '$surname') AND (surname = '$additional_name' OR name = '$additional_name' OR additional_name = '$additional_name')");
            } elseif (!empty($name) && !empty($additional_name)) {
                $personResult = $mysqli->query("SELECT DISTINCT * FROM humans WHERE (surname = '$name' OR name = '$name' OR additional_name = '$name') AND (surname = '$additional_name' OR name = '$additional_name' OR additional_name = '$additional_name')");
            }
        } elseif ($paramCount == 3) {
            // Поиск по трем параметрам
            $personResult = $mysqli->query("SELECT DISTINCT * FROM humans WHERE (surname = '$surname' OR name = '$surname' OR additional_name = '$surname') AND (surname = '$name' OR name = '$name' OR additional_name = '$name') AND (surname = '$additional_name' OR name = '$additional_name' OR additional_name = '$additional_name')");
        }
        
        
        
        




        echo "<div class='generation'>";
        while ($person = $personResult->fetch_assoc()) {
            displayPersonBrief($person);
        }
        echo "</div>";
    }

    function displayPersonBrief($person) {
        echo "<div class='person'>";
        echo "<a href='index.php?id=" . $person['id'] . "'><img src='" . htmlspecialchars($person['photo']) . "' alt='" . htmlspecialchars($person['name']) . "' class='photo'></a>";
        echo "<div class='name'>" . htmlspecialchars($person['name']) . "</div>";
        echo "<a href='person.php?id=" . $person['id'] . "' class='details-btn'>Подробнее</a>";
        echo "</div>";
    }

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Родословное Древо</title>
    <link rel="stylesheet" href="./index.css">
</head>
<body>
<header>
        <a class="index-btn" href="index.php">
            <p>Родословное дерево</p>
        </a>
        <form method="get" action="search.php" class="search_warper">
            <input type="text" name="search" id="search" class="search">
            
            <button type="submit" class="search-btn"></button>
        </form>
        
        <?php if (!isset($_SESSION['loggedin'])) { ?>
            <a href="login.php" class="login-btn">Авторизация редактора</a>
        <?php } else { ?>
            <div class="btns-crator">
                <a href="logout.php">Выход</a>
                <a href="create_person.php">Создать человека</a>
            </div>
        <?php } ?>
    </header>
    <main>
        <?php search($mysqli) ?>
    </main>
    <footer>
        <p>Создатель сайта Дима Долгов. Спасибо за сбор информации Пищеву Сергею Георгиевичу</p>
    </footer>
</body>
</html>