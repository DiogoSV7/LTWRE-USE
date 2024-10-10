<?php
    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if(!$session->isLoggedIn()) 
        die(header('Location: ../pages/login.php'));

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/users.class.php');
    require_once(__DIR__ . '/../database/category.class.php');
    require_once(__DIR__ . '/../database/size.class.php');
    require_once(__DIR__ . '/../database/condition.class.php');

    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/users.tpl.php');
    require_once(__DIR__ . '/../templates/admin.tpl.php');

    $db = getDatabaseConnection();

    $user = User::getUserById($db, $session->getId());

    if (!User::isAdmin($db, $session->getId())) {
        header("Location: ../pages/index.php");
        exit();
    }

    if(isset($_GET['section'])) {
        $section = $_GET['section'];
        if($section === 'container') {
            drawAdminDashboard($db);
            exit();
        }
    }

    drawHeader($session, ["user-profile"]);
    drawProfileTop($db, $user);
    drawAdminDashboard($db);
    drawProfileBotton($db, $user);
    drawComments($db, $user->idUser, 15);
    drawFooter();
?>