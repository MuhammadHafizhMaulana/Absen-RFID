<?php
include 'config.php'; // Koneksi ke database

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];

    // Cek apakah user dengan UID yang diberikan sedang dalam status check-in
    $query = $query = "SELECT * FROM attendance WHERE uid = ? AND waktu_checkin IS NULL";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika ada data dengan check-in tetapi belum check-out, maka lakukan check-out
        $updateQuery = "UPDATE attendance SET waktu_checkout = NOW() WHERE uid = ? AND waktu_checkout IS NULL";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('s', $uid);
        if ($stmt->execute()) {
            echo "Check-out berhasil!";
        } else {
            echo "Gagal melakukan check-out.";
        }
    } else {
        // Jika tidak ada data dengan check-in, maka lakukan check-in
        $insertQuery = "INSERT INTO attendance (uid, waktu_checkin) VALUES (?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param('s', $uid);
        if ($stmt->execute()) {
            echo "Check-in berhasil!";
        } else {
            echo "Gagal melakukan check-in.";
        }
    }
} else {
    echo "UID tidak ditemukan.";
}
?>
