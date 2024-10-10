<?php 
    declare(strict_types = 1); 
    require_once(__DIR__ . '/../database/users.class.php');
    require_once(__DIR__ . '/../database/rating.class.php');
?>

<?php function drawProfileTop($db, $user) {
    $loggedId = $_SESSION['id'] ?? null;
    $condition = $loggedId === $user->idUser;
    $profileImage = $user->getProfileImage($db);
    $averageRating = Rating::getAverageRating($db, $user->idUser);
?>
    <section id="user-profile">
        <div class="profile-info">
            <?php if ($profileImage) { ?>
                <img src="<?= htmlentities((string)$profileImage) ?>" alt="<?= htmlentities($user->name) ?> Profile Picture">
            <?php } else { ?>
                <img src="../docs/images/default_profile_picture.png" alt="<?= htmlentities($user->name) ?> Profile Picture">
            <?php } ?>
            <div class="profile-details">
                <h2><?= htmlentities($user->name) ?></h2>
                <p>Email: <?= htmlentities($user->email) ?></p>
                <p>Username: <?= htmlentities($user->username) ?> </p>
                <?php if ($averageRating !== null && $averageRating !== 0.0) { ?>
                    <p>Rating: <?= number_format((float)htmlentities((string)$averageRating), 1)?> / 5 </p>
                <?php } ?>
            </div>
        </div>
        <?php if ($condition) { ?>
        <div class="profile-content">
            <div class="profile-actions">
                <a href="../pages/profile_user_details.php" id="user-details">User Details</a>
                <a href="../pages/wishlist.php" id="wishlist">Wishlist</a>
                <a href="../pages/profile_your_items.php" id="your-items">Your Items</a>
                <a href="../pages/profile_your_orders.php" id="your-orders">Your Orders</a>
                <a href="../pages/profile_orders_to_ship.php" id="orders-to-ship">Orders to Ship</a>
                <?php if ($user->isAdmin) { ?>
                    <a href="../pages/admin-page.php" id="admin-page">Admin Panel</a>
                <?php } ?>
                <a href="../actions/action_logout.php">Logout</a>
            </div>
            <div id="content-container">
    <?php }
} ?>


<?php function drawProfileBotton($db, $user) {
    $loggedId = $_SESSION['id'] ?? null;
    $condition = $loggedId === $user->idUser;
    if ($condition) { ?>
        </div>
    </div>
    <?php } ?>
    <?php if ($loggedId !== null && $loggedId !== $user->idUser) { ?>
    <form action="../actions/action_submit_rating.php" method="POST">
        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
        <input type="hidden" name="idUser" value="<?=$user->idUser?>">
        <div class="rating">
            <p>Rate this user:</p>
            <div class="stars">
                <input type="radio" id="star5" name="rating" value="5" required>
                <label for="star5"></label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4"></label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3"></label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2"></label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1"></label>
            </div>
        </div>

        <div class="comment">
            <textarea name="comment" placeholder="Leave a comment here"></textarea>
            <button type="submit">Submit</button>
        </div>
    </form>
    <?php } ?>
    <?php if (isset($_SESSION['message'])) { ?>
        <p><?= htmlentities($_SESSION['message']) ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php } ?> 
    <script>
        let isAdmin = <?= $user->isAdmin ?>;
    </script>
<?php } ?>

<?php function drawComments($db, $userId, $limit) { ?>
    <div class = "comments">
        <?php
        $comments = Rating::getRatingsByUser($db, $userId, $limit);
        if ($comments) { ?>
            <h3> Ratings and Comments </h3>
            <?php
            foreach ($comments as $comment) {
                $rating = htmlentities((string)$comment['rating']);
                $commentText = htmlentities($comment['comment']);
                ?>
                <div class="comment-info">
                    <p><span class="comment-stars"><?= str_repeat('&#9733;',(int) $rating) ?></span> </p>
                    <?php if ($commentText) { ?>
                        <p><?= $commentText ?> </p>
                    <?php } ?>
                    <?php if (User::isAdmin($db, $_SESSION['id'])) { ?>
                        <form action="../actions/action_delete_rating.php" method="post">
                            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                            <input type="hidden" name="idRating" value="<?= $comment['idRating'] ?>">
                            <input type="hidden" name="idUser" value="<?= $userId ?>">
                            <button type="submit">Delete</button>
                        </form>
                    <?php } ?>
                </div>
                <?php
            }
        }
        ?>
    </div>
    </section>
<?php } ?>

<?php function drawUserDetails($user) {
        if (isset($_SESSION['message'])) {
            echo "<p>{$_SESSION['message']}</p>";
            unset($_SESSION['message']);
        }
    ?>

    <form action="/actions/action_edit_profile.php" method="post" enctype="multipart/form-data" onsubmit="return validateProfileEditForm()" id="edit-profile-form">
        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
        <label>
            Username: <input type="text" id="username" name="username" value="<?=htmlentities($user->username)?>" required>
        </label><br>
        <label>
            New Password: <input type="password" id="password" name="password">
        </label><br>
        <label>
            Confirm Password: <input type="password" id="confirm_password" name="confirm_password">
        </label><br>
        <label>
            Name: <input type="text" id="name" name="name" value="<?=htmlentities($user->name)?>" required>
        </label><br>
        <label>
            Email: <input type="email" id="email" name="email" value="<?=htmlentities($user->email)?>" required>
        </label><br>
        <label>
            Profile Picture: <input type="file" id="main_image" name="main_image" onchange="previewMainImage(event)">
        </label>
        <img id="main_image_preview" src="#" alt="Profile Image Preview">
        <button type="submit">Save changes</button>
    </form>

<?php } ?>

