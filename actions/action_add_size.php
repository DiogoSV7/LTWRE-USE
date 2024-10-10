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

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $sizeName = $_POST['sizeName'] ?? '';

        if (empty($sizeName)) {
            exit();
        }

        try {
            $db = getDatabaseConnection();
            $highestSizeId = Size::getHighestSizeId($db);
            $size = new Size($highestSizeId + 1, $sizeName);
            $size->save($db);
            header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
            exit();
        } catch (PDOException $e) {
            exit();
        }
    } else {
        exit();
    }

?>