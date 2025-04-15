<?php
//Параметры подключения к базе данных
$host = 'localhost';          //Адрес сервера базы данных
$user = 'root';      //Имя пользователя
$password = '';  //Пароль пользователя
$database = 'bushin_205s1';  //Имя базы данных


//Создаем соединение
$mysqli = new mysqli($host, $user, $password, $database);


//Проверяем соединение
if ($mysqli->connect_error) {
            die('Ошибка подключения (' . $mysqli->connect_errno . ') '
                . $mysqli->connect_error);
}
?>
