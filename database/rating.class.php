<?php 
declare(strict_types=1);

require_once 'users.class.php';

class Rating {
    public int $idRating;
    public int $idUser;
    public int $rating;
    public ?string $comment;
    public string $timestamp;

    public function __construct(int $idRating, int $idUser, int $rating, ?string $comment, string $timestamp) {
        $this->idRating = $idRating;
        $this->idUser = $idUser;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->timestamp = $timestamp;
    }

    public static function getRatingById(PDO $db,int $idRating): ?Rating {
        $stmt = $db->prepare('SELECT * FROM Ratings WHERE idRating = ?');
        $stmt->execute(array($idRating));
        $rating = $stmt->fetch();
        if ($rating === false) {
            return null;
        }
        return new Rating(
            $rating['idRating'],
            $rating['idUser'],
            $rating['rating'],
            $rating['comment'],
            $rating['timestamp']
        );
    }

    public static function getRatingsByUser(PDO $db,int $userId, int $limit): array {
        $stmt = $db->prepare('SELECT * FROM Ratings WHERE idUser = ? LIMIT ?');
        $stmt->execute(array($userId, $limit));
        return $stmt->fetchAll();
    }

    public static function getAverageRating(PDO $db, int $userId): ?float {
        $stmt = $db->prepare('SELECT avg(rating) FROM Ratings WHERE idUser = ?');
        $stmt->execute(array($userId));
        $avg = $stmt->fetchColumn();

        return $avg === false ? null : (float)$avg;
    }
}
?>
