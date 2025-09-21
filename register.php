<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Register - Fullstack</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="card">
        <h1>Daftar Akun</h1>
        <p class="lead">Buat akun baru sebagai mahasiswa, dosen, atau admin.</p>

        <!-- form kirim ke register-process.php -->
        <form id="regForm" autocomplete="off" method="POST" action="register-process.php">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">- Pilih role -</option>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen</option>
            </select>

            <!-- Tempat field NRP/NPK muncul -->
            <div id="idField"></div>

            <label>
                <input type="checkbox" name="isadmin" value="1"> Jadikan Admin
            </label>
            <br><br>

            <input type="hidden" name="action" value="register">
            <button type="submit">Daftar</button>
        </form>
    </div>

    <script>
        $(function() {
            $("#role").on("change", function() {
                var role = $(this).val();
                var $field = $("#idField");
                $field.empty();

                if (role === "mahasiswa") {
                    $field.append(
                        '<label for="id">NRP Mahasiswa</label>' +
                        '<input id="id" name="id" type="text" maxlength="9" required>'
                    );
                } else if (role === "dosen") {
                    $field.append(
                        '<label for="id">NPK Dosen</label>' +
                        '<input id="id" name="id" type="text" maxlength="6" required>'
                    );
                }
            });
        });
    </script>
</body>

</html>