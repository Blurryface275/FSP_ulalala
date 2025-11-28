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

            <!-- Semua role -->
            <li><a href="change-password.php">Ubah Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>
    <!-- biar bisa flex  -->
    <div class="main-content-wrapper">
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

                            <div class="header-top">
                                <h2 class="group-title"><?= htmlspecialchars($group['nama']) ?></h2>

                                <div class="group-buttons">
                                    <?php if (
                                        $_SESSION['username'] == $group['username_pembuat'] ||
                                        (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1) ||
                                        $_SESSION['role'] == 'dosen'
                                    ) : ?>
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
                                        <?= htmlspecialchars($group['kode_pendaftaran']) ?>
                                    </span>
                                </div>
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
                                COALESCE(d.nama, m.nama, a.username) AS nama_member,
                                CASE 
                                WHEN d.npk IS NOT NULL THEN 'Dosen'
                                WHEN m.nrp IS NOT NULL THEN 'Mahasiswa'
                                ELSE 'Tidak diketahui'
                                END AS role,
                                a.username AS username_login,
                                COALESCE(d.npk, m.nrp, a.username) AS id_anggota
                                FROM member_grup mg
                                JOIN akun a ON mg.username = a.username
                                LEFT JOIN dosen d ON a.npk_dosen = d.npk
                                LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
                                WHERE mg.idgrup = ?
                                ORDER BY nama_member ASC;
                                ";

                                $stmt_members = $mysqli->prepare($query_members);
                                $stmt_members->bind_param("i", $group_id);
                                $stmt_members->execute();
                                $result_members = $stmt_members->get_result();

                                echo "<div class='member-list'>";

                                if ($result_members->num_rows > 0) {
                                    while ($member = $result_members->fetch_assoc()) {
                                        echo "<div class='member-item' id='student-" . htmlspecialchars($member['id_anggota']) . "'>";
                                        echo "<div class='member-item-flex'>";

                                        // Tampilkan nama + nrp/npk
                                        echo htmlspecialchars($member['nama_member']) . " (" . htmlspecialchars($member['id_anggota']) . ")";

                                        // Tampilkan tombol Hapus hanya jika user adalah dosen atau admin dan pembuat grup
                                        if (($_SESSION['role'] === 'dosen' || $_SESSION['isadmin'] == 1) && $group['username_pembuat'] == $_SESSION['username']) {
                                            echo "<button class='remove-member-btn'
                                                    data-username='" . htmlspecialchars($member['username_login']) . "'
                                                    data-nrp='" . htmlspecialchars($member['id_anggota']) . "'
                                                    data-nama='" . htmlspecialchars($member['nama_member']) . "'>Hapus</button>";
                                        }

                                        echo "</div>"; // tutup .member-item-flex
                                        echo "</div>"; // tutup .member-item
                                    }
                                } else {
                                    echo "<p>Belum ada anggota dalam grup ini.</p>";
                                }

                                echo "</div>"; // tutup .member-list
                                ?>
                            </div>

                            <!-- Tab Aktivitas -->
                            <div id="tab-content-activities" class="tab-content-item">
                                <h3>Event Grup</h3>

                                <?php
                                $query_activities = "SELECT judul, tanggal FROM event WHERE idgrup = ? ORDER BY tanggal DESC";
                                $stmt_activities = $mysqli->prepare($query_activities);
                                $stmt_activities->bind_param("i", $group_id);
                                $stmt_activities->execute();
                                $result_activities = $stmt_activities->get_result();

                                if ($result_activities && $result_activities->num_rows > 0) {
                                    echo "<table border='1' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
                                    echo "<thead><tr><th>Event</th><th>Tanggal & Waktu</th><th colspan='2'>Aksi</th></tr></thead><tbody>";
                                    while ($activity = $result_activities->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($activity['judul']) . "</td>";
                                        echo "<td>" . htmlspecialchars($activity['tanggal']) . "</td>";
                                        echo "<td><a href='detail-event.php?id=" . $activity['judul'] . "'>Lihat Detail</a></td>";

                                        // tombol hapus event jika dosen yang membuat grup ini
                                        if ($_SESSION['username'] == $group['username_pembuat']) {
                                            echo "<td>
                                                        <button class='delete-event-btn' type='button' 
                                                                onclick=\"if(confirm('Yakin ingin menghapus event ini?')) {
                                                                    window.location.href='delete-event.php?id={$judul}&group_id={$group_id}';
                                                                }\">
                                                            Hapus Event
                                                        </button>
                                                    </td>";
                                        } else {
                                            echo ""; // kosong jika bukan dosen yg login
                                        }

                                        echo "</tr>";
                                    }
                                    echo "</tbody></table>";
                                } else {
                                    echo "<p>Belum ada aktivitas dalam grup ini.</p>";
                                }
                                if (isset($group_id) && $group_id > 0):
                                ?>
                                    <form action='insert-event.php' method='get'>
                                        <input type='hidden' name='idgrup' value='<?= htmlspecialchars($group_id) ?>'>
                                        <button type='submit'>Tambah Event</button>
                                    </form>
                                <?php
                                endif;
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

        <div class="content-box add-member-box" style="margin-right: 20%;">
            <h2>Tambah Anggota</h2>
            <label for="search-member">Cari Mahasiswa:</label>
            <input type="text" name="search-member" id="search-member" placeholder="Masukkan NRP ...">
            <div id="search-results">
                <!-- Munculin hasil pencarian di sini -->
                <?php
                // Menampilkan 10 mahasiswa pertama 
                $query_search = "SELECT m.nrp, m.nama FROM mahasiswa m JOIN akun a ON m.nrp = a.nrp_mahasiswa LEFT JOIN member_grup mg ON a.username = mg.username AND mg.idgrup=? WHERE mg.username IS NULL ORDER BY m.nama ASC LIMIT 10";
                $result_search = $mysqli->prepare($query_search);
                $result_search->bind_param("i", $group_id);
                $result_search->execute();
                $result_search = $result_search->get_result();
                if ($result_search->num_rows > 0) {
                    echo "<ul class='member-default-list'>";

                    while ($student = $result_search->fetch_assoc()) {
                        echo "<li id='student-" . htmlspecialchars($student['nrp']) . "'>";
                        echo "<div class='member-item-flex'>";
                        echo htmlspecialchars($student['nama']) . " (" . htmlspecialchars($student['nrp']) . ")";
                        echo "<button class='add-member-btn' data-nrp='" . htmlspecialchars($student['nrp']) . "' data-nama='" . htmlspecialchars($student['nama']) . "'>Tambah</button>";
                        echo "</div>";
                        echo "</li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>

        </div>

    </div> <!-- .main-content-wrapper -->
    <script>
        const currentUsername = '<?= $_SESSION['username'] ?>';
        const groupCreator = '<?= htmlspecialchars($group['username_pembuat']) ?>';
        const isAdmin = <?= isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1 ? 'true' : 'false' ?>;
        const currentUserRole = '<?= $_SESSION['role'] ?>';


        $(function() {

            // Sidebar toggle
            $("#toggle-btn").on("click", function() {
                $("#sidebar").toggleClass("collapsed");
                $(".content-box").toggleClass("expanded");
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
                const groupId = <?= isset($group_id) ? $group_id : 'null' ?>;
                const nrp = button.data('nrp');
                const nama = button.data('nama');

                if (!groupId) {
                    alert("Group ID tidak valid.");
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
                            // Tambah di member-default-list
                            if ($('.member-default-list').length) { // tujuan ifnya buat kalo box add member tersedia soalnya box add member cuma muncul kalo dosen/admin liat detail group
                                $('.member-default-list').append(
                                    '<li id="student-' + nrp + '">' +
                                    '<div class="member-item-flex">' +
                                    nama + ' (' + nrp + ')' +
                                    '<button class="add-member-btn" data-nrp="' + nrp + '" data-nama="' + nama + '" data-username="' + username + '">Tambah</button>' +
                                    '</div>' +
                                    '</li>'
                                );
                            }

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



            // Button add member ke group
            $(document).on('click', '.add-member-btn', function() {
                const button = $(this); // simpan tombol yang diklik
                const nrp = $(this).data('nrp');
                const groupId = <?= isset($group_id) ? $group_id : 'null' ?>;
                const nama = $(this).data('nama');
                if (!groupId) {
                    alert("Group ID tidak valid.");
                    return;
                }
                $.ajax({
                    url: 'add-member.php',
                    method: 'POST',
                    data: {
                        nrp: nrp,
                        nama: nama,
                        group_id: groupId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const username = response.username; // dapatkan username dari response
                            let tombolHapus = '';
                            // Hanya tampilkan tombol hapus jika user adalah pembuat grup
                            if (currentUsername === groupCreator) {
                                tombolHapus = '<button class="remove-member-btn" ' +
                                    'data-username="' + username + '" ' +
                                    'data-nrp="' + nrp + '" ' +
                                    'data-nama="' + nama + '">Hapus</button>';
                            }

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

            // Delete Event
            $(document).on('click', '.delete-event-btn', function() {
                const button = $(this);
                const judul = button.closest('tr').find('td:first').text();
                const groupId = <?= $group_id ?>;

                if (confirm('Yakin ingin menghapus event ini?')) {
                    $.ajax({
                        url: 'delete-event.php',
                        method: 'GET',
                        data: {
                            id: judul,
                            group_id: groupId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Hapus elemen event dari daftar
                                button.closest('tr').remove();
                                alert("Event berhasil dihapus");
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            alert("Terjadi kesalahan saat menghapus event.");
                        }
                    });
                }
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
                                button.closest('tr').remove();
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


            // Buka modal
            $("#btnEditGroup").on("click", function() {
                $("#editGroupModal").fadeIn();
            });

            // Tutup modal
            $("#closeModal").on("click", function() {
                $("#editGroupModal").fadeOut();
            });

            // Simpan update
            $("#saveGroupEdit").on("click", function() {

                const groupId = $("#btnEditGroup").data("id");
                const newName = $("#editGroupName").val();
                const newDesc = $("#editGroupDesc").val();
                const newType = $("#editGroupType").val();

                $.ajax({
                    url: "update-group.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        idgroup: groupId,
                        group_name: newName,
                        description: newDesc,
                        group_type: newType
                    },
                    success: function(res) {
                        if (res.success) {

                            // update tampilan
                            $("#groupTitle").text(newName);
                            $("#desc-group").text(newDesc);
                            $("#jenis-group").text(newType);

                            $("#editGroupModal").fadeOut();
                            alert("Group berhasil diperbarui!");

                        } else {
                            alert(res.message);
                        }
                    },
                    error: function() {
                        alert("Terjadi error saat menghubungi server.");
                    }
                });
            });

        });
    </script>
    <!-- POP-UP EDIT GROUP -->
    <div id="editGroupModal">

        <div class="modal-content">
            <h3>Edit Grup</h3>

            <label>Nama Grup:</label>
            <input type="text" id="editGroupName" value="<?= htmlspecialchars($group['nama']) ?>" style="width:100%; margin-bottom:10px;">

            <label>Deskripsi:</label>
            <textarea id="editGroupDesc" style="width:100%; margin-bottom:10px;"><?= htmlspecialchars($group['deskripsi']) ?></textarea>

            <label>Jenis Grup:</label>
            <select id="editGroupType" style="width:100%; margin-bottom:10px;">
                <option value="Publik" <?= $group['jenis'] == "Publik" ? "selected" : "" ?>>Publik</option>
                <option value="Privat" <?= $group['jenis'] == "Privat" ? "selected" : "" ?>>Privat</option>
            </select>

            <button id="saveGroupEdit" class="btn-modal">Simpan</button>
            <button id="closeModal" class="btn-modal">Batal</button>
        </div>

    </div>

</body>

</html>