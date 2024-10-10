<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/item.class.php');

    $db = getDatabaseConnection();
    

    $search = isset($_GET["search"]) ? htmlentities($_GET["search"]) : '';
    $category = isset($_GET["category"]) ? htmlentities($_GET["category"]) : 'all';
    $size = isset($_GET["size"]) ? htmlentities($_GET["size"]) : 'all';
    $condition = isset($_GET["condition"]) ? htmlentities($_GET["condition"]) : 'all';
    $order = isset($_GET["order"]) ? htmlentities($_GET["order"]) : 'default';

    $items = Item::searchItems($db, $search, $category, $size, $condition, $order);

    $itemsWithDetails = array();
    foreach ($items as $item) {
        $image = $item->getMainImage($db);

        $itemDetails = array(
            'id' => $item->idItem,
            'name' => $item->name,
            'brand' => $item->brand,
            'model' => $item->model,
            'price' => $item->price,
            'image' => $image
        );

        $itemsWithDetails[] = $itemDetails;
    }

    echo json_encode($itemsWithDetails);
?>