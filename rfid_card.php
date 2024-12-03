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

        // Simpan UID ke database
        $insertQuery = "INSERT INTO rfid_card (uid) VALUES (?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("s", $uid);

        if ($stmt->execute()) {
            echo "UID berhasil disimpan";
        } else {
            echo "Gagal menyimpan UID: " . $conn->error;
        }

        // Tutup koneksi
        $stmt->close();
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
