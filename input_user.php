<?php
include 'config.php'; // Koneksi ke database

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];

    // Ambil data dari form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['nama'];
        $departemen = $_POST['departemen'];
        $posisi = $_POST['posisi'];
        // Default status 
        $status = "logout";

        // Query untuk menyimpan data pengguna baru
        $query = "INSERT INTO user (uid, nama, departemen, posisi, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $uid, $nama, $departemen, $posisi, $status);
        if ($stmt->execute()) {
            header('Location: index.php?message=daftar');
            exit();
        } else {
            $error_message = "Gagal mendaftar: " . $stmt->error;
        }
    }
} else {
    echo "UID tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <!-- Link ke Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Daftar Pengguna Baru</h1>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label>UID RFID:</label>
                <input type="text" name="uid" value="<?= htmlspecialchars($uid) ?>" class="form-control" disabled required>
            </div>
            <div class="form-group">
                <label>Nama:</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Departemen:</label>
                <input type="text" name="departemen" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Posisi:</label>
                <input type="text" name="posisi" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Daftar</button>
        </form>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-link">Kembali ke Halaman Utama</a>
        </div>
    </div>

    <!-- Link ke Bootstrap JS dan jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>