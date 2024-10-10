<?php
declare(strict_types=1);

class Size {
    public int $idSize;
    public string $sizeName;

    public function __construct(int $idSize, string $sizeName) {
        $this->idSize = $idSize;
        $this->sizeName = $sizeName;
    }

    public static function getSizes(PDO $db): array {
        try {
            $stmt = $db->prepare('SELECT * FROM Sizes');
            $stmt->execute();
            
            $sizes = array();
            
            while ($size = $stmt->fetch()) {
                $sizes[] = new Size(
                    $size['idSize'],
                    $size['sizeName']
                );
            }
    
            return $sizes;
        } catch (PDOException $e) {
            return array(); 
        }
    }
    

    public static function getSizeById(PDO $db, int $idSize): ?Size {
        $stmt = $db->prepare('SELECT * FROM Sizes WHERE idSize = ?');
        $stmt->execute([$idSize]);

        $size = $stmt->fetch();

        if ($size === false) {
            return null;
        }
        
        return new Size($size['idSize'], $size['sizeName']);
    }

    public function save(PDO $db): void {
        try {
            $stmt = $db->prepare('INSERT INTO Sizes (sizeName) VALUES (?)');
            $stmt->execute([$this->sizeName]);
        } catch (PDOException $e) {
            exit();
        }
    }

    public static function getHighestSizeId(PDO $db): int {
        $stmt = $db->prepare('SELECT MAX(idSize) FROM Sizes');
        $stmt->execute();
        $id = $stmt->fetchColumn(); 
        return $id !== null ? (int) $id : 0;
    }

    public static function removeSize(PDO $db, int $idSize): void {
        $stmt = $db->prepare('DELETE FROM Sizes WHERE idSize = ?');
        $stmt->execute([$idSize]);
    }
}
?>
