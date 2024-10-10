<?php
    declare(strict_types=1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if(!$session->isLoggedIn()) 
        die(header('Location: ../pages/login.php'));

    if ($_SESSION['csrf'] !== $_POST['csrf']) {
        exit();
    }

    require_once (__DIR__ . '/../database/connection.db.php');
    require_once (__DIR__ . '/../database/users.class.php');

    $db = getDatabaseConnection();
    $user = User::getUserById($db, $_SESSION['id']);

    $targetDir = "../docs/userImages/";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user->name = $name;
        $user->email = $email;
        $user->username = $username;

        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user->password = $hashedPassword;
            $user->save($db, $name, $email, $username, $hashedPassword, $_SESSION['id']);
        } 
        else {
            $user->save($db, $name, $email, $username, $user->password, $_SESSION['id']);
        }

        if(isset($_FILES["main_image"])) {
            $mainImageName = uniqid() . '_' . basename($_FILES["main_image"]["name"]);
            $mainImageTargetFile = $targetDir . $mainImageName;
            $mainImageFileType = strtolower(pathinfo($mainImageTargetFile, PATHINFO_EXTENSION));

            $tmp_mainImageName = $_FILES["main_image"]["tmp_name"];
            
            if (empty($tmp_mainImageName)) {
                header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
                exit();
            }

            $mainImageCheck = getimagesize($tmp_mainImageName);
            if ($mainImageCheck === false) {
                $_SESSION['message'] = "File is not an image.";
                header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
                exit();
            }

            if ($_FILES["main_image"]["size"] > 2000000) {
                $_SESSION['message'] = "Main image file is too large.";
                header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
                exit();
            }

            if ($mainImageFileType != "jpg" && $mainImageFileType != "png" && $mainImageFileType != "jpeg") {
                $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG files are allowed.";
                header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
                exit();
            }

            if (!move_uploaded_file($tmp_mainImageName, $mainImageTargetFile)) {
                $_SESSION['message'] = "Sorry, there was an error uploading your main image file.";
                header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
                exit();
            }
            
            try {
                $stmt = $db->prepare('INSERT INTO Images (imagePath) VALUES (?)');
                $stmt->execute(array($mainImageTargetFile));

                $mainImageId = $db->lastInsertId();

                $stmt = $db->prepare('SELECT idImage FROM UserImage WHERE idUser = ?');
                $stmt->execute(array($user->idUser));
                $oldImageId = $stmt->fetch();

                if ($oldImageId) {
                    $stmt = $db->prepare('UPDATE UserImage SET idImage = ? WHERE idUser = ?');
                    $stmt->execute(array($mainImageId, $user->idUser));
                    $oldImageId = $oldImageId['idImage'];
                    $stmt = $db->prepare('DELETE FROM Images WHERE idImage = ?');
                    $stmt->execute(array($oldImageId));
                } else {
                    $stmt = $db->prepare('INSERT INTO UserImage (idUser, idImage) VALUES (?, ?)');
                    $stmt->execute(array($user->idUser, $mainImageId));
                }
            }
            catch (PDOException $e) {
                $_SESSION['message'] = "Error saving profile image.";
                header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
                exit();
            }
        }
    }

    header("Location: ../pages/user-profile.php?idUser=" . $_SESSION['id']);
    exit();
?>
