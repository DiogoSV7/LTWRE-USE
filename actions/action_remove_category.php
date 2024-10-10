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
    $db = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $categoryId = (int) $_POST['categoryId'];
        $category = Category::getCategoryById($db, $categoryId);
        if ($category !== null) {
            $category->removeCategory($db, $category->idCategory);
            header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
            exit();
        } else {
            echo "Category not found";
        }
    } else {
        exit();
    }
?>
