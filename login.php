<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
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
    <form action="register.php" method="post">
        <button type="register" name="register">Register</button>
    </form>
</body>

</html>