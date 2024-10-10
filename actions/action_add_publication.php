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
    require_once(__DIR__ . '/../database/item.class.php');

    $db = getDatabaseConnection();


    $idItem = Item::getHighestItemId($db) + 1;
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $introduction = $_POST['introduction'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $model = $_POST['model'] ?? '';
    $price = intval($_POST['price'] ?? 0);
    $idCategory = intval($_POST['idCategory'] ?? 0);
    $idCondition = intval($_POST['idCondition'] ?? 0);
    $idSize = intval($_POST['idSize'] ?? 0);
    $idSeller = intval($_SESSION['id']);

    $targetDir = "../docs/itemImages/";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["main_image"])) {
        $mainImageName = uniqid() . '_' . basename($_FILES["main_image"]["name"]);
        $mainImageTargetFile = $targetDir . $mainImageName;
        $mainImageFileType = strtolower(pathinfo($mainImageTargetFile, PATHINFO_EXTENSION));

        $tmp_mainImageName = $_FILES["main_image"]["tmp_name"];

        $mainImageCheck = getimagesize($tmp_mainImageName);
        if ($mainImageCheck === false) {
            $_SESSION['message'] = "File is not an image.";
            header("Location: ../pages/add_publication.php");
            exit();
        }

        if ($_FILES["main_image"]["size"] > 2000000) {
            $_SESSION['message'] = "Main image file is too large.";
            header("Location: ../pages/add_publication.php");
            exit();
        }

        if ($mainImageFileType != "jpg" && $mainImageFileType != "png" && $mainImageFileType != "jpeg") {
            $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG files are allowed.";
            header("Location: ../pages/add_publication.php");
            exit();
        }

        if (!move_uploaded_file($tmp_mainImageName, $mainImageTargetFile)) {
            $_SESSION['message'] = "Sorry, there was an error uploading your main image file.";
            header("Location: ../pages/add_publication.php");
            exit();
        }
    }

    $secondaryImageIds = [];
    if (isset($_FILES["secondary_images"]) && !empty(array_filter($_FILES["secondary_images"]["name"]))) {

        foreach ($_FILES["secondary_images"]["tmp_name"] as $key => $tmp_name) {
            $secondaryImageName = uniqid() . '_' . basename($_FILES["secondary_images"]["name"][$key]);
            $secondaryImageTargetFile = $targetDir . $secondaryImageName;
            $secondaryImageFileType = strtolower(pathinfo($secondaryImageTargetFile, PATHINFO_EXTENSION));

            $tmp_secondaryImageName = $_FILES["secondary_images"]["tmp_name"][$key];

            $secondaryImageCheck = getimagesize($tmp_secondaryImageName);
            if ($secondaryImageCheck === false) {
                continue;
            }

            if ($_FILES["secondary_images"]["size"][$key] > 2000000) {
                continue;
            }

            if ($secondaryImageFileType != "jpg" && $secondaryImageFileType != "png" && $secondaryImageFileType != "jpeg") {
                continue;
            }

            if (!move_uploaded_file($tmp_secondaryImageName, $secondaryImageTargetFile)) {
                continue;
            }

            try {
                $stmt = $db->prepare('INSERT INTO Images (imagePath) VALUES (?)');
                $stmt->execute(array($secondaryImageTargetFile));

                $secondaryImageId = $db->lastInsertId();

                $secondaryImageIds[] = $secondaryImageId;
            } catch (PDOException $e) {
                continue;
            }
        }
    }

    try {
        $stmt = $db->prepare('INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array($idSeller, $name, $introduction, $description, $idCategory, $brand, $model, $idSize, $idCondition, $price, true));

        $lastInsertedItemId = $db->lastInsertId();

        $stmt = $db->prepare('INSERT INTO Images (imagePath) VALUES (?)');
        $stmt->execute(array($mainImageTargetFile));

        $mainImageId = $db->lastInsertId();

        $stmt = $db->prepare('INSERT INTO ItemImages (idItem, idImage, isMain) VALUES (?, ?, ?)');
        $stmt->execute(array($lastInsertedItemId, $mainImageId, true));

        foreach ($secondaryImageIds as $secondaryImageId) {
            $stmt = $db->prepare('INSERT INTO ItemImages (idItem, idImage) VALUES (?, ?)');
            $stmt->execute(array($lastInsertedItemId, $secondaryImageId));
        }

        header("Location: ../pages/index.php");
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error adding item: " . $e->getMessage();
        header("Location: ../pages/add_publication.php");
    }
?>