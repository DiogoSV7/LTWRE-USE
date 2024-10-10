<?php
    declare(strict_types=1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/chats.class.php');

    $db = getDatabaseConnection();

    $userId = $session->getId();
    $otherUserId = $_GET['other_user_id'];
    $itemId = $_GET['item_id'];

    $chats = Chat::getChatByUserAndItem($db, $userId, $otherUserId, $itemId);

    $chatMessages = array();
    foreach ($chats as $chat) {
        $chatMessages[] = array(
            'id' => $chat['idChat'],
            'message' => $chat['message'],
            'sender_id' => $chat['idSender'],
            'sender_name' => User::getUserById($db, $chat['idSender'])->name,
            'recipient_id' => $chat['idRecipient'],
            'timestamp' => $chat['timestamp'],
        );
    }

    echo json_encode($chatMessages);
?>
