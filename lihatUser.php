<?php
include('config.php');  // Koneksi ke database

$query = "SELECT user.name, user.iud, absensi.check_in, absensi.check_out 
          FROM absensi 
          JOIN user ON absensi.user_id = user.id 
          ORDER BY absensi.check_in DESC";
$result = mysqli_query($conn, $query);

echo "<h2>Absensi</h2>";
echo "<table border='1'>
        <tr>
            <th>Name</th>
            <th>IUD</th>
            <th>Check-in</th>
            <th>Check-out</th>
        </tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>" . $row['nama'] . "</td>
            <td>" . $row['iud'] . "</td>
            <td>" . $row['check_in'] . "</td>
            <td>" . ($row['check_out'] ? $row['check_out'] : 'Not Checked Out') . "</td>
          </tr>";
}

echo "</table>";
?>
