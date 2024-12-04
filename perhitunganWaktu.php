<?php
session_start();
include('config.php');

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: loginAdmin.php");
    exit();
}

// Ambil data dari tabel attendance
$query = "SELECT attendance.*, user.nama
FROM attendance
JOIN user ON attendance.uid = user.uid;
";
$result = $conn->query($query);
$attendanceData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Perhitungan Waktu</title>
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
                        <a class="nav-link" href="attendance.php">Data Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="perhitunganWaktu.php">Perhitungan Waktu</a>
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
        <h1 class="mb-4">Perhitungan Waktu Check-in dan Check-out</h1>

        <?php if (count($attendanceData) > 0): ?>
            <table class="table table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Nama</th>
                        <th>Waktu Check-in</th>
                        <th>Waktu Check-out</th>
                        <th>Selisih Waktu</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($attendanceData as $data): ?>
                    <?php
                    $checkinTime = new DateTime($data['waktu_checkin']);
                    $checkoutTime = new DateTime($data['waktu_checkout']);
                    $interval = $checkinTime->diff($checkoutTime);
                    $jam = $interval->h;
                    $menit = $interval->i;
                    $detik = $interval->s; // Menambahkan detik
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($data['nama']) ?></td>
                        <td><?= htmlspecialchars($data['waktu_checkin']) ?></td>
                        <td><?= htmlspecialchars($data['waktu_checkout']) ?></td>
                        <td><?= $jam ?> Jam <?= $menit ?> Menit <?= $detik ?> Detik</td> <!-- Menampilkan detik -->
                    </tr>

                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning mt-4" role="alert">
                Tidak ada data kehadiran yang ditemukan.
            </div>
        <?php endif; ?>
    </div>

    <!-- Link ke Bootstrap JS dan jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>
</html>