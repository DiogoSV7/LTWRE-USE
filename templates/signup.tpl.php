<?php 
  declare(strict_types = 1); 

?>

<?php function drawLogin() { ?>
    <section id="login">
        <h2>Login</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo htmlentities($_SESSION['error']);
            unset($_SESSION['error']);
        }
        ?>
        <form method="post" action="../actions/action_login.php">
            <label>
                Username <input type="text" name="username" required>
            </label>
            <label>
                Password <input type="password" name="password" required>
            </label>
            <button type="submit">Login</button>
        </form>
    </section>
<?php } ?>

<?php function drawRegister() { ?>
    <section id="register">
        <h2>Register</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo htmlentities($_SESSION['error']);
            unset($_SESSION['error']);
        }
        ?>
        <form action="../actions/action_register.php" method="post">
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <label>
                Username <input type="text" name="username" required>
            </label>
            <label>
                First Name <input type="text" name="firstname" required>
            </label>
            <label>
                Last Name <input type="text" name="lastname" required>
            </label>
            <label>
                Email <input type="email" name="email" required>
            </label>
            <label>
                Password <input type="password" name="password" required>
            </label>
            <button type="submit">Register</button>
        </form>
    </section>
<?php } ?>


