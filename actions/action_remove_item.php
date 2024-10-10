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

    $db = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $idItem = isset($_POST['idItem']) ? (int)$_POST['idItem'] : 0;

        if ($idItem === 0) {
            header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
            exit();
        }

        try {
            Item::deleteItem($db, $idItem);
            header("Location: ../pages/index.php");
            exit();
        } catch (PDOException $e) {
            exit();
        }
    } else {
        exit();
    }
?>