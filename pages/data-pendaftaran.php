<?php
session_start();
include '../config/conn.php';

// Cek login khusus guru aja
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

// Link dashboard (udah fix ke guru)
$dashboardLink = 'dashboard-guru.php';

// Query: hanya menampilkan pendaftar dari ekskul yang dibina guru login ini
$query = "
    SELECT p.*, s.nama, e.nama_ekskul
    FROM pendaftaran p
    JOIN siswa s ON p.id_user = s.id_user
    JOIN ekskul e ON p.id_ekskul = e.id_ekskul
    JOIN guru g ON e.id_ekskul = g.id_ekskul
    WHERE g.id_user = ? AND p.status = 'pending'
    ORDER BY p.id_pendaftaran DESC
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
  <title>Data Pendaftaran Ekskul</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 text-white min-h-screen">

  <!-- Navbar -->
  <header class="bg-slate-800/70 backdrop-blur-md shadow-lg sticky top-0 z-50 px-8 py-4 flex justify-between items-center border-b border-slate-700">
    <h1 class="text-xl font-semibold tracking-wide flex items-center gap-2">📝 Tabel Pendaftaran Ekskul</h1>
    <a href="<?= $dashboardLink ?>" class="text-sm text-gray-300 hover:text-white bg-slate-700/50 hover:bg-slate-600 px-3 py-1 rounded-md transition-all duration-200">
      ⬅️ Kembali
    </a>
  </header>

  <!-- Main Content -->
  <main class="p-8 flex justify-center">
    <div class="w-full max-w-6xl bg-slate-800/40 backdrop-blur-md rounded-xl shadow-xl border border-slate-700 p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold flex items-center gap-2">
          📝 <span>Data Pendaftaran Ekskul</span>
        </h2>
      </div>

      <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full text-sm border border-slate-700">
          <thead class="bg-slate-800 text-slate-300 uppercase text-xs">
            <tr>
              <th class="p-3 text-left">No</th>
              <th class="p-3 text-left">Nama Siswa</th>
              <th class="p-3 text-left">Ekskul</th>
              <th class="p-3 text-left">Alasan Bergabung</th>
              <th class="p-3 text-left">Status</th>
              <th class="p-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700">
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
              <tr class="hover:bg-slate-800/50 transition">
                <td class="p-3"><?= $no++ ?></td>
                <td class="p-3 font-medium"><?= htmlspecialchars($row['nama']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['nama_ekskul']) ?></td>
                <td class="p-3 text-slate-300"><?= htmlspecialchars($row['alasan']) ?></td>
                <td class="p-3">
                  <?php if ($row['status'] === 'diterima'): ?>
                    <span class="px-3 py-1 rounded-full text-xs bg-green-500/20 text-green-300 border border-green-500/30">Diterima</span>
                  <?php elseif ($row['status'] === 'pending'): ?>
                    <span class="px-3 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">Pending</span>
                  <?php else: ?>
                    <span class="px-3 py-1 rounded-full text-xs bg-red-500/20 text-red-300 border border-red-500/30">Ditolak</span>
                  <?php endif; ?>
                </td>
                <td class="p-3 text-center">
                  <div class="flex gap-3 justify-center">
                    <a href="update-status.php?id=<?= $row['id_pendaftaran'] ?>&status=diterima"
                      class="px-3 py-1 rounded-md text-green-400 hover:bg-green-500/20 transition font-medium">Terima</a>
                    <a href="update-status.php?id=<?= $row['id_pendaftaran'] ?>&status=ditolak"
                      class="px-3 py-1 rounded-md text-red-400 hover:bg-red-500/20 transition font-medium">Tolak</a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <?php if ($result->num_rows === 0): ?>
        <p class="text-center text-slate-400 mt-6">Belum ada data pendaftaran.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
