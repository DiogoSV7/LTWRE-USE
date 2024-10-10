<?php 
    declare(strict_types = 1); 

?>

<?php function drawAdminDashboard(PDO $db) { ?>
    <section id="admin">
        <article>
            <h2>Add New Categories, Sizes, Conditions</h2>
            <div>
                <h3>Add New Category</h3>
                <form action="../actions/action_add_category.php" method="post">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="categoryName">Category Name:</label>
                    <input type="text" id="categoryName" name="categoryName" required>
                    <button type="submit">Add Category</button>
                </form>
            </div>
            <div>
                <h3>Add New Size</h3>
                <form action="../actions/action_add_size.php" method="post">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="sizeName">Size Name:</label>
                    <input type="text" id="sizeName" name="sizeName" required>
                    <button type="submit">Add Size</button>
                </form>
            </div>
            <div>
                <h3>Add New Condition</h3>
                <form action="../actions/action_add_condition.php" method="post">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="conditionName">Condition Name:</label>
                    <input type="text" id="conditionName" name="conditionName" required>
                    <button type="submit">Add Condition</button>
                </form>
        </article>

        <article>
            <h2>Remove New Categories, Sizes, Conditions</h2>
            <div>
                <h3>Remove Category</h3>
                <form action="../actions/action_remove_category.php" method="post">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <label for="categoryId">Select Category to Remove:</label>
                    <select id="categoryId" name="categoryId" required>
                        <?php
                        $categories = Category::getCategories($db);
                        foreach ($categories as $category) {
                            echo "<option value='" . $category->idCategory . "'>" . htmlentities($category->categoryName) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Remove Category</button>
                </form>
            </div>
            <div>
                <h3>Remove Size</h3>
                <form action="../actions/action_remove_size.php" method="post">
                    <label for="sizeId">Select Size to Remove:</label>
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <select id="sizeId" name="sizeId" required>
                        <?php
                        $sizes = Size::getSizes($db);
                        foreach ($sizes as $size) {
                            echo "<option value='" . $size->idSize . "'>" . htmlentities($size->sizeName) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Remove Size</button>
                </form>
            </div>
            <div>
                <h3>Remove Condition</h3>
                <form action="../actions/action_remove_condition.php" method="post">
                    <label for="conditionId">Select Condition to Remove:</label>
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <select id="conditionId" name="conditionId" required>
                        <?php
                        $conditions = Condition::getConditions($db);
                        foreach ($conditions as $condition) {
                            echo "<option value='" . $condition->idCondition . "'>" . htmlentities($condition->conditionName) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Remove Condition</button>
                </form>
        </article>
        <article>
            <h2>Elevate User to Admin Status</h2>
            <form action="../actions/action_elevate_user.php" method="post">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <?php  
                    if (isset($_SESSION['message'])) {
                        echo "<p>" . htmlentities($_SESSION['message']) . "</p>";
                        unset($_SESSION['message']);
                    }
                ?>
                <input type="text" id="user_search" name="user_search" placeholder="Search for a user..." onkeypress="checkEnterPress(event)">
                <div id="user_list">
                    <?php
                    $users = User::getAllUsers($db);

                    foreach ($users as $user) {
                        if ($user->isAdmin){
                            echo "<div><input type='radio' id='user_{$user->idUser}' name='user_id' value='{$user->idUser}'><label for='user_{$user->idUser}'>" . htmlentities($user->username) . " (" . htmlentities($user->name) . ") - <strong>Admin</strong></label></div>";
                        }
                        else {
                            echo "<div><input type='radio' id='user_{$user->idUser}' name='user_id' value='{$user->idUser}'><label for='user_{$user->idUser}'>" . htmlentities($user->username) . " (" . htmlentities($user->name) . ")</label></div>";
                        
                        }
                    }
                    ?>
                </div>
                <button type="submit">Elevate to Admin</button>
            </form>
        </article>
    </section>
<?php } ?>
