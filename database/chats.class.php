<?php 
declare(strict_types=1);

require_once 'users.class.php';

class Chat {
    public int $idChat;
    public int $idSender;
    public int $idRecipient;
    public string $message;
    public string $timestamp;
    public string $idItem;

    public function __construct(int $idChat, int $idSender, int $idRecipient, string $message, string $timestamp, string $idItem) {
        $this->idChat = $idChat;
        $this->idSender = $idSender;
        $this->idRecipient = $idRecipient;
        $this->message = $message;
        $this->timestamp = $timestamp;
        $this->idItem = $idItem;
    }

    public static function getChatById(int $idChat): ?Chat {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM Chats WHERE idChat = ?');
        $stmt->execute([$idChat]);
        $chat = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($chat === false) {
            return null;
        }
        return new Chat(
            $chat['idChat'],
            $chat['idSender'],
            $chat['idRecipient'],
            $chat['message'],
            $chat['timestamp'],
            $chat['idItem']
        );
    }

    public static function getUsersChats($db, $userId) {
        $query = "SELECT DISTINCT 
                      CASE 
                          WHEN c.idSender = ? THEN c.idRecipient 
                          ELSE c.idSender 
                      END AS otherUserId, 
                      c.idItem
                  FROM Chats c
                  WHERE c.idSender = ? OR c.idRecipient = ?";
    
        $stmt = $db->prepare($query);
        $stmt->execute(array($userId, $userId, $userId));
    
        $pairs = $stmt->fetchAll();
    
        return $pairs;
    }

    public static function getChatByUserAndItem($db, $userId, $otherUserId, $itemId) {
        $query = "SELECT * FROM Chats WHERE ((idSender = ? AND idRecipient = ?) OR (idSender = ? AND idRecipient = ?)) AND idItem = ?";
        $stmt = $db->prepare($query);
        $stmt->execute(array($userId, $otherUserId, $otherUserId, $userId, $itemId));
        return $stmt->fetchAll();
    }
    
    public static function getMessagesForChat(PDO $db, int $chatId): array {
        $stmt = $db->prepare('SELECT * FROM Chats WHERE idChat = ?');
        $stmt->execute([$chatId]);
        return $stmt->fetchAll();
    }

    public static function getMessagesInvolvingUser(PDO $db, int $userId): array {
        $stmt = $db->prepare('SELECT * FROM Chats WHERE idSender = ? OR idRecipient = ?');
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getSender(): ?User {
        return User::getUserById(getDatabaseConnection(), $this->idSender);
    }

    public function getRecipient(): ?User {
        return User::getUserById(getDatabaseConnection(), $this->idRecipient);
    }

}
?>
