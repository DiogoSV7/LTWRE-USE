<?php 
  declare(strict_types = 1); 

?>

<?php function drawHeader($session, $jsPages = array("script")) { ?>
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>FEUP-reUSE</title>
      <link rel="icon" href="../docs/images/REuse-mini.png">
      <link rel="stylesheet" href="../css/style.css">
      <link rel="stylesheet" href="../css/layout.css">
      <link rel="stylesheet" href="../css/responsive.css">
      <?php foreach ($jsPages as $jsPage) { ?>
        <script src="../javascript/<?=$jsPage?>.js" defer></script>
      <?php } ?>
    </head>
    <body>
      <header>
        <h1>
            <a href="../pages/index.php">RE<strong>USE</strong></a>
        </h1>
        <?php 
          if ($session->isLoggedIn()) {
        ?>
            <div id="user-icons">
              <a href="../pages/chats.php"><img src="../docs/images/icon_chat.svg" alt="Chats"></a>
              <a href="../pages/add_publication.php"><img src="../docs/images/icon_add.svg" alt="Add Publication"></a>
              <a href="../pages/cart.php" id="cart-icon"><img src="../docs/images/icon_cart.svg" alt="Cart"></a>
              <a href="../pages/user-profile.php?idUser=<?=$_SESSION['id']?>"><img src="../docs/images/icon_profile.svg" alt="Profile"></a>
            </div>
            <div id="cart-flex">
              <div id="cart-items" style="display: none;">
                <ul>
                </ul>
              </div>
            </div>
            <form id="checkout-form" action="../pages/checkout.php" method="get">
              <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
              <button id="checkout-button" type="submit">Checkout</button>
            </form>
        <?php 
          } else {
        ?>
            <div id="signup">
                <a href="../pages/register.php">Register</a>
                <a href="../pages/login.php">Login</a>
            </div>
        <?php 
          }
        ?>
      </header>
<?php } ?>

<?php function drawCategories($categories) { ?>
    <nav id="menu">
        <!-- just for the hamburguer menu in responsive layout -->
        <input type="checkbox" id="hamburger"> 
        <label class="hamburger" for="hamburger"></label>
        <ul>
            <?php foreach ($categories as $category) { ?>
                <li><a href="../pages/index.php?category=<?= $category->idCategory ?>"><?= Category::getEmojiForCategory($category->categoryName) . " " . htmlentities($category->categoryName) ?></a></li>
            <?php } ?>
        </ul>
    </nav>
<?php } ?>

<?php function drawSearchAndFilter($categories, $sizes, $conditions) { ?>
  <aside>
    <h2>Search and Filter</h2>
      <form id="search-form">
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <input type="text" id="search" name="search" placeholder="Search here">
            <br>
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="all" selected>All</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category->idCategory; ?>"><?php echo Category::getEmojiForCategory($category->categoryName) . " " . htmlentities($category->categoryName); ?></option>
                <?php } ?>
            </select>
            <br>
            <label for="size">Size:</label>
            <select id="size" name="size">
                <option value="all" selected>All</option>
                <?php foreach ($sizes as $size) { ?>
                    <option value="<?php echo $size->idSize; ?>"><?php echo htmlentities($size->sizeName); ?></option>
                <?php } ?>
            </select>
            <br>
            <label for="condition">Condition:</label>
            <select id="condition" name="condition">
                <option value="all" selected>All</option>
                <?php foreach ($conditions as $condition) { ?>
                    <option value="<?php echo $condition->idCondition; ?>"><?php echo htmlentities($condition->conditionName); ?></option>
                <?php } ?>
            </select>
            <br>
            <label for="order">Order by:</label>
            <select id="order" name="order">
                <option value="default" selected>Default</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
            </select>
            <br>
            <button type="submit" id="search-button">Search</button>
        </form>
    </aside>
<?php } ?>

<?php function drawFooter() { ?>
    <footer>
        <p>&copy; FEUP-reUSE, 2024</p>
    </footer>
  </body>
</html>
<?php } ?>

