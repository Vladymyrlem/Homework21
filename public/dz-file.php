<?php
$dsn = 'mysql:dbname=homestead;host=localhost';
$username = 'homestead';
$password = 'secret';
$mysql = new PDO($dsn, $username, $password);

// 1.
    //  Створити транзакцію, в транзакції повинні бути sql запити
    $mysql->beginTransaction();
try {


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

//2
//2.1 створюємо таблицю logs (id, entity_id, entity_name, action, content)
$query = 'CREATE TABLE logs (
    id int primary key auto_increment,
    `action` ENUM("create", "update", "delete") not null,
    entity_id int not null ,
    entity_name  VARCHAR(255) not null ,
    content json not null )';
$mysql->query($query);

//2.2 створюємо трігер, який буде спрацьовувати після insert (тут записуємо інформацію в таблицю logs)
$query = "CREATE TRIGGER `insert_projects`
    AFTER INSERT ON projects
    FOR EACH ROW BEGIN
    INSERT INTO logs (`action`, entity_id, entity_name, content)
    VALUES ('insert', NEW.id, 'project',
            JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'author_id', NEW.author_id, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at));
END;";
$mysql->query($query);


//2.3 створюємо трігер, який буде спрацьовувати перед оновленням (тут записуємо інформацію в таблицю logs)
$query = "CREATE TRIGGER `update_projects`
    BEFORE UPDATE
    ON projects
    FOR EACH ROW
BEGIN
    INSERT INTO logs (`action`, entity_id, entity_name, content)
    VALUES ('update', OLD.id, 'project',
            JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'author_id', OLD.author_id, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at));
END;";
$mysql->query($query);


//2.4 створюємо трігер, який буде спрацьовувати перед видаленням (тут записуємо інформацію в таблицю logs)
$query = "CREATE TRIGGER `delete_projects`
    BEFORE DELETE
    ON projects
    FOR EACH ROW
BEGIN
    INSERT INTO logs (`action`, entity_id, entity_name, content)
    VALUES ('delete', OLD.id, 'project',
            JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'author_id', OLD.author_id, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at));
END;";
$mysql->query($query);


//Після цього написати sql запити, на створення нового запису в project
$mysql->query("INSERT INTO projects (`name`, author_id, created_at, updated_at)
VALUES ('wieuyriwur', 2, '2022-11-30 15:53:22', '2022-12-03 19:22:34')");


//UPdate
$mysql->query("UPDATE projects SET name = 'Testname' where id = 10");


//Delete
$mysql->query("
DELETE
FROM label_project
where project_id = 11;
");
$mysql->query("
DELETE
FROM project_user
where project_id = 11;
");
$mysql->query("
DELETE
FROM projects
where id = 11;
");
