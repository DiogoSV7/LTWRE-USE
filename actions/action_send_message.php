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
    require_once(__DIR__ . '/../database/chats.class.php');

    $db = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        try {
            $userId = $session->getId();
            $otherUserId = $_POST['otherUserId'] ?? null;
            $itemId = $_POST['itemId'] ?? null;
            $message = $_POST['message'] ?? '';

            if (empty($otherUserId) || empty($itemId) || empty($message)) {
                $_SESSION['message'] = "Error: Invalid parameters.";
                exit();
            }

            $query = "INSERT INTO Chats (idSender, idRecipient, message, idItem) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute(array($userId, $otherUserId, $message, $itemId));

            header("Location: ../pages/chat_messages.php?otherUserId={$otherUserId}&itemId={$itemId}");
            exit();
        } 
        catch (Exception $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            exit();
        }
    } else {
        $_SESSION['message'] = "Invalid request.";
        exit();
    }
?>
