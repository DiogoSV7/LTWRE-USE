<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if(!$session->isLoggedIn()) 
        die(header('Location: ../pages/login.php'));

    require_once (__DIR__ . '/../database/connection.db.php');
    require_once (__DIR__ . '/../database/users.class.php');
    require_once (__DIR__ . '/../database/item.class.php'); 
    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/users.tpl.php');
    require_once(__DIR__ . '/../templates/item.tpl.php');
    $db = getDatabaseConnection();

    $user = User::getUserById($db, $_SESSION['id']);

    $wishlistItems = Item::getWishlistItems($db, $_SESSION['id']);

    if(isset($_GET['section'])) {
        $section = $_GET['section'];
        if($section === 'container') {
            drawItems($wishlistItems, $db, false, false, true);
            exit();
        }
    }

    drawHeader($session, ["user-profile"]);
    drawProfileTop($db, $user);
    drawItems($wishlistItems, $db, false, false, true);
    drawProfileBotton($db, $user);
    drawComments($db, $user->idUser, 15);
    drawFooter();
?>
