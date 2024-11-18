<?php
include 'connect.php';
session_start();
function getRootGeneration($mysqli) {
    $result = $mysqli->query("SELECT * FROM humans");
    if ($result->num_rows > 0) {
        $persons = [];
        while ($row = $result->fetch_assoc()) {
            $persons[$row['id']] = $row;
        }

        $relationships = [];
        $result = $mysqli->query("SELECT * FROM relationships");
        while ($row = $result->fetch_assoc()) {
            $relationships[] = $row;
        }

        $children = [];
        foreach ($relationships as $relation) {
            if ($relation['relationship_type'] === 'child') {
                $children[$relation['person2_id']][] = $relation['person1_id'];
            }
        }
        
        
        
        

        // Define root generation
        $rootGeneration = [];
        foreach ($persons as $id => $person) {
            if (!isset($children[$id])) {
                $res = $mysqli->query("SELECT if(person1_id = '$id', person2_id, person1_id) as id FROM relationships WHERE (person2_id = '$id' AND person1_id IN (SELECT person2_id FROM relationships WHERE relationship_type = 'child')) OR (person1_id = '$id' AND person2_id IN (SELECT person2_id FROM relationships WHERE relationship_type = 'child')) AND relationship_type = 'spouse'");
                $sp = [];
                
                while ($row = $res->fetch_assoc()) {
                    $sp[$row['id']] = $row;
                    
                }
                if(empty($sp)){
                    
                    $rootGeneration[] = $id;
                }
            }
        }

        echo "<div class='tree-container'>";
        echo "<div class='generation' data-level='root'>";
        foreach ($rootGeneration as $personId) {
            displayPerson($personId, $persons);
        }
        echo "</div>";
        echo "</div>";
    } else {
        echo "Нет данных о персонах.";
    }
}
function getPersonDetails($mysqli, $personId) {
    $personResult = $mysqli->query("SELECT * FROM humans WHERE id = $personId");
    $person = $personResult->fetch_assoc();
    
    echo "<h3>Человек</h3>";
    echo "</div>";
    if (!$person) {
        echo "Информация о человеке недоступна.";
        return;
    }

    echo "<div class='generation'>";
    displayPersonBrief($person);
    echo "</div>";
    // Получаем супругов
    $spousesResult = $mysqli->query("SELECT h.* FROM relationships r JOIN humans h ON (h.id = r.person1_id AND r.person2_id = $personId) WHERE r.relationship_type = 'child';");
    echo "<h3>Родители:</h3>";
    echo "<div class='generation'>";
    while ($spouse = $spousesResult->fetch_assoc()) {
        displayPersonBrief($spouse);
    }
    echo "</div>";
    // Получаем супругов
    $spousesResult = $mysqli->query("SELECT h.* FROM relationships r JOIN humans h ON (h.id = r.person1_id AND r.person2_id = $personId) OR (h.id = r.person2_id AND r.person1_id = $personId) WHERE r.relationship_type = 'spouse';");
    echo "<h3>Супруги:</h3>";
    echo "<div class='generation'>";
    while ($spouse = $spousesResult->fetch_assoc()) {
        displayPersonBrief($spouse);
    }
    echo "</div>";

    // Получаем детей
    $childrenResult = $mysqli->query("SELECT h.* FROM relationships r JOIN humans h ON h.id = r.person2_id WHERE r.person1_id = $personId AND r.relationship_type = 'child'");
    echo "<h3>Дети:</h3>";
    echo "<div class='generation'>";
    while ($child = $childrenResult->fetch_assoc()) {
        displayPersonBrief($child);
    }
    echo "</div>";
}

function displayPerson($personId, $persons) {
    echo "<div class='person'>";
    echo "<a href='index.php?id={$personId}'><img src='" . htmlspecialchars($persons[$personId]['photo']) . "' alt='" . htmlspecialchars($persons[$personId]['name']) . "' class='photo'></a>";
    echo "<div class='name'>" . htmlspecialchars($persons[$personId]['name']) . "</div>";
    echo "<a href='person.php?id={$personId}' class='details-btn'>Подробнее</a>";
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

<!DOCTYPE html>
<html lang="ru">

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
        <?php
        if (isset($_GET['id'])) {
            $personId = (int)$_GET['id'];
            getPersonDetails($mysqli, $personId);
        } else {
            getRootGeneration($mysqli);
        }
        ?>
    </main>

    <footer>
        <p>Создатель сайта Дима Долгов. Спасибо за сбор информации Пищеву Сергею Георгиевичу</p>
    </footer>
</body>

</html>
