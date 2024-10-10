<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/users.class.php');

    $db = getDatabaseConnection();

    $search = isset($_GET["search"]) ? htmlentities($_GET["search"]) : '';

    $users = User::searchUser($db, $search);

    echo json_encode($users);
?>