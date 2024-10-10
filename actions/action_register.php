<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/users.class.php');

    try{
        $username = $_POST["username"];
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        if ($_SESSION['csrf'] !== $_POST['csrf']) {
            exit();
        }
        
        $name = $firstname . ' ' . $lastname;
        
        $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO Users (username, name, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $name, $email, $hashedpassword]);
        
        header("Location: ../pages/login.php");
        exit();
    }
    catch (PDOException $e) {
        $_SESSION['error'] = "There was an error in the registration process.";
        header("Location: ../pages/register.php");
        exit();
    }


?>