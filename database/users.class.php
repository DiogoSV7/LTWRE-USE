<?php
declare(strict_types = 1);

class User {
    public int $idUser;
    public string $name;
    public string $username;
    public string $password;
    public string $email;
    public bool $isAdmin;

    public function __construct(int $idUser, string $name, string $username, string $password, string $email, bool $isAdmin) {
        $this->idUser = $idUser;
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->isAdmin = $isAdmin;
    }

    public static function userExists(string $username, string $password){
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM Users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user['password'])) {
            return true;
        } else {
            return false;
        }
    }
        

    public static function getUserById(PDO $db, int $idUser): ?User {
        $stmt = $db->prepare('SELECT * FROM Users WHERE idUser = ?');
        $stmt->execute([$idUser]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        return new User($user['idUser'], $user['name'], $user['username'], $user['password'], $user['email'], (bool) $user['isAdmin']);
    }

    public static function getUserByUsername(PDO $db, string $username): ?User {
        $stmt = $db->prepare('SELECT * FROM Users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        return new User($user['idUser'], $user['name'], $user['username'], $user['password'], $user['email'], (bool) $user['isAdmin']);
    }

    public static function isAdmin(PDO $db, int $idUser): bool {
        $stmt = $db->prepare('SELECT isAdmin FROM Users WHERE idUser = ?');
        $stmt->execute(array($idUser));
        $isAdmin = $stmt->fetchColumn();

        return (bool) $isAdmin;
    }

    public static function getAllUsers(PDO $db, int $limit=0): array {
        if ($limit > 0){
            $stmt = $db->prepare('SELECT * FROM Users LIMIT ?');
            $stmt->execute(array($limit));
        } else {
            $stmt = $db->prepare('SELECT * FROM Users');
            $stmt->execute();
        }

        $users = $stmt->fetchAll();
        $result = [];
        foreach ($users as $user) {
            $result[] = new User($user['idUser'], $user['name'], $user['username'], $user['password'], $user['email'], (bool) $user['isAdmin']);
        }

        return $result;
    }

    public function getProfileImage(PDO $db): ?string {
        $stmt = $db->prepare('SELECT imagePath FROM UserImage JOIN Images ON UserImage.idImage = Images.idImage WHERE idUser = ?');
        $stmt->execute(array($this->idUser));
        $imagePath = $stmt->fetch();
        if ($imagePath)
            $imagePath = $imagePath['imagePath'];
    
        return $imagePath ? $imagePath : null;
    }

    public static function isInWishlist(PDO $db, int $idUser, int $idItem): bool {
        $stmt = $db->prepare('SELECT * FROM Wishlists WHERE idUser = ? AND idItem = ?');
        $stmt->execute(array($idUser, $idItem));
        $result = $stmt->fetch();

        return $result ? true : false;
    }

    public static function isFromUser(PDO $db, int $idUser, int $idItem): bool {
        $stmt = $db->prepare('SELECT * FROM Items WHERE idSeller = ? AND idItem = ?');
        $stmt->execute(array($idUser, $idItem));
        $result = $stmt->fetch();

        return $result ? true : false;
    }

    public static function save(PDO $db, $name, $email, $username, $password, $idUser) {
        $stmt = $db->prepare('UPDATE Users SET name = ?, email = ?, username = ?, password = ? WHERE idUser = ?');
        $stmt->execute(array($name, $email, $username, $password, $idUser));
    }

    public static function searchUser(PDO $db, string $username): array {
        $stmt = $db->prepare('SELECT * FROM Users WHERE username LIKE ?');
        $stmt->execute(array("%$username%"));
        $users = $stmt->fetchAll();

        $result = [];
        foreach ($users as $user) {
            $result[] = new User($user['idUser'], $user['name'], $user['username'], $user['password'], $user['email'], (bool) $user['isAdmin']);
        }

        return $result;
    }
}
?>
