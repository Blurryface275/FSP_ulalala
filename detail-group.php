<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Group</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #error-warning {
            color: red;
            border: 1px solid red;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #ffeaea;
            border-radius: 5px;
            text-align: center;
        }

        .tab-content-item {
            display: none;
        }

        .tab-content-item.active {
            display: block;
        }
    </style>
</head>

<body>
    <div id="sidebar" class="sidebar">
        <div style="display: flex; align-items: center; gap: 10px; padding: 0 20px; margin-bottom: 20px;">
            <div class="toggle-btn" id="toggle-btn">☰</div>
        </div>
        <ul>
            <li><a href="data-dosen.php">Data Dosen</a></li>
            <li><a href="data-mahasiswa.php">Data Mahasiswa</a></li>
            <li><a href="insert-dosen.php">Tambah Dosen</a></li>
            <li><a href="insert-mahasiswa.php">Tambah Mahasiswa</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'dosen'): ?>
                <li><a href="insert-group.php">Tambah Group</a></li>
            <?php endif; ?>
            <li><a href="change-password.php"> Ubah Password</a></li>
            <li><a href="data-group.php">Data Group</a></li>
            <li><a href="logout.php"> Logout</a></li>
        </ul>
    </div>

    <div class="content-box detail-group">
        <h1>Detail Group</h1>

        <?php
        if (!isset($_GET['id'])) {
            echo "<div id='error-warning'>ID group tidak valid.</div>";
        } else {
            $group_id = (int)$_GET['id'];
            if ($group_id <= 0) {
                echo "<div id='error-warning'>ID group tidak valid.</div>";
            } else {
                $query = "SELECT * FROM grup WHERE idgrup = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("i", $group_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    $group = $result->fetch_assoc();
        ?>
                    <div class="header-group">
                        <div class="group-info">
                            <h2><?= htmlspecialchars($group['nama']) ?></h2>
                            <p>Dibuat oleh: <?= htmlspecialchars($group['username_pembuat']) ?></p>
                            <p>Deskripsi: <?= htmlspecialchars($group['deskripsi']) ?></p>
                        </div>
                        <div class="registration-code-area">
                            <h3>Kode Registrasi:</h3>
                            <span id="reg-code" class="registration-code"><?= htmlspecialchars($group['kode_pendaftaran']) ?></span>
                        </div>
                    </div>

                    <!-- Tab Menu -->
                    <div class="tab-menu" id="tab-menu">
                        <button data-tab="member" class="active">Anggota</button>
                        <button data-tab="activities">Aktivitas</button>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content" id="tab-content">
                        <!-- Tab Anggota -->
                        <div id="tab-content-member" class="tab-content-item active">
                            <h3>Daftar Anggota</h3>
                            <?php
                            $query_members = "SELECT 
                                CASE WHEN a.isadmin = 1 THEN d.nama ELSE m.nama END AS nama_member,
                                CASE WHEN a.isadmin = 1 THEN 'Dosen' ELSE 'Mahasiswa' END AS role
                            FROM member_grup mg
                            JOIN akun a ON mg.username = a.username
                            LEFT JOIN dosen d ON a.npk_dosen = d.npk
                            LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
                            WHERE mg.idgrup = ?
                            ORDER BY nama_member ASC";
                            $stmt_members = $mysqli->prepare($query_members);
                            $stmt_members->bind_param("i", $group_id);
                            $stmt_members->execute();
                            $result_members = $stmt_members->get_result();

                            if ($result_members->num_rows > 0) {
                                echo "<ul class='member-list'>";
                                while ($member = $result_members->fetch_assoc()) {
                                    echo "<li>" . htmlspecialchars($member['nama_member']) . " (" . htmlspecialchars($member['role']) . ")</li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<p>Belum ada anggota dalam grup ini.</p>";
                            }
                            ?>
                        </div>

                        <!-- Tab Aktivitas -->
                        <div id="tab-content-activities" class="tab-content-item">
                            <h3>Aktivitas Grup</h3>
                            <?php
                            $query_activities = "SELECT judul, tanggal FROM event WHERE idgrup = ? ORDER BY tanggal DESC";
                            $stmt_activities = $mysqli->prepare($query_activities);
                            $stmt_activities->bind_param("i", $group_id);
                            $stmt_activities->execute();
                            $result_activities = $stmt_activities->get_result();

                            if ($result_activities && $result_activities->num_rows > 0) {
                                echo "<table border='1' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
                                echo "<thead><tr><th>Aktivitas</th><th>Tanggal & Waktu</th></tr></thead><tbody>";
                                while ($activity = $result_activities->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($activity['judul']) . "</td>";
                                    echo "<td>" . htmlspecialchars($activity['tanggal']) . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody></table>";
                            } else {
                                echo "<p>Belum ada aktivitas dalam grup ini.</p>";
                            }
                            ?>
                        </div>
                    </div>

        <?php
                } else {
                    echo "<div id='error-warning'>Group tidak ditemukan.</div>";
                }
            }
        }
        ?>
    </div> <!-- .content-box -->

    <!-- Box untuk add member dengan fitur search-->
    <div class="content-box">
        <h2>Tambah Anggota</h2>
        <label for="search-member">Cari Mahasiswa:</label>
        <input type="text" name="search-member" id="search-member" placeholder="Masukkan NRP ...">
        <div id="search-results">
            <!-- Munculin hasil pencarian di sini -->
            <?php
            // Menampilkan 10 mahasiswa pertama 
            $query_search = "SELECT nrp, nama FROM mahasiswa ORDER BY nama ASC LIMIT 10";
            $result_search = $mysqli->query($query_search);
            if ($result_search->num_rows > 0) {
                echo "<ul class='member-default-list'>";
                while ($student = $result_search->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($student['nama']) . " (" . htmlspecialchars($student['nrp']) . ") <button class='add-member-btn' data-nrp='" . htmlspecialchars($student['nrp']) . "'>Tambah</button></li>";
                }
                echo "</ul>";
            }
            ?>
        </div>

    </div>

    <script>
        $(function() {
            // Sidebar toggle
            $("#toggle-btn").on("click", function() {
                $("#sidebar").toggleClass("collapsed");
                $(".content-box").toggleClass("expanded");
            });

            // Tab switching
            $('#tab-menu button').on('click', function() {
                // Hapus active dari semua
                $('#tab-menu button').removeClass('active');
                $('.tab-content-item').removeClass('active');

                // Tambah active ke yang diklik
                $(this).addClass('active');
                const tab = $(this).data('tab');
                $('#tab-content-' + tab).addClass('active');
            });
        });
    </script>
</body>

</html>