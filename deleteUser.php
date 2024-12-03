<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id']);

    $query = "DELETE FROM user WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header('Location: admin_dashboard.php?msg=UserDeleted');
    } else {
        header('Location: admin_dashboard.php?msg=DeleteFailed');
    }
    exit();
}
?>
