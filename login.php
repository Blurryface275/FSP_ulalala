<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .box{
    max-width: 35%;
    max-height: 15%;
    border: 1px solid #ffcfcf;
    background-color: #ffcfcf;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin: auto;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)
    }
    .box form p label, input, button{
        margin: 5px;
        
    }



    </style>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="box">
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
    </div>
</body>

</html>