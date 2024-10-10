<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../utils/session.php');
  $session = new Session();

  if(!$session->isLoggedIn()) 
    die(header('Location: ../pages/login.php'));

  $session->logout();

  header('Location: ../pages/index.php');
?>