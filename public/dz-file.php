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
    $query = 'INSERT INTO users (name, email, token, country_id) VALUES (:name, :email, :token, :country_id)';
    $sth = $mysql->prepare($query);

    $name = 'Albert Dunio';
    $email = 'albert37y9@gmail.com' . rand(0, 9999);
    $token = '7|ysBJMCCykwmo1O60FMOOlx7eUOv4hfBzM2Ywr1Vm';
    $country_id = 199;

    $sth->bindValue('name', $name);
    $sth->bindValue('email', $email);
    $sth->bindValue('token', $token);
    $sth->bindValue('country_id', $country_id);
    $sth->execute();
//b. отримання id нового юзера
    $userId = $mysql->lastInsertId();


//c. далі привязуємо юзеру рандомний проект, тобто робимо insert в таблицю project_user
    $projectAll = $mysql->query('SELECT id FROM projects ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

    $query = 'INSERT INTO project_user (project_id, user_id) VALUES (:project_id, :user_id)';
    $sth = $mysql->prepare($query);
    $sth->bindValue('project_id', $projectAll[rand(1, count($projectAll))]['id']);
    $sth->bindValue('user_id', $userId);
    $sth->execute();

//d. після цього створюємо лейб і прив'язуємо до нього юзера
    $query = 'INSERT INTO labels (name, author_id) VALUES (:name, :author_id)';
    $sth = $mysql->prepare($query);
    $sth->bindValue('name', 'randomName: ' . rand(0, 9999));
    $sth->bindValue('author_id', $userId);
    $sth->execute();

    $mysql->commit();
} catch (PDOException $exception) {
    $mysql->rollBack();
    echo $exception->getMessage();
}

