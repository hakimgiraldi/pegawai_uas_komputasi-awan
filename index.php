<?php
session_start();

if (!isset($_SESSION['pegawai'])) {
    $_SESSION['pegawai'] = [];
}

$success = false;

// Tambah atau Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $jabatan = $_POST['jabatan'];

    $data = [
        'nama' => htmlspecialchars($nama),
        'nip' => htmlspecialchars($nip),
        'jabatan' => htmlspecialchars($jabatan)
    ];

    if (isset($_POST['edit_index'])) {
        $_SESSION['pegawai'][$_POST['edit_index']] = $data;
        $success = 'edit';
    } else {
        $_SESSION['pegawai'][] = $data;
        $success = 'add';
    }
}

// Hapus
if (isset($_GET['hapus'])) {
    $i = $_GET['hapus'];
    unset($_SESSION['pegawai'][$i]);
    $_SESSION['pegawai'] = array_values($_SESSION['pegawai']);
    $success = 'delete';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Pegawai</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg: #f5f3ed;
            --text: #2c3e50;
            --card: #ffffff;
            --border: #2c3e50;
            --table-header: #ecf0f1;
            --button-bg: #2c3e50;
            --button-hover: #1a252f;
            --input-bg: #ffffff;
            --input-border: #ccc;
            --row-even: #fafafa;
        }

        body.dark {
            --bg: #1c1c1c;
            --text: #f0f0f0;
            --card: #2c2c2c;
            --border: #4a90e2;
            --table-header: #3a3a3a;
            --button-bg: #4a90e2;
            --button-hover: #357ABD;
            --input-bg: #3a3a3a;
            --input-border: #555;
            --row-even: #2e2e2e;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            transition: 0.3s ease;
        }

        .container {
            max-width: 850px;
            margin: 50px auto;
            background: var(--card);
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 15px;
            border-left: 10px solid var(--border);
        }

        h1, h2 {
            text-align: center;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .form input {
            padding: 12px;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            font-size: 16px;
            background-color: var(--input-bg);
            color: var(--text);
        }

        .form button {
            padding: 12px;
            background-color: var(--button-bg);
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form button:hover {
            background-color: var(--button-hover);
        }

        .dark-toggle {
            float: right;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: -30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th {
            background-color: var(--table-header);
            color: var(--text);
        }

        td {
            background-color: var(--card);
            color: var(--text);
        }

        tr:nth-child(even) {
            background-color: var(--row-even);
        }

        th, td {
            padding: 14px;
            text-align: left;
        }

        .btn-action {
            text-decoration: none;
            padding: 6px 12px;
            background-color: var(--button-bg);
            color: white;
            border-radius: 5px;
            font-size: 14px;
            margin-right: 5px;
        }

        .btn-action:hover {
            background-color: var(--button-hover);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dark-toggle" onclick="toggleDark()">🌙/☀️ Mode</div>
        <h1>Dashboard Admin</h1>
        <h2><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Data Pegawai</h2>

        <form method="POST" class="form">
            <?php if (isset($_GET['edit'])): 
                $e = $_SESSION['pegawai'][$_GET['edit']];
            ?>
                <input type="hidden" name="edit_index" value="<?= $_GET['edit'] ?>">
                <input type="text" name="nama" placeholder="Nama Pegawai" value="<?= $e['nama'] ?>" required>
                <input type="text" name="nip" placeholder="NIP" value="<?= $e['nip'] ?>" required>
                <input type="text" name="jabatan" placeholder="Jabatan" value="<?= $e['jabatan'] ?>" required>
            <?php else: ?>
                <input type="text" name="nama" placeholder="Nama Pegawai" required>
                <input type="text" name="nip" placeholder="NIP" required>
                <input type="text" name="jabatan" placeholder="Jabatan" required>
            <?php endif; ?>
            <button type="submit"><?= isset($_GET['edit']) ? 'Update' : 'Simpan' ?></button>
        </form>

        <div class="table-container">
            <h2>Daftar Pegawai</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['pegawai'] as $index => $p): ?>
                        <tr>
                            <td><?= $p['nama'] ?></td>
                            <td><?= $p['nip'] ?></td>
                            <td><?= $p['jabatan'] ?></td>
                            <td>
                                <a href="?edit=<?= $index ?>" class="btn-action">Edit</a>
                                <a href="?hapus=<?= $index ?>" class="btn-action" onclick="return confirmHapus(event)">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleDark() {
            document.body.classList.toggle('dark');
            localStorage.setItem('darkmode', document.body.classList.contains('dark') ? '1' : '0');
        }

        function confirmHapus(event) {
            event.preventDefault();
            const href = event.currentTarget.getAttribute("href");

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data ini akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya, hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        }

        window.onload = function() {
            // Restore dark mode
            if (localStorage.getItem('darkmode') === '1') {
                document.body.classList.add('dark');
            }

            <?php if ($success == 'add'): ?>
            Swal.fire('Berhasil!', 'Data pegawai berhasil ditambahkan.', 'success');
            <?php elseif ($success == 'edit'): ?>
            Swal.fire('Diperbarui!', 'Data pegawai berhasil diubah.', 'success');
            <?php elseif ($success == 'delete'): ?>
            Swal.fire('Dihapus!', 'Data pegawai berhasil dihapus.', 'success');
            <?php endif; ?>
        };
    </script>
</body>
</html>
