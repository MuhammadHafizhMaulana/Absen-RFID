<?php
session_start();
include('config.php'); // Pastikan jalur ini sesuai dengan lokasi file config.php Anda

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: loginAdmin.php");
    exit();
}

// Cek apakah data yang diperlukan ada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $nama = trim($_POST['nama']);
    $departemen = trim($_POST['departemen']);
    $posisi = trim($_POST['posisi']);
    $status = trim($_POST['status']);

    // Perbarui data pengguna di database
    $query = "UPDATE user SET nama = ?, departemen = ?, posisi = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $nama, $departemen, $posisi, $status, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data pengguna berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat memperbarui data pengguna.";
    }

    // Redirect kembali ke dashboard admin
    header("Location: admin_dashboard.php");
    exit();
} else {
    // Jika tidak ada data yang dikirim
    $_SESSION['error'] = "Data tidak valid.";
    header("Location: admin_dashboard.php");
    exit();
}