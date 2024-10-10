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
    require_once(__DIR__ . '/../database/size.class.php');

    $db = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $sizeId = isset($_POST['sizeId']) ? (int)$_POST['sizeId'] : 0;

        if ($sizeId === 0) {
            header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
            exit();
        }

        try {
            Size::removeSize($db, $sizeId);
            header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
            exit();
        } catch (PDOException $e) {
            exit();
        }
    } else {
        exit();
    }
?>
