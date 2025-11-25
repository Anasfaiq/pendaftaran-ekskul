-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 08 Nov 2025 pada 09.10
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ekskul`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ekskul`
--

CREATE TABLE `ekskul` (
  `id_ekskul` int(11) NOT NULL,
  `nama_ekskul` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ekskul`
--

INSERT INTO `ekskul` (`id_ekskul`, `nama_ekskul`) VALUES
(1, 'Pramuka'),
(2, 'Paskibra'),
(3, 'English club'),
(4, 'Tari'),
(5, 'Silat'),
(6, 'Panahan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id_guru` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_ekskul` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`id_guru`, `id_user`, `id_ekskul`) VALUES
(1, 5, 2),
(2, 8, 1),
(3, 10, 4),
(4, 11, 5),
(5, 12, 6),
(6, 13, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_pendaftaran` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_ekskul` int(11) NOT NULL,
  `alasan` text DEFAULT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','diterima','ditolak') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_pendaftaran`, `id_user`, `id_ekskul`, `alasan`, `tanggal_daftar`, `status`) VALUES
(1, 3, 1, NULL, '2025-10-31 08:22:38', 'diterima'),
(4, 7, 2, 'Saya ingin coba paskibra\r\n', '2025-11-02 06:57:22', 'diterima'),
(7, 6, 1, 'qwewqe', '2025-11-07 09:54:31', 'diterima'),
(8, 3, 2, 'sadasd', '2025-11-07 10:07:05', 'diterima');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`nis`, `nama`, `kelas`, `id_user`) VALUES
('10000001', 'Anas Faiq', 'XII PPLG', 3),
('10000002', 'Topik', 'XII RPLG', 6),
('10000003', 'Udin', 'XII PPLG', 7),
('10000004', 'Marum', 'XII PPLG', 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','siswa','guru') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(3, 'Anas', '$2y$10$ISBTZSnCBb.zaPwASZTeK.aHBvsYZa.SrVc3cpEdx5qFNM6PPI5a6', 'siswa'),
(4, 'Atmin Anas', '$2y$10$Cffg887Y1hjdZSO/A91LgebKsOyPjt09cxO3tL3c77vL35HfragmS', 'admin'),
(5, 'Tatag', '$2y$10$SbfW/ox4kjHPm.UGZcjMTORCt8QBp6Rz9Zqel5cDPGbb/Q8zH1Ye2', 'guru'),
(6, 'Topik', '$2y$10$lCLuVdmmVXocTSrrFGlzEe27l/bZWCiIjOD2ljPd3AITnmq9GzfEu', 'siswa'),
(7, 'Udin', '$2y$10$rB2MUEvSS.EeUxdvKrBkPORHRWp8JJiEnyHDqvGHPXbZXnRy.9Qsi', 'siswa'),
(8, 'Jay', '$2y$10$nZckfeMaLXa5FGGZcvq.mOQLlJyqUfuYZ5Ed2A9ZD/Z3uzGYKq3c6', 'guru'),
(9, 'Marum', '$2y$10$GD6xxBn/CctDORaj481v/.aHBvawVyG4Hr5wGJ2A5IFCJ8ltqyhFK', 'siswa'),
(10, 'Rina', '$2y$10$Mk69RfqlMgjsNPpishCcnOo5zbUioPtaRAi2moOeMAlDVDZuBVZaa', 'guru'),
(11, 'Dedi', '$2y$10$/pgpwkOxhUelBGp6Q7e7..heNbPbbyusfmhYJvSnqgHN2yGzuMvii', 'guru'),
(12, 'Anggra', '$2y$10$oT79NfKnGK43DKaNAxdxROuAPDTC5ZydjdECIylKo2yOLqqgWnXkW', 'guru'),
(13, 'Gilang', '$2y$10$yy/1AOLBqqP2E8vrztcCh.w3s7PsWNRyVhK3pwN5/BTr//2WWqoSi', 'guru');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ekskul`
--
ALTER TABLE `ekskul`
  ADD PRIMARY KEY (`id_ekskul`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id_guru`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_ekskul` (`id_ekskul`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_ekskul` (`id_ekskul`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ekskul`
--
ALTER TABLE `ekskul`
  MODIFY `id_ekskul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id_guru` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `guru_ibfk_2` FOREIGN KEY (`id_ekskul`) REFERENCES `ekskul` (`id_ekskul`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`id_ekskul`) REFERENCES `ekskul` (`id_ekskul`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
