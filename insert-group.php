<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Group</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="box">
        <h2>Tambah Group Baru</h2>
        <form action="process-group.php" method="POST">
            <label for="group_name">Nama Group:</label><br>
            <input type="text" id="group_name" name="group_name" required><br><br>

            <label for="description">Deskripsi:</label><br>
            <textarea id="description" name="description" required></textarea><br><br>

            <button class="btn"type="submit" value="Tambah Group" name="add_group">Tambah Group</button>
        </form>
    </div>
</body>

</html>
<script>
        $(function() {
            $("#toggle-btn").on("click", function() {
                $("#sidebar").toggleClass("collapsed");
                $(".main-content").toggleClass("expanded");
            });
        });
    </script>   