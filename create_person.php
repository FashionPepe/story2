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
        
                <hr>
                <h2>Добавить человека</h2>
                <form action="save_person.php" method="POST" enctype="multipart/form-data" class="edit-form">
                    <input type="hidden" name="id">
                    <label>Имя: <input type="text" name="name" ></label><br>
                    <label>Фамилия: <input type="text" name="surname" ></label><br>
                    <label>Отчество: <input type="text" name="additional_name"></label><br>
                    <label>Дата рождения: <input type="date" name="birth_date"></label><br>
                    <label>Описание: <textarea name="description"></textarea></label><br>
                    <div class="file-upload-wrapper">
                        <label class="file-upload-button" for="file-upload-input">Выберите файл</label>
                        <input type="file" id="file-upload-input" name="photo" class="file-upload-input" onchange="displayFileName(this)">
                        <span class="file-upload-filename" id="file-upload-filename">Файл не выбран</span>
                    </div>
                    <br>
                    <button type="submit" class="btn-save">Сохранить изменения</button>
                </form>
            
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