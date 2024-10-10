<?php
declare(strict_types=1);

class Category {
    public int $idCategory;
    public string $categoryName;

    public function __construct(int $idCategory, string $categoryName) {
        $this->idCategory = $idCategory;
        $this->categoryName = $categoryName;
    }

    public static function getCategories(PDO $db): array {
        try {
            $stmt = $db->prepare('SELECT * FROM Categories');
            $stmt->execute();
    
            $categories = array();
    
            while ($category = $stmt->fetch()) {
                $categories[] = new Category(
                    $category['idCategory'],
                    $category['categoryName']
                );
            }
    
            return $categories;
        } catch (PDOException $e) {
            echo "Error fetching categories: " . $e->getMessage();
            return array(); 
        }
    }

    public static function getCategoryByName(PDO $db, string $categoryName): ?Category {
        $stmt = $db->prepare('SELECT * FROM Categories WHERE categoryName = ?');
        $stmt->execute([$categoryName]);

        $category = $stmt->fetch();

        if ($category === false) {
            return null;
        }
        
        return new Category($category['idCategory'], $category['categoryName']);
    }

    public static function getCategoryById(PDO $db, int $idCategory): ?Category {
        $stmt = $db->prepare('SELECT * FROM Categories WHERE idCategory = ?');
        $stmt->execute([$idCategory]);

        $category = $stmt->fetch();

        if ($category === false) {
            return null;
        }
        
        return new Category($category['idCategory'], $category['categoryName']);
    }
    public function save(PDO $db): void {
        try {
            $stmt = $db->prepare('INSERT INTO Categories (categoryName) VALUES (?)');
            $stmt->execute([$this->categoryName]);
        } catch (PDOException $e) {
            exit();
        }
    }
    
    public static function getHighestCategoryId(PDO $db): int {
        $stmt = $db->prepare('SELECT MAX(idCategory) FROM Categories');
        $stmt->execute();
        $id = $stmt->fetchColumn(); 
        return $id !== null ? (int) $id : 0;
    }
    
    public static function removeCategory(PDO $db, int $idCategory): void {
        $stmt = $db->prepare('DELETE FROM Categories WHERE idCategory = ?');
        $stmt->execute(array($idCategory));
    }

    public static function getEmojiForCategory(string $categoryName): string {
        switch ($categoryName) {
            case 'Electronics':
                return '&#128187;';
            case 'Clothing':
                return '&#128084;';
            case 'Furniture':
                return '&#129681;';
            case 'Books':
                return '&#128218;';
            case 'Games':
                return '&#127918;';
            case 'Sports':
                return '&#9917;';
            case 'Homeware':
                return '&#128250;';
            case 'Others':
                return '&#128259;';
            default:
                return '';
        }
    }
}
?>
