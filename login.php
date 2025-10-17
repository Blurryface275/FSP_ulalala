<!DOCTYPE html>
<html lang="en">

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
           <?php
        // untuk menampilkan pesan error jika ada
        if (isset($_SESSION['login_error'])) {
            echo '<p style="color: red; font-weight: bold;">' . $_SESSION['login_error'] . '</p>';
            unset($_SESSION['login_error']); // Hapus pesan setelah ditampilkan
        }
        ?>
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

<script>
   $(function() {
        $("form[action='login-process.php']").on("submit", function(e) {
            
            var username = $("#username").val().trim();
            var password = $("#password").val().trim();
            // Perhatikan: Anda harus memastikan ada elemen di HTML dengan ID="#login-error"
            var errorMessage = $("#login-error"); 

            if (username === "" || password === "") {
                
                // 1. Mencegah form untuk dikirim dengan mengembalikan false
                // Ini menggantikan e.preventDefault();
                errorMessage.show(); 
                
                return false; // <--- Perubahan Kunci di sini
            } else {
                
                // 2. Jika input terisi, sembunyikan pesan error dan biarkan form terkirim
                errorMessage.hide();
                
                // Secara implisit mengembalikan true, membiarkan form terkirim.
                // Anda bisa menambahkan 'return true;' secara eksplisit, tetapi tidak wajib.
            }
        });
    });
</script>