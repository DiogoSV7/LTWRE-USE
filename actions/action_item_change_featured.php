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
        $idItem = $_POST['idItem'] ?? '';
        if (empty($idItem)) {
            exit();
        }

        try {
            $db = getDatabaseConnection();
            $item = Item::getItemById($db, (int) $idItem);
            $stmt = $db->prepare('UPDATE Items SET featured = ? WHERE idItem = ?');
            $stmt->execute(array(!$item->featured, $item->idItem));
            header("Location: ../pages/item.php?idItem=" . $idItem);
            exit();
        } 
        catch (PDOException $e) {
            exit();
        }
    } else {
        exit();
    }
?>