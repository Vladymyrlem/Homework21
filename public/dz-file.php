<?php
$dsn = 'mysql:dbname=homestead;host=localhost';
$username = 'homestead';
$password = 'secret';
$mysql = new PDO($dsn, $username, $password);

// 1.
try {
    //  Створити транзакцію, в транзакції повинні бути sql запити
    $mysql->beginTransaction();

//a. insert в таблицю user створення юзера

//b. отримання id нового юзера

//c. далі привязуємо юзеру рандомний проект, тобто робимо insert в таблицю project_user

//d. після цього створюємо лейб і прив'язуємо до нього юзера

} catch (PDOException $exception) {
    $mysql->rollBack();
    echo $exception->getMessage();
}

