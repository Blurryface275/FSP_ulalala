<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dosen</title>
</head>
<body>
<form action="insert-process.php" method="POST" enctype="multipart/form-data">

        <p>
            <label for="nama">Nama : </label>
            <input type="text" name="nama" id="nama">
        </p>
        <p>
            <label for="NPK">NPK : </label>
            <input type="text" name="NPK" id="NPK">
        </p>
        <p>
            <label for="foto">Foto : </label>
            <input type="file" name="foto" id="foto">
        </p>
        <button type="submit" name="insert">Insert</button>
    </form>
</body>
</html>