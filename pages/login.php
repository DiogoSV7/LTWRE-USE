<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/category.class.php');
    require_once __DIR__ . '/../database/users.class.php';

    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/signup.tpl.php');

    $db = getDatabaseConnection();

    $categories = Category::getCategories($db);

    drawHeader($session);
    drawCategories($categories);
    drawLogin();
    drawFooter();
?>
