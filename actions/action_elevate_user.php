<?php
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

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["user_id"])) {
        $userId = $_POST["user_id"];

        if (User::isAdmin($db, $userId)){
            $_SESSION['message'] = "User is already an admin";
            header("Location: ../pages/admin-page.php");
            exit();
        }
        else{
            $stmt = $db->prepare('UPDATE Users SET isAdmin = 1 WHERE idUser = ?');
            $stmt->execute([$userId]);
            $_SESSION['message'] = "User elevated to admin";
            header("Location: ../pages/admin-page.php");
            exit();
        }
    } 
    else {
        $_SESSION['message'] = "Request failed";
        header("Location: ../pages/admin-page.php");
        exit();
    }
?>