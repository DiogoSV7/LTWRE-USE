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
    require_once(__DIR__ . '/../database/users.class.php');

    $db = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        try {
            $rating = $_POST['idRating'];
            $userId = $_POST['idUser'];

            $stmt = $db->prepare("DELETE FROM Ratings WHERE idRating = ?");
            $stmt->execute(array($rating));

            header("Location: ../pages/user-profile.php?idUser={$userId}#comments");
            exit();
        } 
        catch (Exception $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            header("../pages/index.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Invalid request.";
        $userId = $_POST['idUser'];
        header("Location: ../pages/user-profile.php?idUser={$userId}#comments");
        exit();
    }
?>