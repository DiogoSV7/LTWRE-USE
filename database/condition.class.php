<?php
declare(strict_types=1);

class Condition {
    public int $idCondition;
    public string $conditionName;

    public function __construct(int $idCondition, string $conditionName) {
        $this->idCondition = $idCondition;
        $this->conditionName = $conditionName;
    }

    static function getConditions(PDO $db) : array {
        $stmt = $db->prepare('SELECT * FROM Conditions');
        $stmt->execute();
    
        $conditions = array();
        
        while ($condition = $stmt->fetch()) {
            $conditions[] = new Condition(
              $condition['idCondition'],
              $condition['conditionName']
            );
        }

        return $conditions;
    }

    public static function getConditionById(PDO $db, int $idCondition): ?Condition {
        $stmt = $db->prepare('SELECT * FROM Conditions WHERE idCondition = ?');
        $stmt->execute([$idCondition]);

        $condition = $stmt->fetch();

        if ($condition === false) {
            return null;
        }
        
        return new Condition($condition['idCondition'], $condition['conditionName']);
    }

    public function save(PDO $db): void {
        try {
            $stmt = $db->prepare('INSERT INTO Conditions (conditionName) VALUES (?)');
            $stmt->execute([$this->conditionName]);
        } catch (PDOException $e) {
            exit();
        }
    }

    public static function getHighestConditionId(PDO $db): int {
        $stmt = $db->prepare('SELECT MAX(idCondition) FROM Conditions');
        $stmt->execute();
        $id = $stmt->fetchColumn(); 
        return $id !== null ? (int) $id : 0;
    }

    public static function removeCondition(PDO $db, int $idCondition): void {
        $stmt = $db->prepare('DELETE FROM Conditions WHERE idCondition = ?');
        $stmt->execute([$idCondition]);
    }
}
?>
