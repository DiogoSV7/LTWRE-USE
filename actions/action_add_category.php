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
    require_once(__DIR__ . '/../database/category.class.php');

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $categoryName = $_POST['categoryName'] ?? '';
        if (empty($categoryName)) {
            exit();
        }

        try {
            $db = getDatabaseConnection();
            $highestCategoryId = Category::getHighestCategoryId($db);
            $category = new Category($highestCategoryId + 1, $categoryName);
            $category->save($db);
            header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
            exit();
        } catch (PDOException $e) {
            exit();
        }
    } else {
        exit();
    }
?>
