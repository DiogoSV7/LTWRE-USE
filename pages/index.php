<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/category.class.php');
    require_once(__DIR__ . '/../database/size.class.php');
    require_once(__DIR__ . '/../database/condition.class.php');
    require_once(__DIR__ . '/../database/item.class.php');
    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/item.tpl.php');

    $db = getDatabaseConnection();

    $categories = Category::getCategories($db);
    $sizes = Size::getSizes($db);
    $conditions = Condition::getConditions($db);

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        
        $search = isset($_GET["search"]) ? htmlentities($_GET["search"]) : '';
        $category = isset($_GET["category"]) ? htmlentities($_GET["category"]) : 'all';
        $size = isset($_GET["size"]) ? htmlentities($_GET["size"]) : 'all';
        $condition = isset($_GET["condition"]) ? htmlentities($_GET["condition"]) : 'all';
        $order = isset($_GET["order"]) ? htmlentities($_GET["order"]) : 'default';

        if(empty($search) && $category === 'all' && $size === 'all' && $condition === 'all' && $order === 'default') {
            $items = Item::getFeaturedItems($db);
        } 
        else {
            $items = Item::searchItems($db, $search, $category, $size, $condition, $order);
        }
    } 
    else {
        $items = Item::getFeaturedItems($db);
    }

    drawHeader($session, ['cart', 'search-items']);
    drawCategories($categories);
    drawSearchAndFilter($categories, $sizes, $conditions);
    drawItems($items, $db, true);
    drawFooter();

    $isLogged = json_encode(isset($_SESSION['id']));
    $temp = json_encode($_SESSION['csrf']);

?>
    <script>
        let isLogged = <?= $isLogged ?>;
        let temp = <?= $temp ?>;
    </script>
