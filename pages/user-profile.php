<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    require_once (__DIR__ . '/../database/connection.db.php');
    require_once (__DIR__ . '/../database/users.class.php');

    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/users.tpl.php');

    $db = getDatabaseConnection();
    $idUser = (int) $_GET['idUser'];

    $user = User::getUserById($db, $idUser);

    drawHeader($session, ["user-profile"]);
    drawProfileTop($db, $user);
    drawProfileBotton($db, $user);
    drawComments($db, $user->idUser, 15);
    drawFooter();
?>
