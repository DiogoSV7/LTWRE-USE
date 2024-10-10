<?php
    declare(strict_types=1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if(!$session->isLoggedIn()) 
        die(header('Location: ../pages/login.php'));

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/category.class.php');
    require_once(__DIR__ . '/../database/item.class.php');
    require_once(__DIR__ . '/../database/size.class.php');
    require_once(__DIR__ . '/../database/condition.class.php');

    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/item.tpl.php');

    $db = getDatabaseConnection();

    $categories = Category::getCategories($db);
    $sizes = Size::getSizes($db);
    $conditions = Condition::getConditions($db);

    drawHeader($session);
    drawAddPublication($categories, $sizes, $conditions);
    drawFooter();
?>
