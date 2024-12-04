<?php
session_start();

// Sertakan config.php untuk koneksi database
include('config.php');

// Fungsi untuk menghapus UID dari tabel rfid_card

function delete_uid_from_database($conn, $uid) {
    $deleteQuery = "DELETE FROM rfid_card WHERE uid = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('s', $uid);
    return $stmt->execute();
}

// Mengambil UID terbaru dan ID dari tabel rfid_card
$query = "SELECT uid, id FROM rfid_card ORDER BY id DESC LIMIT 1";
$result = $conn->query($query);

// Memeriksa apakah data tersedia
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $uid = $row['uid']; // UID terbaru
    $rfid_card_id = $row['id']; // ID dari tabel rfid_card
   
    // Cek apakah UID sudah terdaftar di tabel user
    $checkQuery = "SELECT * FROM user WHERE uid = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        // UID ditemukan di tabel user (sudah terdaftar)
        $user = $checkResult->fetch_assoc();
        $status = $user['status']; // Ambil status pengguna

        // Cek apakah UID dan ID dari tabel rfid_card sama
        if ($user['uid'] === $uid && $user['id'] == $rfid_card_id) {
            // Jika UID dan ID sama, kosongkan UID
            // Hapus UID dari database
            delete_uid_from_database($conn, $row['uid']);
        } else {
            // Cek status pengguna
            if ($status === 'logout') {
                // Jika status adalah logout, lakukan check-in
                $checkinQuery = "INSERT INTO attendance (uid, waktu_checkin) VALUES (?, NOW())";
                $checkinStmt = $conn->prepare($checkinQuery);
                $checkinStmt->bind_param('s', $uid);
                if ($checkinStmt->execute()) {
                    // Update status pengguna menjadi login
                    $updateStatusQuery = "UPDATE user SET status = 'login' WHERE uid = ?";
                    $updateStatusStmt = $conn->prepare($updateStatusQuery);
                    $updateStatusStmt->bind_param('s', $uid);
                    $updateStatusStmt->execute();

                    $_SESSION['message'] = "Check-in berhasil!";
                    // Hapus UID dari database
                    delete_uid_from_database($conn, $row['uid']);
                } else {
                    $_SESSION['message'] = "Gagal melakukan check-in.";
                    // Hapus UID dari database
                    delete_uid_from_database($conn, $row['uid']);
                }
            } else if ($status === 'login') {
                // Jika status adalah login, lakukan check-out
                $checkoutQuery = "UPDATE attendance SET waktu_checkout = NOW() WHERE uid = ? AND waktu_checkout IS NULL";
                $checkoutStmt = $conn->prepare($checkoutQuery);
                $checkoutStmt->bind_param('s', $uid);
                if ($checkoutStmt->execute()) {
                    // Update status pengguna menjadi logout
                    $updateStatusQuery = "UPDATE user SET status = 'logout' WHERE uid = ?";
                    $updateStatusStmt = $conn->prepare($updateStatusQuery);
                    $updateStatusStmt->bind_param('s', $uid);
                    $updateStatusStmt->execute();

                    $_SESSION['message'] = "Check-out berhasil!";
                    // Hapus UID dari database
                    delete_uid_from_database($conn, $row['uid']);
                } else {
                    $_SESSION['message'] = "Gagal melakukan check-out.";
                    // Hapus UID dari database
                    delete_uid_from_database($conn, $row['uid']);
                }
            }
        }
    } else {
        // Jika UID tidak ditemukan di tabel user, arahkan ke halaman pendaftaran
        header("Location: input_user.php?uid=" . $uid);
        exit();
    }
} else {
    $_SESSION['message'] = "Tidak ada data UID yang ditemukan.";
}

// Menutup statement jika didefinisikan

if (isset($stmt) && $stmt) {
    $stmt->close();
}

if (isset($checkinStmt) && $checkinStmt) {
    $checkinStmt->close();
}

if (isset($checkoutStmt) && $checkoutStmt) {
    $checkoutStmt->close();
}

if (isset($updateStatusStmt) && $updateStatusStmt) {
    $updateStatusStmt->close();
}


// Menutup koneksi database

$conn->close();

// Fungsi untuk menampilkan pesan
function show_message() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-info mt-3" role="alert">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']); // Menghapus pesan setelah ditampilkan
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi RFID</title>
    <!-- Refresh halaman setiap 5 detik -->
    <meta http-equiv="refresh" content="5">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
        <div class="text-center">
            <h1 class="display-4 text-primary">Selamat Datang</h1>
            <p class="lead">di Sistem Absensi dengan RFID</p>
            <p class="fs-5 text-muted">Silahkan Tap Kartu Anda</p>
        </div>

        <?php show_message(); ?>
        <!-- Pemanggilan fungsi di sini hanya sekali -->

        <div class="mt-4">
            <img src="./aset/istockphoto-1200536941-612x612.jpg" style="height: 200px;" alt="RFID Image" class="img-fluid rounded-circle shadow">
        </div>

        <footer class="mt-5 text-center">
            <p class="text-muted">&copy; <?= date("Y"); ?> Sistem Absensi RFID</p>
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>