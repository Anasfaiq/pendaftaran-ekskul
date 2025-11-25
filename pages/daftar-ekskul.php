<?php
session_start();
include '../config/conn.php';

// pastiin cuma siswa yang bisa akses
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}

// ambil data user yang login
$id_user = $_SESSION['id_user'];
$query = mysqli_query($conn, "
  SELECT siswa.nama, siswa.nis, siswa.kelas
  FROM siswa
  JOIN users ON siswa.id_user = users.id_user
  WHERE siswa.id_user = '$id_user'
");
$siswa = mysqli_fetch_assoc($query);

// ambil semua data ekskul dari tabel ekskul
$queryEkskul = mysqli_query($conn, "SELECT * FROM ekskul ORDER BY nama_ekskul ASC");

// hitung jumlah ekskul aktif (pending / diterima)
$cekAktif = mysqli_query($conn, "
  SELECT COUNT(*) AS total FROM pendaftaran 
  WHERE id_user = '$id_user' 
  AND status IN ('pending', 'diterima')
");
$dataAktif = mysqli_fetch_assoc($cekAktif);
$totalEkskulAktif = $dataAktif['total'];

// proses pendaftaran
if (isset($_POST['daftar'])) {
    $id_ekskul = mysqli_real_escape_string($conn, $_POST['id_ekskul']);

    // cek apakah udah nyentuh batas maksimal
    if ($totalEkskulAktif >= 2) {
        echo "<script>alert('Kamu sudah mencapai batas maksimal 2 ekskul aktif!'); window.location='dashboard-siswa.php';</script>";
        exit;
    }

    // cek apakah sudah daftar ekskul yang sama
    $cekDuplikat = mysqli_query($conn, "
        SELECT * FROM pendaftaran 
        WHERE id_user = '$id_user' 
        AND id_ekskul = '$id_ekskul' 
        AND status IN ('pending', 'diterima')
    ");
    if (mysqli_num_rows($cekDuplikat) > 0) {
        echo "<script>alert('Kamu sudah mendaftar ekskul ini sebelumnya!'); window.location='dashboard-siswa.php';</script>";
        exit;
    }

    // insert data ke tabel pendaftaran
    $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);
    $insert = "INSERT INTO pendaftaran (id_user, id_ekskul, alasan, status)
               VALUES ('$id_user', '$id_ekskul', '$alasan', 'pending')";
    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('Pendaftaran ekskul berhasil! Status kamu: pending'); window.location='dashboard-siswa.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Ekskul</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center">
  <div class="bg-slate-800 p-6 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4 text-center">Form Pendaftaran Ekskul</h2>

    <form method="POST">
      <div class="mb-3">
        <label class="block mb-1 text-sm text-slate-300">Nama:</label>
        <input type="text" value="<?= htmlspecialchars($siswa['nama']) ?>" disabled class="w-full px-4 py-2 rounded-md bg-slate-700 text-white">
      </div>

      <div class="mb-3">
        <label class="block mb-1 text-sm text-slate-300">NIS:</label>
        <input type="text" value="<?= htmlspecialchars($siswa['nis']) ?>" disabled class="w-full px-4 py-2 rounded-md bg-slate-700 text-white">
      </div>

      <div class="mb-3">
        <label class="block mb-1 text-sm text-slate-300">Pilih Ekskul:</label>
        <select name="id_ekskul" required class="w-full px-4 py-2 rounded-md bg-slate-700 text-white">
          <option value="">-- Pilih Ekskul --</option>
          <?php while ($row = mysqli_fetch_assoc($queryEkskul)) : ?>
            <option value="<?= $row['id_ekskul'] ?>"><?= htmlspecialchars($row['nama_ekskul']) ?> - <?= htmlspecialchars($row['pembina']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="block mb-1 text-sm text-slate-300">Alasan Bergabung:</label>
        <textarea name="alasan" rows="3" required placeholder="Kenapa kamu pengen ikut ekskul ini?" class="w-full px-4 py-2 rounded-md bg-slate-700 text-white"></textarea>
      </div>

      <button type="submit" name="daftar" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md transition">Daftar</button>
    </form>

    <!-- Pesan batas ekskul -->
    <div class="mt-6 text-center text-sm text-slate-400">
      <?php if ($totalEkskulAktif == 0): ?>
        <p>Kamu belum terdaftar di ekskul mana pun. Kamu bisa daftar hingga <b>2 ekskul</b>.</p>
      <?php elseif ($totalEkskulAktif == 1): ?>
        <p>Kamu saat ini sudah terdaftar di <b>1 ekskul aktif</b>.<br>Kamu masih bisa daftar <b>1 ekskul lagi</b>.</p>
      <?php else: ?>
        <p class="text-red-400 font-medium">Kamu sudah mencapai batas maksimal <b>2 ekskul aktif</b>.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
