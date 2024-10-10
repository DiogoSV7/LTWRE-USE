<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if(!$session->isLoggedIn()) 
        die(header('Location: ../pages/login.php'));

    if (!isset($_GET['orderId']) || !isset($_GET['itemId'])) {
        header('Location: ../pages/index.php');
        exit();
    }

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/order.class.php');

    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/item.tpl.php');

    $orderId = (int)$_GET['orderId'];
    $itemId = (int)$_GET['itemId'];
    if ($_SESSION['csrf'] !== $_GET['csrf']) {
        exit();
    }

    $order = Order::getOrderCheckoutInfo($db, $orderId, $itemId);

    if (!$order) {
        header('Location: ../pages/index.php');
        exit();
    }

    $shippingAddress = htmlentities($order['address']) . ", " . htmlentities($order['city']) . ", " . htmlentities($order['zipCode']);
    $qrCode = "http://api.qrserver.com/v1/create-qr-code/?data=". urlencode($shippingAddress) . "&size=300x300"; // goqr.me api


    drawHeader($session, ['shipping-form']);
    drawPrintShippingForm($order, $shippingAddress, $qrCode);
    drawFooter();
?>



