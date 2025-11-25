<?php
session_start();
include '../config/conn.php';

// 🔒 Cek login dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../login.php');
    exit;
}

// 🧠 Ambil data guru dari user login
$id_user = $_SESSION['id_user'];
$stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$guru = mysqli_fetch_assoc($result);

// 📊 Ambil ringkasan data
$totalSiswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM siswa"))['total'];
$totalEkskul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ekskul"))['total'];
$totalPendaftar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pendaftaran"))['total'];

// 🧾 Ambil daftar ekskul + jumlah pendaftar
$ringkasanEkskul = mysqli_query($conn, "
    SELECT e.nama_ekskul, COUNT(p.id_pendaftaran) AS jumlah_siswa
    FROM ekskul e
    LEFT JOIN pendaftaran p ON e.id_ekskul = p.id_ekskul
    GROUP BY e.id_ekskul
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Guru</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#2563eb',
            secondary: '#0f172a',
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 text-white min-h-screen">

  <!-- SIDEBAR -->
  <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 bg-white/10 backdrop-blur-2xl border-r border-white/10 shadow-xl z-40">
    <div class="p-6 text-center">
      <img src="../assets/logo2.jpg" alt="Logo" class="w-20 h-20 rounded-xl mx-auto mb-3">
      <h3 class="text-lg font-bold mb-6">Menu Guru</h3>
      <ul class="space-y-3 text-sm font-medium">
        <li><a href="anggota-ekskul.php" class="block py-2 px-4 rounded-md hover:bg-white/10 transition">👨‍🎓 Anggota Ekskul</a></li>
        <li><a href="data-pendaftaran.php" class="block py-2 px-4 rounded-md hover:bg-white/10 transition">📝 Data Pendaftaran</a></li>
        <li><a href="../logout.php" class="block py-2 px-4 rounded-md hover:bg-red-500/20 text-red-400 transition">🚪 Logout</a></li>
      </ul>
    </div>
  </aside>

  <!-- OVERLAY (buat HP) -->
  <div id="overlay" class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 z-30 lg:hidden"></div>

  <!-- NAVBAR -->
  <header class="flex items-center justify-between w-full p-4 bg-white/10 backdrop-blur-md border-b border-white/10 lg:pl-72">
    <div class="flex items-center gap-3">
      <button id="menuToggle" class="p-2 rounded-md text-white hover:bg-white/10 focus:ring-2 focus:ring-white lg:hidden">
        <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round">
          <path d="M3 6h18M3 12h18M3 18h18"/>
        </svg>
      </button>
      <span class="text-xl font-bold">Dashboard Guru</span>
    </div>
    <div class="text-sm text-slate-300">👋 Hai, <strong><?= htmlspecialchars($guru['username']); ?></strong></div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="p-6 lg:pl-72">
    <!-- Hero Section -->
    <section class="text-center mb-10">
      <h1 class="text-3xl font-bold mb-2">Selamat Datang, <?= htmlspecialchars($guru['username']) ?> 👋</h1>
      <p class="text-slate-400">Kelola ekskul dan pantau pendaftar dengan mudah di sini.</p>
    </section>

    <!-- Ringkasan Data -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
      <div class="bg-white/10 backdrop-blur-md p-5 rounded-xl border border-white/10 shadow-md hover:scale-[1.02] transition">
        <h3 class="text-lg font-semibold mb-2">👨‍🎓 Total Siswa</h3>
        <p class="text-3xl font-bold text-blue-400"><?= $totalSiswa ?></p>
      </div>
      <div class="bg-white/10 backdrop-blur-md p-5 rounded-xl border border-white/10 shadow-md hover:scale-[1.02] transition">
        <h3 class="text-lg font-semibold mb-2">📘 Total Ekskul</h3>
        <p class="text-3xl font-bold text-green-400"><?= $totalEkskul ?></p>
      </div>
      <div class="bg-white/10 backdrop-blur-md p-5 rounded-xl border border-white/10 shadow-md hover:scale-[1.02] transition">
        <h3 class="text-lg font-semibold mb-2">📝 Total Pendaftar</h3>
        <p class="text-3xl font-bold text-yellow-400"><?= $totalPendaftar ?></p>
      </div>
    </section>

    <!-- Ringkasan Ekskul -->
    <section class="bg-white/10 backdrop-blur-md rounded-xl border border-white/10 shadow-md p-6">
      <h2 class="text-xl font-bold mb-4 text-center">📊 Jumlah Siswa per Ekskul</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-white/10 rounded-lg">
          <thead class="bg-white/20 text-white uppercase text-xs">
            <tr>
              <th class="px-4 py-2 text-left">Nama Ekskul</th>
              <th class="px-4 py-2 text-left">Jumlah Siswa</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($ringkasanEkskul)) : ?>
              <tr class="border-t border-white/10 hover:bg-white/10 transition">
                <td class="px-4 py-2"><?= htmlspecialchars($row['nama_ekskul']) ?></td>
                <td class="px-4 py-2"><?= $row['jumlah_siswa'] ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <div class="text-center mt-4">
        <a href="data-pendaftaran.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
          📄 Lihat Selengkapnya
        </a>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="text-center py-6 text-slate-400 text-sm mt-10 lg:pl-72 border-t border-white/10">
    &copy; <?= date('Y'); ?> SMK Kesuma Bangsa 2. Dashboard Guru.
  </footer>

  <!-- Sidebar Script -->
  <script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    function openSidebar(){
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('opacity-0','pointer-events-none');
      overlay.classList.add('opacity-100');
    }

    function closeSidebar(){
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('opacity-0','pointer-events-none');
      overlay.classList.remove('opacity-100');
    }

    menuToggle?.addEventListener('click', () => {
      sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
    });

    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => { if(e.key === 'Escape') closeSidebar(); });
  </script>
</body>
</html>
