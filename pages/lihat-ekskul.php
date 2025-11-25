<?php
session_start();
include '../config/conn.php';

// Cek login siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data siswa
$query_siswa = "
  SELECT siswa.nama, siswa.nis, siswa.kelas
  FROM siswa
  JOIN users ON siswa.id_user = users.id_user
  WHERE users.id_user = '$id_user'
";
$result_siswa = mysqli_query($conn, $query_siswa);
$siswa = mysqli_fetch_assoc($result_siswa);

// Ambil semua data ekskul yang diikuti siswa
$query = "
  SELECT ekskul.nama_ekskul, pendaftaran.status, pendaftaran.tanggal_daftar
  FROM pendaftaran
  LEFT JOIN ekskul ON pendaftaran.id_ekskul = ekskul.id_ekskul
  WHERE pendaftaran.id_user = '$id_user'
  ORDER BY pendaftaran.id_pendaftaran DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lihat Ekskul Saya</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-800 via-blue-800 to-gray-400 text-white min-h-screen">
  <div class="max-w-5xl mx-auto p-8">

    <!-- Header -->
    <div class="mb-8 text-center">
      <h1 class="text-3xl font-bold mb-2">📋 Ekskul yang Kamu Ikuti</h1>
      <p class="text-slate-300">Berikut daftar semua ekskul yang pernah kamu daftarkan.</p>
    </div>

    <!-- Info Siswa -->
    <div class="bg-white/10 p-5 rounded-2xl border border-white/10 shadow-md mb-6">
      <h2 class="text-xl font-semibold mb-4">👨‍🎓 Data Siswa</h2>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-slate-200">
        <p><strong>Nama:</strong> <?= htmlspecialchars($siswa['nama']) ?></p>
        <p><strong>NIS:</strong> <?= htmlspecialchars($siswa['nis']) ?></p>
        <p><strong>Kelas:</strong> <?= htmlspecialchars($siswa['kelas']) ?></p>
      </div>
    </div>

    <!-- Tabel Ekskul -->
    <div class="bg-white/10 rounded-xl border border-white/10 shadow-lg overflow-hidden">
      <table class="w-full text-sm text-left">
        <thead class="bg-slate-900/60 text-slate-300 uppercase text-xs">
          <tr>
            <th class="p-3">No</th>
            <th class="p-3">Nama Ekskul</th>
            <th class="p-3">Status</th>
            <th class="p-3">Tanggal Daftar</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1; 
          if (mysqli_num_rows($result) > 0): 
            while ($row = mysqli_fetch_assoc($result)): 
          ?>
          <tr class="border-t border-slate-700 hover:bg-white/5 transition">
            <td class="p-3 text-center"><?= $no++ ?></td>
            <td class="p-3 font-semibold text-slate-100"><?= htmlspecialchars($row['nama_ekskul']) ?></td>
            <td class="p-3">
              <span class="px-3 py-1 rounded-full text-xs font-bold 
                <?= $row['status'] === 'diterima' ? 'bg-green-400/20 text-green-300' :
                   ($row['status'] === 'pending' ? 'bg-yellow-400/20 text-yellow-300' : 'bg-red-400/20 text-red-300') ?>">
                <?= ucfirst($row['status']) ?>
              </span>
            </td>
            <td class="p-3"><?= date('d M Y', strtotime($row['tanggal_daftar'])) ?></td>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="4" class="text-center p-6 text-slate-400 italic">Belum ada data pendaftaran ekskul.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Tombol Kembali -->
    <div class="mt-6 text-center">
      <a href="dashboard-siswa.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition-all"> Kembali ke Dashboard</a>
    </div>
  </div>
</body>
</html>
