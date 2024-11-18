<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Родословное Древо</title>
    <link rel="stylesheet" href="./person.css">
</head>
<body>
    <header>
        <a class="index-btn" href="index.php"><p>Родословное дерево</p></a>
        <?php
        
        if (!isset($_SESSION['loggedin'])) {
            echo "<a href='login.php' class='login-btn'>Авторизация редактора</a>";
        } else {
            echo "<div class='btns-crator'>";
            echo "<a href='logout.php'>Выход</a>";
            echo "<a href='create_person.php'>Создать человека</a>";
            echo "</div>";
        }
        ?>
    </header>

    <main class="tree-container">
        <?php 
        include 'connect.php';

        if (isset($_SESSION['loggedin'])): 
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            if ($id > 0){
                $result = $mysqli->query("SELECT * FROM humans WHERE id = $id");
                $person = $result->fetch_assoc();
            }
            $birthDate = sprintf('%04d-%02d-%02d', $person['year_birth'], $person['month_birth'], $person['day_birth']);
            $deathDate = sprintf('%04d-%02d-%02d', $person['year_death'], $person['month_death'], $person['day_seath']);

            if ($person): ?>
            
            

                <hr>
                <h2>Редактировать информацию</h2>
                <form action="update_person.php" method="POST" enctype="multipart/form-data" class="edit-form">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($person['id']); ?>">
                    <label>Имя: <input type="text" name="name" value="<?= htmlspecialchars($person['name']); ?>"></label><br>
                    <label>Фамилия: <input type="text" name="surname" value="<?= htmlspecialchars($person['surname']); ?>"></label><br>
                    <label>Отчество: <input type="text" name="additional_name" value="<?= htmlspecialchars($person['additional_name']); ?>"></label><br>
                    <label>Дата рождения: <input type="date" name="birth_date" value="<?= $birthDate ;?>"></label><br>
                    <label>Дата смерти: <input type="date" name="death_date" value="<?= $deathDate ;?>"></label><br>
                    <label>Описание: <textarea name="description"><?= htmlspecialchars($person['description']); ?></textarea></label><br>
                    <div class="file-upload-wrapper">
                        <label class="file-upload-button" for="file-upload-input">Выберите файл</label>
                        <input type="file" id="file-upload-input" name="photo" class="file-upload-input" onchange="displayFileName(this)">
                        <span class="file-upload-filename" id="file-upload-filename">Файл не выбран</span>
                    </div>
                    <br>
                    <button type="submit" class="btn-save">Сохранить изменения</button>
                </form>
            <?php endif; ?>

            <hr>
            <h3>Родственные связи</h3>
            <div class="relationships">
                <?php
                $relationships = $mysqli->query("SELECT r.*, h1.name AS person1_name, h2.name AS person2_name 
                                                 FROM relationships r
                                                 JOIN humans h1 ON r.person1_id = h1.id
                                                 JOIN humans h2 ON r.person2_id = h2.id
                                                 WHERE (r.person1_id = $id OR r.person2_id = $id)");

                while ($relationship = $relationships->fetch_assoc()) {
                   $person1_id = $relationship['person1_id'];
                    $person2_id = $relationship['person2_id'];
                    $relationship_type = $relationship['relationship_type'];
                    
                    // Определяем имя для отображения
                    $relation_name = $person1_id == $id ? $relationship['person2_name'] : $relationship['person1_name'];
                    $relation_type = "";
                    
                    // Определяем тип связи
                    if ($relationship_type == 'child') {
                        if($person1_id == $id){
                            $relation_type = "Ребенок";
                        }
                        else{
                            $relation_type = "Родитель";  
                        }
                    }elseif ($relationship_type == 'spouse') {
                        $relation_type = "Супруг(а)";
                    }

                    echo "<div class='relationship-item'>";
                    echo "<p><strong>{$relation_name}</strong> - {$relation_type}</p>";
                    echo "<form action='delete_relationship.php' method='POST' class='delete-form'>";
                    echo "<input type='hidden' name='person1_id' value='{$person1_id}'>";
                    echo "<input type='hidden' name='person2_id' value='{$person2_id}'>";
                    echo "<input type='hidden' name='id' value='{$_GET['id']}'>";
                    echo "<input type='hidden' name='relationship_type' value='{$relationship_type}'>";
                    echo "<button type='submit' class='delete-btn'>Удалить связь</button>";
                    echo "</form>";
                    echo "</div>";
                }
                ?>
            </div>

            <hr>
            <h3>Добавить родственную связь</h3>
            <form action="add_relationship.php" method="POST" class="add-relationship-form">
                <input type="hidden" name="person1_id" value="<?php echo $id; ?>">

                <label for="person2_id">Выберите человека:</label>
                <select name="person2_id" required class="form-select">
                    <option value="">Выберите...</option>
                    <?php
                    $other_people = $mysqli->query("SELECT * FROM humans WHERE id != $id");
                    while ($person_option = $other_people->fetch_assoc()) {
                        echo "<option value='{$person_option['id']}'>{$person_option['name']} {$person_option['surname']}</option>";
                    }
                    ?>
                </select><br>

                <label for="relationship_type">Тип связи:</label>
                <select name="relationship_type" required class="form-select">
                    <option value="parent">Родитель</option>
                    <option value="child">Ребёнок</option>
                    <option value="spouse">Супруг(а)</option>
                </select><br>

                <button type="submit" class="btn-save">Добавить связь</button>
            </form>

        <?php endif;
        if(!isset($_SESSION['loggedin'])){
            
// Подключаем файл с подключением к базе данных


// Получаем ID из массива GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    

    $person = [];
    $result = $mysqli->query("SELECT * FROM humans WHERE id = $id");
    while ($row = $result->fetch_assoc()) {
        $person[] = $row;
    }
    

    if ($person[0]) {
        echo "<div class='person-info'>";
        // Отображение фото, если оно указано
        if (!empty($person[0]['photo'])) {
            echo "<p><img src='{$person[0]['photo']}' alt='Фото {$person[0]['name']}' ></p>";
        }
        echo "<div class='person-info-content'>";
        echo "<h1>Информация о человеке</h1>";
        echo "<p><strong>Имя:</strong> {$person[0]['name']}</p>";
        echo "<p><strong>Фамилия:</strong> {$person[0]['surname']}</p>";
        echo "<p><strong>Отчество:</strong> {$person[0]['additional_name']}</p>";

        // Форматирование даты рождения
        $birthDate = "{$person[0]['day_birth']}.{$person[0]['month_birth']}.{$person[0]['year_birth']}";
        echo "<p><strong>Дата рождения:</strong> $birthDate</p>";

        // Форматирование даты смерти, если она существует
        if (!empty($person[0]['day_seath']) && !empty($person[0]['month_death']) && !empty($person[0]['year_death'])) {
            $deathDate = "{$person[0]['day_seath']}.{$person[0]['month_death']}.{$person[0]['year_death']}";
            echo "<p><strong>Дата смерти:</strong> $deathDate</p>";
        }

        echo "<p><strong>Описание:</strong> {$person[0]['description']}</p>";

        
    } else {
        echo "<p>Человек с таким ID не найден.</p>";
    }
} else {
    echo "<p>Некорректный ID.</p>";
}
        }
        ?>
    </main>

    <footer>
        <p>Создатель сайта Дима Долгов. Спасибо за сбор информации Пищеву Сергею Георгиевичу</p>
    </footer>

    <script>
        function displayFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : "Файл не выбран";
            document.getElementById('file-upload-filename').textContent = fileName;
        }
    </script>
</body>
</html>