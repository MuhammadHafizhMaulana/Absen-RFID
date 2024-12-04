<?php
$servername = "localhost";
$dbname = "absensi";
$username = "root";
$password = "";

$api_key_value = "123456789";

$api_key = $uid = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if ($api_key == $api_key_value) {
        // Ambil data UID dari POST
        $uid = test_input($_POST["uid"]);

        // Koneksi ke database
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection Failed: " . $conn->connect_error);
        }

        // Simpan UID ke tabel rfid_card
        $insertQuery = "INSERT INTO rfid_card (uid) VALUES (?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("s", $uid);

        if ($stmt->execute()) {
            echo "UID berhasil disimpan ";

            // Cek status pengguna di tabel user
            $checkQuery = "SELECT status FROM user WHERE uid = ? ORDER BY id DESC LIMIT 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $uid);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $status = $user['status'];

                if ($status === 'login') {
                    echo "Berhasil Login";
                } elseif ($status === 'logout') {
                    echo "Berhasil Checkout";
                } else {
                    echo "Status tidak valid";
                }
            } else {
                echo "UID tidak ditemukan di tabel user. Harap registrasi terlebih dahulu.";
            }

            // Tutup statement check
            $stmtCheck->close();
        } else {
            echo "Gagal menyimpan UID ke tabel rfid_card: " . $conn->error;
        }

        // Tutup statement insert
        $stmt->close();
        // Tutup koneksi
        $conn->close();
    } else {
        echo "API Key tidak sama";
    }
} else {
    echo "No data sent via HTTP POST Method";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
