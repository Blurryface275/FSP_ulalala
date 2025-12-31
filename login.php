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
    <link rel="stylesheet" href="style.css">

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
            <button type="submit" name="login" class="btn">Masuk</button>
        </form>
    </div>
</body>

<script>
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');

    toggleBtn.addEventListener('click', function() {
        if (window.innerWidth > 768) {
            // Mode Desktop: Mengecilkan sidebar (Collapsed)
            sidebar.classList.toggle('collapsed');
        } else {
            // Mode Mobile: Memunculkan/Menyembunyikan sidebar (Show)
            sidebar.classList.toggle('show');
        }
    });

    // Tambahan: Klik di luar sidebar untuk menutup saat di mobile
    document.addEventListener('click', function(event) {
        const isClickInside = sidebar.contains(event.target) || toggleBtn.contains(event.target);

        if (!isClickInside && window.innerWidth <= 768 && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
        }
    });

    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    // 1. Cek simpanan preferensi user di local storage saat halaman dimuat
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
        themeIcon.innerText = '☀️'; // Ganti jadi matahari jika mode dark
    }

    // 2. Event Listener Klik
    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');

        // Update icon dan simpan ke Local Storage
        if (body.classList.contains('dark-mode')) {
            themeIcon.innerText = '☀️';
            localStorage.setItem('theme', 'dark');
        } else {
            themeIcon.innerText = '🌙';
            localStorage.setItem('theme', 'light');
        }
    });
</script>

</html>