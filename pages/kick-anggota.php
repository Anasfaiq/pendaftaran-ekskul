<?php
session_start();
include '../config/conn.php';

// Cek login guru
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM pendaftaran WHERE id_pendaftaran = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: anggota-ekskul.php");
exit;
?>
