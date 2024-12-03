<?php
$servername = "localhost";
$dbname = "absensi";
$username = "root";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = test_input($_POST["uid"]);
    $nama = test_input($_POST["nama"]);

    // Koneksi ke database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }

    // Menyimpan data pengguna baru
    $insertQuery = "INSERT INTO user (uid, nama) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ss", $uid, $nama);
    if ($stmt->execute()) {
        $message = "Pendaftaran berhasil!";
    } else {
        $message = "Gagal melakukan pendaftaran.";
    }

    // Tutup koneksi
    $stmt->close();
    $conn->close();
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Kartu RFID</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
        <div class="text-center">
            <h1 class="display-4 text-primary">Pendaftaran Kartu RFID</h1>
            <p class="fs-5 text-muted">Silahkan lengkapi form berikut untuk mendaftar</p>
        </div>

        <?php
        if (isset($message)) {
            echo '<div class="alert alert-info mt-3" role="alert">' . $message . '</div>';
        }
        ?>

        <div class="mt-4">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="uid" class="form-label">UID Kartu RFID</label>
                    <input type="text" class="form-control" id="uid" name="uid" required>
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <button type="submit" class="btn btn-success">Daftar</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
