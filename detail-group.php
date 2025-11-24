<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
// Cek apakah username yang login sama dengan username pembuat group
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
    <!-- jQuery -->
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
        <div class="header-group">

            <?php
            // Ambil ID group dari parameter URL
            if (isset($_GET['id'])) {
                $group_id = (int)$_GET['id'];
                $query = "SELECT * FROM grup WHERE idgrup = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("i", $group_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result && $result->num_rows > 0) {
                    $group = $result->fetch_assoc();
                    echo "<div class='group-info'>";
                    echo "<h2>" . htmlspecialchars($group['nama']) . "</h2>"; // nama grup
                    echo "<p>Dibuat oleh: " . htmlspecialchars($group['username_pembuat']) . "</p>";
                    echo "<p>Deskripsi: " . htmlspecialchars($group['deskripsi']) . "</p>";
                    echo "</div>";
                    // Kode pendaftaran
                    echo '<div class="registration-code-area">';
                    echo '<h3>Kode Registrasi:</h3>';
                    echo "<span id='reg-code' class='registration-code'>" . htmlspecialchars($group['kode_pendaftaran']) . "</span>";
                    echo '</div>';
                    echo '</div>'; // tutup header-group
                    // Tab Menu
                    // Tab anggota
                    echo '<div class="tab-menu" id="tab-menu">';
                    echo '<button data-tab="member" class="active">Anggota</button>';
                    echo '<button data-tab="activities">Aktivitas</button>';
                    echo '</div>';
                    // Konten tab
                    echo '<div class="tab-content" id="tab-content">';
                    // Konten anggota
                    echo '<div id="tab-content-member" class="tab-content-item active">';
                    echo '<h3>Daftar Anggota</h3>';
                    $query_members = "SELECT CASE WHEN a.isadmin = 1 THEN d.nama ELSE m.nama END AS nama_member,
                                        CASE 
                                            WHEN a.isadmin = 1 THEN a.npk_dosen 
                                            ELSE a.nrp_mahasiswa 
                                        END AS npk_nrp_member,
                                        CASE 
                                            WHEN a.isadmin = 1 THEN 'Dosen' 
                                            ELSE 'Mahasiswa' 
                                        END AS role
                                    FROM member_grup mg
                                    JOIN akun a ON mg.username = a.username
                                    LEFT JOIN dosen d ON a.npk_dosen = d.npk
                                    LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
                                    WHERE mg.idgrup = ?
                                    ORDER BY nama_member ASC;";
                    $stmt_members = $mysqli->prepare($query_members);
                    $stmt_members->bind_param("i", $group_id);
                    $stmt_members->execute();
                    $result_members = $stmt_members->get_result();
                    while ($member = $result_members->fetch_assoc()) {
                        echo "<ul class='member-list'>";

                        echo "<li>" . htmlspecialchars($member['nama_member']) . " (" . htmlspecialchars($member['role']) . ")</li>";

                        echo "</ul>";
                    }
                    if ($result_members->num_rows == 0) {
                        echo "<p>Belum ada anggota dalam grup ini.</p>";
                    }
                    echo '</div>'; // tutup tab-content
                    echo '</div>'; // tutup tab-content utama

                } else {
                    echo "<div id='error-warning'>Group tidak ditemukan.</div>";
                }
                // Tab Event
                echo '<div id="tab-content-activities" class="tab-content-item" echo ($activeTab == "activities" ? "active" : "");> ';
                echo '<h3>Aktivitas Grup</h3>';
                // Ambil aktivitas dari tabel aktivitas berdasarkan idgrup
                $query_activities = "SELECT judul, tanggal FROM event WHERE idgrup = ? ORDER BY tanggal DESC";
                $stmt_activities = $mysqli->prepare($query_activities);
                $stmt_activities->bind_param("i", $group_id);
                $stmt_activities->execute();
                $result_activities = $stmt_activities->get_result();
                if ($result_activities && $result_activities->num_rows > 0) {
                    echo "<table border=1 cell-spacing=0><th>Aktivitas</th> <th>Tanggal & Waktu</th>";
                    while ($activity = $result_activities->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($activity['judul']) . "</td>";
                        echo "<td>" . htmlspecialchars($activity['tanggal']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo '</div>'; // tutup tab-content
                    echo '</div>'; // tutup tab-content utama
                } else {
                    echo "<p>Belum ada aktivitas dalam grup ini.</p>";
                }
            } else {
                echo "<div id='error-warning'>ID group tidak valid.</div>";
            }

            ?>

        </div>

</body>

</html>

<script>
    $(function() {
        // Logika Sidebar
        $("#toggle-btn").on("click", function() {
            $("#sidebar").toggleClass("collapsed");
            $(".main-content").toggleClass("expanded");
        });

        // Logika buat Tab Switching
        $('#tab-menu button').on('click', function() {
            const tabName = $(this).data('tab');

            // Hapus kelas aktif dari semua tombol dan konten
            $('#tab-menu button').removeClass('active');
            $('.tab-content-item').removeClass('active');

            // Aktifkan tombol yang diklik dan konten yang sesuai
            $(this).addClass('active');
            $('#tab-content-' + tabName).addClass('active');
        });

        // Inisiasi tampilan tab saat halaman dimuat (berdasarkan URL parameter)
        // Pastikan tab content yang sesuai juga diset active saat load
        const initialTab = new URLSearchParams(window.location.search).get('tab') || 'member';
        $(`#tab-menu button[data-tab="${initialTab}"]`).addClass('active');
        $(`#tab-content-${initialTab}`).addClass('active');
    });
</script>