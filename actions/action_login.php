<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/users.class.php');

    $db = getDatabaseConnection();

    $username = $_POST["username"];
    $password = $_POST["password"];
    try {
        if (User::userExists($username, $password)) {
            $_SESSION['id'] = User::getUserByUsername($db, $username)->idUser;
            $_SESSION['name'] = User::getUserByUsername($db, $username)->username;
            $session->addMessage('success', 'Login successful!');
            header("Location: ../pages/index.php");
            exit();
        } else {
            $session->addMessage('error', 'Wrong password!');
            $_SESSION['error'] = 'Login failed.';
            header("Location: ../pages/login.php");
            exit();
        }
    } catch (PDOException $e) {
        $session->addMessage('error', 'Database error: ' . $e->getMessage());
        header("Location: ../pages/login.php");
        exit();
    }
?>
