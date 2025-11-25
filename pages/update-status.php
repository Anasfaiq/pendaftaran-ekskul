<?php
include '../config/conn.php';

$id = $_GET['id'];
$status = $_GET['status'];

// Kalau statusnya ditolak → langsung hapus
if ($status === 'ditolak') {
    mysqli_query($conn, "DELETE FROM pendaftaran WHERE id_pendaftaran='$id'");
} else {
    // Selain itu (misal diterima), update status aja
    mysqli_query($conn, "UPDATE pendaftaran SET status='$status' WHERE id_pendaftaran='$id'");
}

// Balik ke halaman data pendaftaran
header('Location: data-pendaftaran.php');
exit;
?>
