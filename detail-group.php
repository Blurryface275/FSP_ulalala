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

$logged_in_username = $_SESSION['username'];
$logged_in_role = $_SESSION['role'] ?? '';
$is_admin = $_SESSION['isadmin'] ?? 0;
$group_id = $_GET['id'] ?? null;
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
$group = null; // Inisialisasi variabel grup
$is_member = false;
$can_edit_group = false; // Akan di-update setelah data grup diambil

// --- Ngcek Member ---
function isMember($mysqli, $username, $group_id)
{
    $q = "SELECT 1 FROM member_grup WHERE username = ? AND idgrup = ?";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("si", $username, $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// --- Ambil data grup sama pengecekan ---
if ($group_id > 0) {
    $query = "SELECT * FROM grup WHERE idgrup = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $group = $result->fetch_assoc();
        $is_member = isMember($mysqli, $logged_in_username, $group_id);

        // Menentukan otoritas edit
        $is_owner = ($logged_in_username == $group['username_pembuat']);
        $can_edit_group = $is_owner || $is_admin == 1 || $logged_in_role == 'dosen';
    }
}

// ---Logika updet grup ---
$edit_submitted = isset($_POST['action']) && $_POST['action'] === 'update_group';

if ($group && $can_edit_group && $edit_submitted) {
    $nama_baru = trim($_POST['nama_grup'] ?? '');
    $deskripsi_baru = trim($_POST['deskripsi'] ?? '');
    $jenis_baru = trim($_POST['jenis'] ?? '');
    $target_group_id = $_POST['idgrup'] ?? $group_id;

    if (empty($nama_baru) || empty($deskripsi_baru) || empty($jenis_baru)) {
        $error_message = "Semua field harus diisi.";
    } else {
        $sqlUpdate = "UPDATE grup SET nama = ?, deskripsi = ?, jenis = ? WHERE idgrup = ?";
        $stmtUpdate = $mysqli->prepare($sqlUpdate);
        $stmtUpdate->bind_param("sssi", $nama_baru, $deskripsi_baru, $jenis_baru, $target_group_id);

        if ($stmtUpdate->execute()) {
            $success_message = "Data grup berhasil diperbarui!";
            // Perbarui tampilan
            $group['nama'] = $nama_baru;
            $group['deskripsi'] = $deskripsi_baru;
            $group['jenis'] = $jenis_baru;
        } else {
            $error_message = "Gagal memperbarui grup: " . $stmtUpdate->error;
        }
    }
}


// --- Logika untuk Mahasiswa yg Belum Member ---
if ($group && $logged_in_role === 'mahasiswa' && !$is_member && isset($_POST['action']) && $_POST['action'] === 'submit_code') {
    $code = trim($_POST['reg_code'] ?? '');

    // Verifikasi Kode
    if ($group['kode_pendaftaran'] === $code) {
        // Kalo bener nambah member
        $sqlInsert = "INSERT INTO member_grup (idgrup, username) VALUES (?, ?)";
        $stmtInsert = $mysqli->prepare($sqlInsert);
        $stmtInsert->bind_param("is", $group_id, $logged_in_username);

        if ($stmtInsert->execute()) {
            $_SESSION['success_message'] = "Selamat! Anda berhasil bergabung ke grup " . htmlspecialchars($group['nama']) . ".";

            header("Location: detail-group.php?id=" . $group_id);
            exit();
        } else {
            $error_message = "Gagal menambahkan member. Coba lagi atau hubungi administrator. Error: " . $mysqli->error;
        }
    } else {
        // Kode Salah
        $error_message = "Kode registrasi salah! (jangan lupa perhatikan lower/uppercase!) Coba lagi.";
    }
}


// --- Refresh status $can_view_full_detail setelah semua proses beres ---
$can_view_full_detail = $is_member ||
    ($group && $logged_in_username == $group['username_pembuat']) ||
    $is_admin == 1 ||
    $logged_in_role == 'dosen';


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

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
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
            <div id="theme-toggle" style="cursor: pointer; font-size: 18px;">
                <span id="theme-icon">🌙</span>
            </div>
        </div>

        <ul>
            <?php
            // Admin
            if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1): ?>

                <li><a href="data-dosen.php">Data Dosen</a></li>
                <li><a href="data-mahasiswa.php">Data Mahasiswa</a></li>
                <li><a href="insert-dosen.php">Tambah Dosen</a></li>
                <li><a href="insert-mahasiswa.php">Tambah Mahasiswa</a></li>
                <li><a href="data-group.php">Data Group</a></li>
                <li><a href="insert-group.php">Tambah Group</a></li>

            <?php
            // Dosen
            elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'dosen'): ?>

                <li><a href="data-group.php">Data Group</a></li>
                <li><a href="insert-group.php">Tambah Group</a></li>

            <?php
            // Mahasiswa
            elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'mahasiswa'): ?>

                <li><a href="data-group.php">Data Group</a></li>

            <?php endif; ?>

            <li><a href="change-password.php">Ubah Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>
    <div class="main-content-wrapper">
        <div class="content-box detail-group">
            <h1>Detail Group</h1>

            <?php if ($success_message): ?>
                <div class="alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <?php
            if (!$group_id) {
                echo "<div id='error-warning'>ID group tidak valid.</div>";
            } elseif (!$group) {
                echo "<div id='error-warning'>Group tidak ditemukan.</div>";
            } else {

                // --- TAMPILAN kalo blm Member & Role Mahasiswa) ---
                if (!$can_view_full_detail && $logged_in_role === 'mahasiswa') {
            ?>
                    <div style="text-align: center; padding: 40px; border: 1px solid #ccc; border-radius: 8px;">
                        <h2>Verifikasi Keanggotaan Grup: <?= htmlspecialchars($group['nama']) ?></h2>
                        <p>Masukkan Kode Registrasi Grup untuk bergabung dan melihat detail:</p>

                        <form action="detail-group.php?id=<?= $group_id ?>" method="POST" style="margin-top: 20px;">
                            <input type="hidden" name="action" value="submit_code">

                            <input type="text" id="reg_code" name="reg_code" placeholder="Kode Registrasi" required style="padding: 10px; width: 60%; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">

                            <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                Konfirmasi Kode
                            </button>
                        </form>
                        <p style="margin-top: 20px;"><a href="data-group.php">&laquo; Kembali ke Daftar Grup</a></p>
                    </div>

                <?php
                    // --- TAMPILAN DETAIL GRUP PENUH kalo dah terverif ---
                } else {
                ?>
                    <div class="header-group">

                        <div class="header-top">
                            <h2 class="group-title" id="groupTitle"><?= htmlspecialchars($group['nama']) ?></h2>

                            <div class="group-buttons">
                                <?php if ($_SESSION['role'] == 'mahasiswa' && $_SESSION['username'] !== $group['username_pembuat']) : ?>
                                    <button
                                        class="leave-group-btn"
                                        data-id="<?= $group['idgrup'] ?>"
                                        data-username="<?= $_SESSION['username'] ?>">
                                        Keluar
                                    </button>
                                <?php endif; ?>


                                <?php if ($_SESSION['username'] == $group['username_pembuat']) : ?>
                                    <button
                                        id="btnEditGroup"
                                        class="edit-group-btn"
                                        data-id="<?= $group['idgrup'] ?>">
                                        Edit
                                    </button>
                                    <button
                                        id="btnHapusGroup"
                                        class="delete-group-btn"
                                        data-id="<?= $group['idgrup'] ?>">
                                        Hapus
                                    </button>

                                <?php endif; ?>
                            </div>
                        </div>

                        <hr class="separator">

                        <div class="header-info-body">

                            <div class="group-info">
                                <p><strong>Dibuat oleh:</strong> <?= htmlspecialchars($group['username_pembuat']) ?></p>
                                <p><strong>Deskripsi:</strong> <span id="desc-group"><?= htmlspecialchars($group['deskripsi']) ?></span></p>
                                <p><strong>Tgl Pembentukan:</strong> <?= date("Y-m-d", strtotime($group['tanggal_pembentukan'])) ?></p>
                                <p><strong>Jenis Group:</strong> <span id="jenis-group"><?= htmlspecialchars($group['jenis']) ?></span></p>
                            </div>

                            <div class="registration-code-area">
                                <p class="code-label"><strong>Kode Registrasi:</strong></p>
                                <span id="reg-code" class="registration-code">
                                    <?php
                                    //tampilin kodenya
                                    echo htmlspecialchars($group['kode_pendaftaran']);

                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>


                    <div class="tab-menu" id="tab-menu">
                        <button data-tab="member" class="active">Anggota</button>
                        <button data-tab="activities">Aktivitas</button>
                        <button data-tab="threads">Thread</button>
                    </div>

                    <!-- Tab Anggota Menu -->
                    <div class="tab-content" id="tab-content">
                        <div id="tab-content-member" class="tab-content-item active">
                            <h3>Daftar Anggota</h3>
                            <?php
                            require_once("class/group.php");
                            $groupObj = new group($mysqli);

                            // Ambil data member dari class
                            $members = $groupObj->getGroupMembers($group_id);

                            echo "<div class='member-list'>";

                            if (!empty($members)) {
                                foreach ($members as $member) {
                                    echo "<div class='member-item' id='student-" . htmlspecialchars($member['id_anggota']) . "'>";
                                    echo "<div class='member-item-flex'>";

                                    // Tampilkan Nama + ID (NRP/NPK)
                                    echo htmlspecialchars($member['nama_member']) . " (" . htmlspecialchars($member['id_anggota']) . ")";

                                    // Cek permission: Jika user login adalah pembuat grup, tampilkan tombol hapus 
                                    // (Kecuali untuk dirinya sendiri)
                                    if ($group['username_pembuat'] == $logged_in_username && $member['username_login'] != $logged_in_username) {
                                        echo "<button class='remove-member-btn'
                                              data-username='" . htmlspecialchars($member['username_login']) . "'
                                              data-nrp='" . htmlspecialchars($member['id_anggota']) . "'
                                              data-nama='" . htmlspecialchars($member['nama_member']) . "'>Hapus</button>";
                                    }

                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<p>Belum ada anggota dalam grup ini.</p>";
                            }

                            echo "</div>";
                            ?>
                        </div>

                        <!-- Tab Event Menu -->
                        <div id="tab-content-activities" class="tab-content-item">
                            <h3>Event Grup</h3>

                            <?php
                            require_once("class/event.php");
                            $eventObj = new Event($mysqli);

                            // Ambil semua aktivitas event dari class
                            $activities = $eventObj->getEventsByGroup($group_id);

                            if (!empty($activities)) {
                                echo "<table border='1' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
                                echo "<thead>
                                            <tr>
                                                <th>Event</th>
                                                <th>Tanggal & Waktu</th>
                                                <th colspan='2'>Aksi</th>
                                            </tr>
                                        </thead>
                                    <tbody>";

                                foreach ($activities as $activity) {
                                    $event_id = $activity['idevent'];

                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($activity['judul']) . "</td>";
                                    echo "<td>" . htmlspecialchars($activity['tanggal']) . "</td>";
                                    echo "<td>
                                            <a href='detail-event.php?event_id=" . urlencode($event_id) . "&group_id=" . urlencode($group_id) . "' class='detail-event-btn'>Lihat Detail</a>
                                        </td>";

                                    // Logic tombol hapus khusus pembuat grup
                                    if ($group['username_pembuat'] == $logged_in_username) {
                                        echo "<td>
                                        <button class='delete-event-btn' type='button' 
                                        onclick=\"if(confirm('Yakin ingin menghapus event ini?')) {
                                            window.location.href='delete-event.php?event_id=" . urlencode($event_id) . "&group_id={$group_id}';
                                        }\">
                                        Hapus Event
                                        </button>
                                    </td>";
                                    } else {
                                        echo "<td></td>";
                                    }
                                    echo "</tr>";
                                }
                                echo "</tbody></table>";
                            } else {
                                echo "<p>Belum ada aktivitas dalam grup ini.</p>";
                            }

                            // Tombol Tambah Event
                            if ($group_id > 0 && $can_edit_group): ?>
                                <form action='insert-event.php' method='get' style="margin-top: 15px;">
                                    <input type='hidden' name='idgrup' value='<?= htmlspecialchars($group_id) ?>'>
                                    <button type='submit'>Tambah Event</button>
                                </form>
                            <?php endif; ?>

                            <!-- Thread Menu Tab -->
                            <div id="tab-content-threads" class="tab-content-item">
                                <h3>Thread Grup</h3>
                                <?php
                                require_once("class/thread.php");
                                $threadObj = new thread($mysqli);

                                // Ambil semua data thread dari class
                                $threads = $threadObj->getThreads($group_id);

                                if (!empty($threads)) {
                                    echo "<table border='1' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
                                    echo "<thead>
                                            <tr>
                                                <th>Thread ID / Pembuat</th>
                                                <th>Tanggal Pembuatan</th>
                                                <th>Status</th>
                                                <th colspan='2'>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>";

                                    foreach ($threads as $thread) {
                                        $thread_id = $thread['idthread'];
                                        $tanggal = $thread['tanggal_pembuatan'];
                                        $pembuat = $thread['username_pembuat'];
                                        $status_thread = $thread['status'];

                                        echo "<tr>";
                                        echo "<td>Thread #" . htmlspecialchars($thread_id) . " by " . htmlspecialchars($pembuat) . "</td>";
                                        echo "<td>" . htmlspecialchars($tanggal) . "</td>";
                                        echo "<td>" . htmlspecialchars($status_thread) . "</td>";
                                        echo "<td>";

                                        // Tombol Chat (Hanya jika status Open)
                                        if ($status_thread == 'Open') {
                                            echo "<a href='chatting.php?thread_id=" . urlencode($thread_id) . "&group_id=" . urlencode($group_id) . "' class='detail-event-btn'>Chat</a>";
                                        } else {
                                            echo "<span style='color: gray;'>Closed</span>";
                                        }
                                        echo "</td>";

                                        // Tombol Tutup (Hanya untuk pembuat thread)
                                        if ($pembuat == $logged_in_username) {
                                            echo "<td>
                                            <button class='delete-event-btn' type='button' 
                                            onclick=\"if(confirm('Yakin ingin menutup thread ini?')) {
                                                window.location.href='close-thread.php?thread_id=" . urlencode($thread_id) . "&group_id={$group_id}';
                                            }\">
                                            Tutup
                                            </button>
                                        </td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                        echo "</tr>";
                                    }
                                    echo "</tbody></table>";
                                } else {
                                    echo "<p>Belum ada thread dalam grup ini.</p>";
                                }

                                // Tombol Tambah Thread
                                if ($group_id > 0 && $can_edit_group): ?>
                                    <form action='insert-thread.php' method='get' style="margin-top: 15px;">
                                        <input type='hidden' name='idgrup' value='<?= htmlspecialchars($group_id) ?>'>
                                        <button type='submit'>Tambah Thread</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                <?php
                }
            }
                ?>
                    </div>

                    <?php
                    // Tampillin Tambah Anggota kalo yg login itu pemilik grup
                    if ($group && $logged_in_username === $group['username_pembuat']) : ?>
                        <div class="content-box add-member-box" style="margin-right: 20%;">
                            <h2>Tambah Anggota</h2>
                            <label for="search-member">Cari Mahasiswa:</label>
                            <input type="text" name="search-member" id="search-member" placeholder="Masukkan NRP ...">
                            <div id="search-results">
                                <?php
                                require_once("class/mahasiswa.php");
                                $mhsObj = new mahasiswa($mysqli);

                                // Ambil 10 mahasiswa yang belum jadi member grup ini
                                $nonMembers = $mhsObj->getNonMembers($group_id);

                                if (!empty($nonMembers)) {
                                    echo "<ul class='member-default-list'>";

                                    foreach ($nonMembers as $student) {
                                        $nrp = htmlspecialchars($student['nrp']);
                                        $nama = htmlspecialchars($student['nama']);
                                        $username = htmlspecialchars($student['username']);

                                        echo "<li id='student-$nrp'>";
                                        echo "<div class='member-item-flex'>";
                                        echo "$nama ($nrp)";
                                        echo "<button class='add-member-btn' 
                data-nrp='$nrp' 
                data-nama='$nama' 
                data-username='$username'>Tambah</button>";
                                        echo "</div>";
                                        echo "</li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "<p>Tidak ada mahasiswa baru untuk ditambahkan.</p>";
                                }
                                ?>
                            </div>

                        </div>
                    <?php endif; ?>

        </div>

        <?php
        if ($can_edit_group):
        ?>
            <div id="editGroupModal" style="
        display: none; /* Sembunyikan secara default */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5); /* Background gelap */
    ">
                <div class="modal-content" style="
            background-color: #fefefe;
            margin: 10% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        ">
                    <h3>Edit Grup</h3>

                    <label>Nama Grup:</label>
                    <input type="text" id="editGroupName" value="<?= htmlspecialchars($group['nama'] ?? '') ?>" style="width:100%; margin-bottom:10px;">

                    <label>Deskripsi:</label>
                    <textarea id="editGroupDesc" style="width:100%; margin-bottom:10px;"><?= htmlspecialchars($group['deskripsi'] ?? '') ?></textarea>

                    <label>Jenis Grup:</label>
                    <select id="editGroupType" style="width:100%; margin-bottom:20px;">
                        <option value="Publik" <?= ($group['jenis'] ?? '') == "Publik" ? "selected" : "" ?>>Publik</option>
                        <option value="Privat" <?= ($group['jenis'] ?? '') == "Privat" ? "selected" : "" ?>>Privat</option>
                    </select>

                    <button id="saveGroupEdit" class="btn-modal" style="padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Simpan</button>
                    <button id="closeModal" class="btn-modal" style="padding: 10px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">Batal</button>
                </div>

            </div>
        <?php endif; ?>
        <script>
            const currentUsername = '<?= $logged_in_username ?>';
            const groupCreator = '<?= htmlspecialchars($group['username_pembuat'] ?? '') ?>';
            const isAdmin = <?= $is_admin == 1 ? 'true' : 'false' ?>;
            const currentUserRole = '<?= $logged_in_role ?>';


            $(function() {

                // Sidebar toggle
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

                // Tab switching
                $('#tab-menu button').on('click', function() {
                    $('#tab-menu button').removeClass('active');
                    $('.tab-content-item').removeClass('active');
                    $(this).addClass('active');

                    const tab = $(this).data('tab');
                    $('#tab-content-' + tab).addClass('active');
                });

                // Hapus member
                $(document).on('click', '.remove-member-btn', function() {
                    const button = $(this);
                    const username = button.data('username');
                    const groupId = <?= $group_id ?? 'null' ?>;
                    const nrp = button.data('nrp');
                    const nama = button.data('nama');

                    if (!groupId) {
                        alert("Group ID tidak valid.");
                        return;
                    }

                    // Tambahkan konfirmasi
                    if (!confirm(`Yakin ingin mengeluarkan ${nama} dari grup?`)) {
                        return;
                    }

                    $.ajax({
                        url: 'remove-member.php',
                        method: 'POST',
                        data: {
                            username: username,
                            group_id: groupId,
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Hapus elemen member dari daftar
                                button.closest('.member-item').remove();
                                alert("Anggota berhasil dihapus");

                                // Tambahin balik ke list tambah member kalo mahasiswa
                                $('.member-default-list').append(
                                    '<li id="student-' + nrp + '">' +
                                    '<div class="member-item-flex">' +
                                    nama + ' (' + nrp + ')' +
                                    '<button class="add-member-btn" data-nrp="' + nrp + '" data-nama="' + nama + '" data-username="' + username + '">Tambah</button>' +
                                    '</div>' +
                                    '</li>'
                                );


                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            alert("Terjadi kesalahan saat menghapus anggota.");
                        }
                    });
                });

                // Buat search member by NRP
                const defaultMemberList = $("#search-results").html();

                $("#search-member").on("input", function() {
                    const nrp = $(this).val().trim();
                    const groupId = <?= (int)($group_id ?? 0) ?>;

                    // ini misal kosong, isi pake default
                    if (nrp === "") {
                        $("#search-results").html(defaultMemberList);
                        return; // Stop, jangan jalankan AJAX
                    }

                    $.get("search-member.php?nrp=" + encodeURIComponent(nrp) + "&group_id=" + groupId, function(data) {
                        $("#search-results").html(data);
                    });
                });



                // Button add member ke group
                $(document).on('click', '.add-member-btn', function() {
                    const button = $(this); // simpan tombol yang diklik
                    const nrp = $(this).data('nrp');
                    const username = $(this).data('username'); // Ambil username dari data-attribute
                    const groupId = <?= $group_id ?? 'null' ?>;
                    const nama = $(this).data('nama');

                    if (!groupId) {
                        alert("Group ID tidak valid.");
                        return;
                    }

                    $.ajax({
                        url: 'add-member.php',
                        method: 'POST',
                        data: {
                            username: username,
                            nrp: nrp,
                            nama: nama,
                            group_id: groupId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                let tombolHapus = '';
                                // Hanya tampilin tombol hapus kalo user adalah pembuat grup
                                if (currentUsername === groupCreator) {
                                    tombolHapus = '<button class="remove-member-btn" ' +
                                        'data-username="' + username + '" ' +
                                        'data-nrp="' + nrp + '" ' +
                                        'data-nama="' + nama + '">Hapus</button>';
                                }

                                // Tambahin ke daftar anggota
                                $('.member-list').append(
                                    '<div class="member-item" id="student-' + nrp + '">' +
                                    '<div class="member-item-flex">' +
                                    nama + ' (' + nrp + ')' +
                                    tombolHapus +
                                    '</div>' +
                                    '</div>'
                                );
                                button.closest('li').remove(); // hapus dari default-member-list


                                alert("Anggota berhasil ditambahkan");
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            alert("Terjadi kesalahan saat menambahkan anggota.");
                        }
                    });
                });


                // Delete Group
                $(document).on('click', '.delete-group-btn', function() {
                    const button = $(this);
                    const groupId = button.data('id');

                    if (confirm('Yakin ingin menghapus group ini?')) {
                        $.ajax({
                            url: 'delete-group.php',
                            method: 'GET',
                            data: {
                                id: groupId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    alert("Group berhasil dihapus");
                                    window.location.href = "data-group.php";
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText);
                                alert("Terjadi kesalahan saat menghapus group.");
                            }
                        });
                    }
                });

                //leave group 
                // keluar dari grup
                $(document).on('click', '.leave-group-btn', function() {
                    const button = $(this);
                    const groupId = button.data('id');
                    const username = button.data('username');

                    if (confirm('Yakin ingin keluar dari group ini?')) {
                        $.ajax({
                            url: 'leave-group.php',
                            method: 'GET',
                            data: {
                                id: groupId,
                                username: username
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    alert("Anda berhasil keluar dari group ini");
                                    window.location.href = "data-group.php";
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText);
                                alert("Terjadi kesalahan saat keluar group.");
                            }
                        });
                    }
                });


                // Tampilin Modal saat tombol Edit diklik
                $('#btnEditGroup').on('click', function() {
                    // Pastikan nilai di modal sesuai dengan data yang sedang ditampilkan
                    $('#editGroupName').val($('#groupTitle').text().trim());
                    $('#editGroupDesc').val($('#desc-group').text().trim());
                    $('#editGroupType').val($('#jenis-group').text().trim());

                    $('#editGroupModal').show();
                });

                $('#closeModal').on('click', function() {
                    $('#editGroupModal');
                });

                // pake form temporer untuk redirect post PHP
                $('#saveGroupEdit').on('click', function() {
                    // Buat form temporer untuk submit POST agar prosesnya ditangani oleh PHP di atas
                    const form = $('<form action="detail-group.php?id=<?= $group_id ?>" method="POST"></form>');
                    form.append('<input type="hidden" name="action" value="update_group">');
                    form.append('<input type="hidden" name="idgrup" value="<?= $group_id ?>">');
                    form.append('<input type="hidden" name="nama_grup" value="' + $('#editGroupName').val() + '">');
                    form.append('<input type="hidden" name="deskripsi" value="' + $('#editGroupDesc').val() + '">');
                    form.append('<input type="hidden" name="jenis" value="' + $('#editGroupType').val() + '">');

                    $('body').append(form);
                    form.submit();
                });

                <?php if ($error_message && $edit_submitted && $can_edit_group): ?>
                    // Kalo ada error habis submit edit, dan user itu pengedit, tampilin balik modal
                    $('#editGroupModal');
                <?php endif; ?>


            });
        </script>

</body>

</html>