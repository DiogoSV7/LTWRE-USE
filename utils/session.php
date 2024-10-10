<?php
  class Session {
    private array $messages;

    public function __construct() {
      session_start();
      if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = self::generate_random_token();
      }
      $this->messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
      unset($_SESSION['messages']);
    }

    public function isLoggedIn() : bool {
      return isset($_SESSION['id']);    
    }

    public function logout() {
      session_destroy();
    }

    public function getId() : ?int {
      return isset($_SESSION['id']) ? $_SESSION['id'] : null;    
    }

    public function getName() : ?string {
      return isset($_SESSION['name']) ? $_SESSION['name'] : null;
    }

    public function setId(int $id) {
      $_SESSION['id'] = $id;
    }

    public function setName(string $name) {
      $_SESSION['name'] = $name;
    }

    public function addMessage(string $type, string $text) {
      $_SESSION['messages'][] = array('type' => $type, 'text' => $text);
    }

    public function getMessages() {
      return $this->messages;
    }

    public function getCart(): array {
      return isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }
  
    public function addToCart(int $idItem): void {
      if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
          $_SESSION['cart'] = [];
      }
      $_SESSION['cart'][] = $idItem;
    }
    
    public function removeFromCart(int $idItem): void {
      if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
          return;
      }
      $key = array_search($idItem, $_SESSION['cart']);
      if ($key !== false) {
          unset($_SESSION['cart'][$key]);
      }
    }

    public function generate_random_token() {
      return bin2hex(openssl_random_pseudo_bytes(32));
    }
    
  }
?>