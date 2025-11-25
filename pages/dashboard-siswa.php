<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

// Ambil data pendaftaran terakhir
$query_pendaftaran = "
  SELECT ekskul.nama_ekskul, pendaftaran.status, pendaftaran.tanggal_daftar
  FROM pendaftaran
  LEFT JOIN ekskul ON pendaftaran.id_ekskul = ekskul.id_ekskul
  WHERE pendaftaran.id_user = '$id_user'
  ORDER BY pendaftaran.id_pendaftaran DESC
  LIMIT 1
";
$result_pendaftaran = mysqli_query($conn, $query_pendaftaran);
$pendaftaran = mysqli_fetch_assoc($result_pendaftaran);

// Kalau belum daftar ekskul sama sekali
if (!$pendaftaran) {
    $pendaftaran = [
        'nama_ekskul' => 'Belum mendaftar ekskul',
        'status' => 'Belum terdaftar',
        'tanggal_daftar' => null
    ];
}

// Hitung total ekskul yang diikuti (semua status)
$count_query = "SELECT COUNT(*) AS total FROM pendaftaran WHERE id_user = '$id_user'";
$count_result = mysqli_query($conn, $count_query);
$count_data = mysqli_fetch_assoc($count_result);
$total_ekskul = $count_data['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Siswa</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-800 via-blue-800 to-gray-400 text-white">
  <div class="flex flex-col items-center justify-between min-h-screen" id="mainWrapper">
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 transform -translate-x-full transition-transform duration-300 shadow-lg z-40 bg-white">
      <nav class="p-6 text-center">
        <img src="../assets/logo2.jpg" alt="Logo SMK Kesuma Bangsa 2" class="w-20 h-20 rounded-xl mb-4 mx-auto">
        <h3 class="text-slate-800 font-bold mb-4">Menu</h3>
        <a id="closeSidebar" class="fixed top-4 right-4 cursor-pointer">
          <img src="../assets/close.png" class="w-3 h-3" alt="">
        </a>
        <ul class="space-y-3">
          <li><a class="text-slate-700 hover:text-slate-900" href="daftar-ekskul.php">Daftar Ekskul</a></li>
          <li><a class="text-slate-700 hover:text-slate-900" href="lihat-ekskul.php">Lihat Ekskul Saya</a></li>
          <li><a class="text-slate-700 hover:text-red-600" href="../logout.php">Logout</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black/40 opacity-0 pointer-events-none transition-opacity duration-300 z-30"></div>

    <!-- Navbar -->
    <div class="nav flex flex-row items-center justify-between w-full p-4">
      <div class="flex items-center gap-3">
        <button id="menuToggle" aria-label="Buka menu" class="p-2 rounded-md text-white bg-transparent hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M3 6h18" stroke="white" stroke-width="2" stroke-linecap="round" />
            <path d="M3 12h18" stroke="white" stroke-width="2" stroke-linecap="round" />
            <path d="M3 18h18" stroke="white" stroke-width="2" stroke-linecap="round" />
          </svg>
        </button>

        <div class="logo flex items-center gap-2 text-xl font-bold text-shadow-lg">
          <img src="../assets/logo2.jpg" style="border-radius: 9px;" alt="SMK Kesuma Bangsa 2" width="50" height="50">
          SMK Kesuma Bangsa 2
        </div>
      </div>
    </div>

    <div class="w-full p-6 text-center bg-white/10 mt-4 backdrop-blur-md shadow-md">
      <h1 class="text-3xl font-bold mb-2">Selamat Datang, <span class="text-blue-300"><?= htmlspecialchars($siswa['nama'])?></span> 👋</h1>
      <p class="text-sm text-slate-300">Tetap semangat belajar dan berprestasi di SMK Kesuma Bangsa 2!</p>
    </div>
    
    <!-- Main Content -->
    <div class="body transition-transform duration-300 w-full max-w-7xl p-6 grid grid-cols-1 lg:grid-cols-3 gap-6" id="mainContent">

      <!-- Kiri -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Data Siswa -->
        <div class="bg-white/10 p-6 rounded-2xl border border-white/10 shadow-xl">
          <h3 class="text-xl font-semibold mb-4">📘 Data Siswa</h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white/10 p-4 rounded-xl">
              <p class="text-sm text-slate-300">NAMA</p>
              <p class="text-xl font-bold"><?= htmlspecialchars($siswa['nama']) ?></p>
            </div>
            <div class="bg-white/10 p-4 rounded-xl">
              <p class="text-sm text-slate-300">NIS</p>
              <p class="text-xl font-bold"><?= htmlspecialchars($siswa['nis']) ?></p>
            </div>
            <div class="bg-white/10 p-4 rounded-xl">
              <p class="text-sm text-slate-300">KELAS</p>
              <p class="text-xl font-bold"><?= htmlspecialchars($siswa['kelas']) ?></p>
            </div>
          </div>
        </div>

        <!-- Status Ekskul -->
        <div class="bg-white/10 p-6 rounded-xl border border-white/10 shadow-md relative">
          <h3 class="text-xl font-bold mb-3">🎯 Status Ekskul</h3>
          <p><strong>Ekskul:</strong> <?= htmlspecialchars($pendaftaran['nama_ekskul']) ?></p>
          <p><strong>Status:</strong> 
            <span class="px-3 py-1 rounded-full text-sm font-semibold 
              <?= $pendaftaran['status'] === 'diterima' ? 'bg-green-400/20 text-green-300' : 
                ($pendaftaran['status'] === 'pending' ? 'bg-yellow-400/20 text-yellow-300' : 
                'bg-red-400/20 text-red-300') ?>">
              <?= ucfirst($pendaftaran['status']) ?>
            </span>
          </p>

          <!-- Tombol Lihat Selengkapnya -->
          <div class="mt-4">
            <a href="lihat-ekskul.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">Lihat Selengkapnya</a>
          </div>
        </div>

        <!-- Menu -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
          <a href="daftar-ekskul.php" class="group p-6 bg-white/10 rounded-2xl hover:bg-white/20 transition-all border border-white/10 shadow-md">
            <h3 class="text-lg font-semibold">📝 Daftar Ekskul</h3>
            <p class="text-sm text-slate-300">Daftar ekskul baru kalau kamu belum punya.</p>
          </a>
          <a href="lihat-ekskul.php" class="group p-6 bg-white/10 rounded-2xl hover:bg-white/20 transition-all border border-white/10 shadow-md">
            <h3 class="text-lg font-semibold">📋 Lihat Ekskul</h3>
            <p class="text-sm text-slate-300">Lihat semua ekskul yang kamu ikuti.</p>
          </a>
        </div>
      </div>

      <!-- Kanan -->
      <aside class="space-y-6">
        <!-- Statistik -->
        <div class="bg-white/10 rounded-2xl p-5 border border-white/10 shadow-md">
          <h3 class="text-lg font-semibold mb-4">📊 Statistik Kamu</h3>
          <ul class="space-y-2 text-slate-200">
            <li>✅ Ekskul diikuti: <span class="text-blue-300 font-semibold"><?= $total_ekskul ?></span></li>
            <li>⭐ Status keaktifan: 
              <span class="font-semibold <?= $pendaftaran['status'] === 'diterima' ? 'text-green-300' : 'text-yellow-300' ?>">
                <?= ucfirst($pendaftaran['status']) ?>
              </span>
            </li>
            <li>🔥 Terdaftar sejak: 
              <span class="text-indigo-300 font-semibold">
                <?= $pendaftaran['tanggal_daftar'] ? date('d M Y', strtotime($pendaftaran['tanggal_daftar'])) : '—' ?>
              </span>
            </li>
          </ul>
        </div>

        <!-- Pengumuman -->
        <div class="bg-white/10 rounded-2xl p-5 border border-white/10 shadow-md">
          <h3 class="text-lg font-semibold mb-3">📅 Pengumuman</h3>
          <ul class="text-slate-300 space-y-3 text-sm">
            <li>🏆 <strong>Turnamen Futsal</strong> - 10 November 2025</li>
            <li>🎨 Lomba Poster Digital - 15 November 2025</li>
            <li>🗓️ Ujian Akhir Semester - 2 Desember 2025</li>
          </ul>
        </div>

        <!-- Motivasi -->
        <?php 
          $quotes = [
            "Setiap baris kode yang kamu tulis hari ini, bisa jadi masa depan yang kamu bangun besok.",
            "Jangan takut error, karena dari situ kamu belajar jadi developer sejati.",
            "Debugging bukan kegagalan, tapi seni memahami pikiran sendiri 😎",
            "Sedikit-sedikit coding, lama-lama jadi fullstack 🔥"
          ];
          $motivasi = $quotes[array_rand($quotes)];
        ?>
        <div class="bg-gradient-to-r from-indigo-500/30 to-purple-500/20 rounded-2xl p-5 border border-white/10 shadow-md">
          <h3 class="text-lg font-semibold mb-2">💡 Motivasi Hari Ini</h3>
          <p class="text-slate-100 italic text-sm">"<?= htmlspecialchars($motivasi) ?>"</p>
        </div>
      </aside>
    </div>

    <footer class="mt-12 mb-4 text-center text-sm text-slate-400 border-t border-white/10 pt-4">
      <p class="animate-pulse text-slate-300">&copy; 2025 SMK Kesuma Bangsa 2.</p>
    </footer>

    <!-- Sidebar Script -->
    <script>
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const toggle = document.getElementById('menuToggle');
      const closeSidebar = document.getElementById('closeSidebar');

      toggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('opacity-0');
        overlay.classList.toggle('pointer-events-none');
      });

      overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('opacity-0');
        overlay.classList.add('pointer-events-none');
      });

      // ✅ Tombol X buat nutup sidebar
      closeSidebar.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('opacity-0');
        overlay.classList.add('pointer-events-none');
      });
    </script>

  </div>
</body>
</html>
