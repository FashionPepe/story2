<?php
$host = 'c9128423.beget.tech'; // Адрес сервера
$user = 'c9128423_story';      // Имя пользователя
$password = 'epitih34!';      // Пароль
$dbname = 'c9128423_story'; // Имя базы данных

// Создание подключения
$mysqli = new mysqli($host, $user, $password, $dbname);

// Проверка подключения
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Установка кодировки
$mysqli->set_charset('utf8');
?>