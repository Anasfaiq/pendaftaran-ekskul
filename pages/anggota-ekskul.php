<?php
session_start();
include '../config/conn.php';

// Cek login khusus guru aja
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data anggota dari ekskul yang dibina guru login ini
$query = "
    SELECT p.id_pendaftaran, s.nama, s.nis, s.kelas, e.nama_ekskul
    FROM pendaftaran p
    JOIN siswa s ON p.id_user = s.id_user
    JOIN ekskul e ON p.id_ekskul = e.id_ekskul
    JOIN guru g ON e.id_ekskul = g.id_ekskul
    WHERE g.id_user = ? AND p.status = 'diterima'
    ORDER BY s.nama ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Anggota Ekskul</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 text-white min-h-screen">

  <!-- Navbar -->
  <header class="bg-slate-800/70 backdrop-blur-md shadow-lg sticky top-0 z-50 px-8 py-4 flex justify-between items-center border-b border-slate-700">
    <h1 class="text-xl font-semibold tracking-wide flex items-center gap-2">👥 Anggota Ekskul</h1>
    <a href="dashboard-guru.php" 
       class="text-sm text-gray-300 hover:text-white bg-slate-700/50 hover:bg-slate-600 px-3 py-1 rounded-md transition-all duration-200">
       ⬅️ Kembali
    </a>
  </header>

  <!-- Main -->
  <main class="p-8 flex justify-center">
    <div class="w-full max-w-5xl bg-slate-800/40 backdrop-blur-md rounded-xl shadow-xl border border-slate-700 p-6">
      <h2 class="text-2xl font-bold mb-6">Daftar Anggota Ekskul</h2>

      <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full text-sm border border-slate-700">
          <thead class="bg-slate-800 text-slate-300 uppercase text-xs">
            <tr>
              <th class="p-3 text-left">No</th>
              <th class="p-3 text-left">Nama</th>
              <th class="p-3 text-left">NIS</th>
              <th class="p-3 text-left">Kelas</th>
              <th class="p-3 text-left">Ekskul</th>
              <th class="p-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700">
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
              <tr class="hover:bg-slate-800/50 transition">
                <td class="p-3"><?= $no++ ?></td>
                <td class="p-3 font-medium"><?= htmlspecialchars($row['nama']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['nis']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['kelas']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['nama_ekskul']) ?></td>
                <td class="p-3 text-center">
                  <a href="kick-anggota.php?id=<?= $row['id_pendaftaran'] ?>"
                     onclick="return confirm('Yakin mau menghapus anggota ini dari ekskul?')"
                     class="px-3 py-1 rounded-md text-red-400 hover:bg-red-500/20 transition font-medium">Kick</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <?php if ($result->num_rows === 0): ?>
        <p class="text-center text-slate-400 mt-6">Belum ada anggota di ekskul ini.</p>
      <?php endif; ?>
    </div>
  </main>

</body>
</html>
