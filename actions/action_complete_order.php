<?php
    declare(strict_types=1);
    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if(!$session->isLoggedIn()) 
        die(header('Location: ../pages/login.php'));

    if ($_SESSION['csrf'] !== $_POST['csrf']) {
        exit();
    }

    require_once(__DIR__ . '/../database/connection.db.php');


    $db = getDatabaseConnection();


    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $totalPrice = (int) $_POST['total_price'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $zipCode = $_POST['zipcode'];
        $paymentMethod = $_POST['payment_method'];
        $userId = (int) $session->getId();
        $stmt = $db->prepare("INSERT INTO Orders (idBuyer, totalPrice) VALUES (?, ?)");
        $stmt->execute(array($userId, $totalPrice));

        $idOrder = $db->lastInsertId();
        $stmt = $db->prepare("INSERT INTO  CheckoutInfo (idOrder, address, city, zipCode, paymentMethod) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(array($idOrder, $address, $city, $zipCode, $paymentMethod));

        $cartItems = $session->getCart();

        foreach ($cartItems as $idItem) {
            $stmt = $db->prepare("INSERT INTO OrderItems (idOrder, idItem) VALUES (?, ?)");
            $stmt->execute(array($idOrder, $idItem));
            $session->removeFromCart($idItem);
        }

        header("Location: ../pages/index.php");
        exit();

    }
    else{
        header("Location: ../pages/index.php");
        exit();
    }

?>


