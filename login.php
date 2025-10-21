
<?php
session_start();
?>
<!DOCTYPE html>
<?php
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'] ?? '';
    unset($_SESSION['error_message']);
}
?>
<html lang="en">
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login-style.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="box">
        <?php if (!empty($error_message)): ?> 
            <div id="error-warning"><?= $error_message ?></div>
        <?php endif; ?>
        <form action="login-process.php" method="post">
            <p><label for="username">Username : </label>
                <input type="text" name="username" id="username" placeholder="Enter your username">
            </p>

            <p>
                <label for="password">Password : </label>
                <input type="password" name="password" id="password" placeholder="Enter your password"><br>
            </p>
            <button type="submit" name="login">Masuk</button>
        </form>
    </div>
</body>

</html>

