<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    
    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/category.class.php');
    require_once(__DIR__ . '/../database/item.class.php');
    require_once(__DIR__ . '/../database/users.class.php');

    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/item.tpl.php');

    $db = getDatabaseConnection();
    $idItem =(int) $_GET['idItem'];
    $idUser = (int) $session->getId();
    $isAdmin = User::isAdmin($db,$idUser);
    $isInWishlist = User::isInWishlist($db, $idUser, $idItem);
    $isFromUser = User::isFromUser($db, $idUser, $idItem);

    $categories = Category::getCategories($db);
    $item = Item::getItemById($db, $idItem);

    drawHeader($session);
    drawCategories($categories);
    drawItem($db, $item, $isAdmin, $isInWishlist, $isFromUser);
    drawFooter();
?>
