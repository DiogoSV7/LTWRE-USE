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
    require_once(__DIR__ . '/../database/item.class.php');

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['idItem'])) {
            $idItem = (int)$_POST['idItem'];
            
            $cartItems = $session->getCart();

            if (!in_array($idItem, $cartItems)) {
                $db = getDatabaseConnection();
                $session->addToCart($idItem);
            }

            header("Location: ../pages/index.php");
            exit();
        }
    }else{
        exit();
    }
?>
