<?php
include 'config.php'; // Koneksi ke database

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];

    // Ambil data dari form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['nama'];
        $departemen = $_POST['departemen'];
        $posisi = $_POST['posisi'];
        //default status 
        $status = "logout";

        // Query untuk menyimpan data pengguna baru
        $query = "INSERT INTO user (uid, nama, departemen, posisi, status) VALUES ( ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $uid,  $nama, $departemen, $posisi, $status);
        if ($stmt->execute()) {
            echo "Pendaftaran berhasil!";
            header('Location: index.php?message=daftar');
        } else {
            echo "Gagal mendaftar: " . $stmt->error;
        }
    }
} else {
    echo "UID tidak ditemukan.";
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pengguna</title>
</head>
<body>
    <h1>Daftar Pengguna Baru</h1>
    <form method="POST">
        <label>UID RFID:</label>
        <input type="text" name="uid" value="<?=$uid?>" disabled required>
        <br>
        <label>Nama:</label>
        <input type="text" name="nama" required>
        <br>
        <label>Departemen:</label>
        <input type="text" name="departemen" required>
        <br>
        <label>Posisi:</label>
        <input type="text" name="posisi" required>
        <br>
        <button type="submit">Daftar</button>
    </form>
</body>
</html>
