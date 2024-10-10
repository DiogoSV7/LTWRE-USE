<?php
    declare(strict_types=1);
    require_once(__DIR__ . '/../utils/session.php');
    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/item.class.php');
    require_once(__DIR__ . '/../templates/item.tpl.php');
    require_once(__DIR__ . '/../templates/common.tpl.php');

    $db = getDatabaseConnection();

    $session = new Session();

    if(!$session->isLoggedIn()) 
        die(header('Location: ../pages/login.php'));

    $cartItems = $session->getCart();

    drawHeader($session);

    $items = [];
    $totalPrice = 0;

    foreach ($cartItems as $idItem) {
        $item = Item::getItemById($db, (int)$idItem);
        if ($item) {
            $items[] = $item;
            $totalPrice += $item->price;
        }
    }

    drawItems($items, $db, false);
    drawCart($totalPrice, $items);
    drawFooter();
?>
