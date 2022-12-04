#Команди для консолі БД ДЗ

CREATE TABLE logs
(
    id          int primary key auto_increment,
    `action`    ENUM ('create', 'update', 'delete') not null,
    entity_id   int                                 not null,
    entity_name VARCHAR(255)                        not null,
    content     json                                not null
);


CREATE TRIGGER `insert_projects`
    AFTER INSERT
    ON projects
    FOR EACH ROW
BEGIN
    INSERT INTO logs (`action`, entity_id, entity_name, content)
    VALUES ('insert', NEW.id, 'project',
            JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'author_id', NEW.author_id, 'created_at', NEW.created_at,
                        'updated_at', NEW.updated_at));
END;


CREATE TRIGGER `update_projects`
    BEFORE UPDATE
    ON projects
    FOR EACH ROW
BEGIN
    INSERT INTO logs (`action`, entity_id, entity_name, content)
    VALUES ('update', OLD.id, 'project',
            JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'author_id', OLD.author_id, 'created_at', OLD.created_at,
                        'updated_at', OLD.updated_at));
END;


CREATE TRIGGER `delete_projects`
    BEFORE DELETE
    ON projects
    FOR EACH ROW
BEGIN
    INSERT INTO logs (`action`, entity_id, entity_name, content)
    VALUES ('delete', OLD.id, 'project',
            JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'author_id', OLD.author_id, 'created_at', OLD.created_at,
                        'updated_at', OLD.updated_at));
END;

#   CREATE
INSERT INTO projects (`name`, author_id, created_at, updated_at)
VALUES ('wieuyriwur', 2, '2022-11-30 15:53:22', '2022-12-03 19:22:34');

#   UPDATE
UPDATE projects
SET name = 'Testname'
where id = 10;


#   DELETE id 11
DELETE
FROM label_project
where project_id = 11;

DELETE
FROM project_user
where project_id = 11;

DELETE
FROM projects
where id = 11;


DROP TRIGGER insert_projects;
DROP TRIGGER update_projects;
DROP TRIGGER delete_projects;
