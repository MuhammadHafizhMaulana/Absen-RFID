<?php
session_start();
include('config.php');

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Ambil data absensi
$query = "SELECT attendance.*, user.nama, user.status
FROM attendance
INNER JOIN user ON attendance.uid = user.uid;
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi</title>
    <!-- Refresh halaman setiap 3 detik -->
    <meta http-equiv="refresh" content="3">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Data User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="attendance.php">Data Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="perhitunganWaktu.php">Perhitungan Waktu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mt-4">
        <h1 class="mb-4">Data Absensi</h1>
        <table class="table table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Nama</th>
                    <th>UID</th>
                    <th>Waktu Check-In</th>
                    <th>Waktu Check-Out</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['uid']; ?></td>
                            <td><?= $row['waktu_checkin']; ?></td>
                            <td><?= $row['waktu_checkout'] ?: '-'; ?></td>
                            <td><?= $row['status'] ?: '-'; ?></td>
                            <td><a href="deleteAttendance.php?id=<?=$row['id']?>" class="btn btn-danger">Hapus</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data absensi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
