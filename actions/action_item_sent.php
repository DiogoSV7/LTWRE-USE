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
        $idOrder = (int) $_POST['idOrder'];
        $idItem = (int) $_POST['idItem'];
        $stmt = $db->prepare("UPDATE OrderItems SET sent = 1 WHERE idOrder = ? AND idItem = ?");
        $stmt->execute(array($idOrder, $idItem));
            
        header("Location: ../pages/index.php");
        exit();

    }else{
        exit();
    }

?>