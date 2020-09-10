-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2019 at 05:10 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ujian_sekolah`
--
CREATE DATABASE IF NOT EXISTS `ujian_sekolah` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `ujian_sekolah`;

-- --------------------------------------------------------

--
-- Table structure for table `gambar`
--

DROP TABLE IF EXISTS `gambar`;
CREATE TABLE `gambar` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `link` varchar(50) NOT NULL,
  `nik` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gambar`
--

INSERT INTO `gambar` (`id`, `nama`, `link`, `nik`) VALUES
(24, 'BEBRAS8', '7.png', 10663),
(25, 'CT-POLA', '7.png', 10663),
(26, 'CT-POLA', '17Q.png', 10663),
(28, 'koala', 'Koala.jpg', 1672013),
(31, 'CT-POLA', '11A.png', 10663),
(32, 'CT-BIRD', '8Q.png', 10663),
(33, 'BT-BLOCKY1', '5q.png', 10663),
(34, 'CT-BLOCKY2', '7q.png', 10663),
(35, 'CT-HARTA KARUN', '6q.png', 10663),
(37, 'CT-IO1', '4a.png', 10663),
(38, 'CT-IO2', '4B.png', 10663),
(39, 'CT-PAINT', '12.png', 10663),
(40, 'CT-TANGRAM1', 'TANGRAM1.jpg', 10663),
(41, 'CT-TANGRAM2', 'TANGRAM2.jpg', 10663),
(42, 'CT-TANGRAM1DAN2', 'TANGRAM1DAN2.jpg', 10663),
(43, 'CT-RUBIK', 'rubik_4x4.jpg', 10663),
(44, 'CT-LOLIPOP', 'LOLIPOP.jpg', 10663),
(45, 'Logo_Glowing', 'logo1.png', 18070220);

-- --------------------------------------------------------

--
-- Table structure for table `jawaban_siswa`
--

DROP TABLE IF EXISTS `jawaban_siswa`;
CREATE TABLE `jawaban_siswa` (
  `id` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `id_ujian` int(11) NOT NULL,
  `nik` int(11) NOT NULL,
  `jawaban` longtext DEFAULT NULL,
  `nomor_soal` int(11) DEFAULT NULL,
  `pilihan_jawaban` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jawaban_siswa`
--

INSERT INTO `jawaban_siswa` (`id`, `id_soal`, `id_ujian`, `nik`, `jawaban`, `nomor_soal`, `pilihan_jawaban`) VALUES
(65, 20, 8, 106631, '13', NULL, NULL),
(66, 56, 10, 1672013, 'hanya dalam bidang ilmu informatika saja', 1, 'C'),
(68, 56, 10, 1772005, 'dalam berbagai bidang', NULL, NULL),
(69, 93, 11, 180702201, ' Logo Ultah Talenta ke 14', 1, 'A'),
(70, 56, 10, 19207001, ' hanya untuk bidang komputer saja', 1, 'A'),
(71, 92, 10, 19207001, ' aaaa', 2, 'A'),
(72, 93, 11, 19207001, ' Logo Ultah Talenta ke 14', 1, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `jawaban_siswa_isian`
--

DROP TABLE IF EXISTS `jawaban_siswa_isian`;
CREATE TABLE `jawaban_siswa_isian` (
  `id` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `id_ujian` int(11) NOT NULL,
  `nik` int(11) NOT NULL,
  `jawaban` longtext DEFAULT NULL,
  `nomor_soal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `unit` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama`, `unit`) VALUES
(1, '7A', 'SMP'),
(2, '7B', 'SMP'),
(5, '7C', 'SMP'),
(6, '7D', 'SMP'),
(7, '7E', 'SMP'),
(8, '8A', 'SMP'),
(9, '8B', 'SMP'),
(10, '8C', 'SMP'),
(11, '8D', 'SMP'),
(12, '8E', 'SMP'),
(13, '9A', 'SMP'),
(14, '9B', 'SMP'),
(15, '9C', 'SMP'),
(16, '9D', 'SMP'),
(17, '9E', 'SMP');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

DROP TABLE IF EXISTS `nilai`;
CREATE TABLE `nilai` (
  `id` int(11) NOT NULL,
  `nik` int(11) NOT NULL,
  `id_ujian` int(11) NOT NULL,
  `jenis_ujian` varchar(50) DEFAULT NULL,
  `tipe` varchar(50) DEFAULT NULL,
  `hasil` int(11) NOT NULL,
  `tampil` varchar(50) DEFAULT NULL,
  `kelas` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`id`, `nik`, `id_ujian`, `jenis_ujian`, `tipe`, `hasil`, `tampil`, `kelas`) VALUES
(23, 106631, 8, NULL, NULL, 100, 'non-aktif', NULL),
(25, 1772005, 10, NULL, NULL, 0, 'non-aktif', NULL),
(26, 1672013, 10, NULL, NULL, 0, 'non-aktif', NULL),
(27, 180702201, 11, NULL, NULL, 0, 'aktif', ''),
(28, 19207001, 10, NULL, NULL, 0, 'non-aktif', '7A'),
(29, 19207001, 11, NULL, NULL, 0, 'non-aktif', '7A');

-- --------------------------------------------------------

--
-- Table structure for table `soalisian`
--

DROP TABLE IF EXISTS `soalisian`;
CREATE TABLE `soalisian` (
  `id` int(11) NOT NULL,
  `materi` varchar(255) NOT NULL,
  `soal` longtext NOT NULL,
  `kd` varchar(20) NOT NULL,
  `kunci_jawaban1` longtext NOT NULL,
  `kunci_jawaban2` longtext DEFAULT NULL,
  `kunci_jawaban3` varchar(255) DEFAULT NULL,
  `kunci_jawaban4` varchar(255) DEFAULT NULL,
  `kunci_jawaban5` varchar(255) DEFAULT NULL,
  `gambarSoal` varchar(50) DEFAULT NULL,
  `nik` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `soalisian`
--

INSERT INTO `soalisian` (`id`, `materi`, `soal`, `kd`, `kunci_jawaban1`, `kunci_jawaban2`, `kunci_jawaban3`, `kunci_jawaban4`, `kunci_jawaban5`, `gambarSoal`, `nik`) VALUES
(4, 'test', 'aaa', '1.1', '1', '2', '3', '4', '5', NULL, 10663),
(5, 'testbuang', '1', '1.1', '1', '1', '1', '', '', 'gambar3.jpg', 10663),
(6, 'test2', 'soal 3', '1.3', 'aaa', '', '', '', '', 'gambar21.png', 10663),
(7, 'test', 'Contoh soal ...', '1.2', 'test', '', '', '', '', NULL, 1672013);

-- --------------------------------------------------------

--
-- Table structure for table `soalpg`
--

DROP TABLE IF EXISTS `soalpg`;
CREATE TABLE `soalpg` (
  `id` int(11) NOT NULL,
  `materi` varchar(255) NOT NULL,
  `kd` varchar(20) DEFAULT NULL,
  `soal` longtext NOT NULL,
  `a` longtext NOT NULL,
  `b` longtext NOT NULL,
  `c` longtext NOT NULL,
  `d` longtext NOT NULL,
  `e` longtext NOT NULL,
  `gambarSoal` varchar(50) DEFAULT NULL,
  `gambarA` varchar(50) DEFAULT NULL,
  `gambarB` varchar(50) DEFAULT NULL,
  `gambarC` varchar(50) DEFAULT NULL,
  `gambarD` varchar(50) DEFAULT NULL,
  `gambarE` varchar(50) DEFAULT NULL,
  `kunci_jawaban` longtext NOT NULL,
  `nik` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `soalpg`
--

INSERT INTO `soalpg` (`id`, `materi`, `kd`, `soal`, `a`, `b`, `c`, `d`, `e`, `gambarSoal`, `gambarA`, `gambarB`, `gambarC`, `gambarD`, `gambarE`, `kunci_jawaban`, `nik`) VALUES
(56, 'COMPUTATIONAL THINKING', '2.1', 'Computational Thinking itu adalah...', 'dalam berbagai bidang', 'hanya orang dewasa saja yang menggunakan', 'hanya dalam bidang ilmu informatika saja', 'hanya untuk bidang komputer saja', 'hanya untuk satu bidang saja', NULL, NULL, NULL, NULL, NULL, NULL, 'dalam berbagai bidang', 10663),
(57, 'COMPUTATIONAL THINKING', '2.1', 'Computational Thinking itu adalah...', 'menyelesaikan masalah dengan ilmu informatika', 'menyelesaikan masalah dengan berbagai ilmu pendidi', 'bisa dalam satu bidang', 'hanya bisa menyelesakan  satu bidang saja.', 'tidak bisa  menyelesakan  satu bidang.', NULL, NULL, NULL, NULL, NULL, NULL, 'menyelesaikan masalah dengan ilmu informatika', 10663),
(58, 'COMPUTATIONAL THINKING', '2.1', 'Manfaat belajar Computational Thinking ...', 'masalah kecil terselesaikan', 'hanya masalah besar saja yang terselesaikan', 'dapat menyelesaikan masalah besar dan kecil', 'pola tidak diperlukan dalam menyelesaikan masalah', 'pola tidak dapat menyelesaikan masalah', NULL, NULL, NULL, NULL, NULL, NULL, 'dapat menyelesaikan masalah besar dan kecil', 10663),
(59, 'COMPUTATIONAL THINKING', '2.1', 'Computational Thinking itu adalah...', 'tidak perlu menemukan pola untuk menyelesaikan mas', 'menemukan jawaban untuk menyelesaikan masalah', 'menemukan informasi untuk menyelesaikan masalah', 'menemukan pola untuk menyelesaikan masalah', 'menemukan masalah  untuk menyelesaikan jawaban', NULL, NULL, NULL, NULL, NULL, NULL, 'menemukan pola untuk menyelesaikan masalah', 10663),
(60, 'COMPUTATIONAL THINKING', '2.1', 'Computational Thinking itu adalah...', 'menyelesaikan masalah tanpa mengurainya jadi seder', 'menyelesaikan masalah dengan mengurainya jadi besa', 'menyelesaikan masalah dengan mengurainya jadi sede', 'menyelesaikan masalah dengan mengurainya jadi besa', 'menyelesaikan masalah dengan mengurainya jadi sang', NULL, NULL, NULL, NULL, NULL, NULL, 'menyelesaikan masalah dengan mengurainya jadi sede', 10663),
(61, 'COMPUTATIONAL THINKING', '2.1', 'Dalam cerita nasi goreng, kemiripan proses masak nasi goreng dan kwetiau goreng adalah proses... \r\n', 'pattern recognition', 'decomposition', 'abstraction', 'design algorithm', 'design pattern', NULL, NULL, NULL, NULL, NULL, NULL, 'pattern recognition', 10663),
(62, 'COMPUTATIONAL THINKING', '2.1', 'Dalam cerita nasi goreng, proses siapkan bahan dan mengetahui langkah memasak nasi goreng adalah proses... \r\n', 'pattern recognition', 'decomposition', 'abstraction', 'design algorithm', 'design pattern', NULL, NULL, NULL, NULL, NULL, NULL, 'decomposition', 10663),
(63, 'COMPUTATIONAL THINKING', '2.1', 'Dalam cerita nasi goreng, menyalakan kompor dan cara membeli bawang di pasar, tidak disampaikan,  adalah proses... \r\n', 'pattern recognition', 'decomposition', 'abstraction', 'design algorithm', 'design pattern', NULL, NULL, NULL, NULL, NULL, NULL, 'abstraction', 10663),
(64, 'COMPUTATIONAL THINKING', '2.1', 'Dalam cerita nasi goreng, terdapat langkah memasaknya, masukkan minyak lalu bawang, dan seterusnya sehingga nasi goreng siap disajikan  adalah proses... \r\n', 'pattern recognition', 'decomposition', 'abstraction', 'design algorithm', 'design pattern', NULL, NULL, NULL, NULL, NULL, NULL, 'design algorithm', 10663),
(65, 'COMPUTATIONAL THINKING', '2.1', 'Menurut Pattern Recognition, membuat sirup melon sama dengan membuat... \r\n', 'kopi', 'teh', 'sirup marquisa', 'susu kental manis', 'es buah', NULL, NULL, NULL, NULL, NULL, NULL, 'sirup marquisa', 10663),
(66, 'COMPUTATIONAL THINKING', '2.1', 'Design algoritma adalah langkah yang dibuat untuk menyelesaikan masalah dengan ketentuan... \r\n', 'langkah logis dan dapat dipahami', 'langkah bebas yang penting dipahami', 'langkah logis dan tidak masalah tidak mudah dipaha', 'langkahnya tidak beraturan dan mudah dipahami', 'langkahnya beraturan namun tidak dapat menyelesaik', NULL, NULL, NULL, NULL, NULL, NULL, 'langkah logis dan dapat dipahami', 10663),
(67, 'COMPUTATIONAL THINKING', '2.1', 'Langkah dibuat berurutan, namun untuk hal yang dianggap sudah pahami secara umum, tidak lagi disebutkan dalam langkah langkah, langkah yang tidak perlu disebutkan adalah... \r\n', 'Siapkan piring', 'Memiliki Mulut untuk makan', 'Siapkan Minyak goreng', 'Siapkan minyak goreng panas', 'siapkan wajan', NULL, NULL, NULL, NULL, NULL, NULL, 'Memiliki Mulut untuk makan', 10663),
(68, 'COMPUTATIONAL THINKING', '2.1', 'Membuat Bolu kukus harus menyiapkan bahan bahan yang tepat, seperti halnya telur, tepung terigu, pengembangan dll, hal ini disebut dengan ... \r\n', 'pattern recognition', 'decomposition', 'abstraction', 'design algorithm', 'design pattern', NULL, NULL, NULL, NULL, NULL, NULL, 'decomposition', 10663),
(69, 'COMPUTATIONAL THINKING', '2.1', 'Pada Gambar, Berapa banyak stiker yang diperlukan untuk menutup semua bagian ...\r\n', '96', '512', '384', '256', '382', NULL, NULL, NULL, NULL, NULL, NULL, '384', 10663),
(70, 'COMPUTATIONAL THINKING', '2.1', 'Susunlah angka dari besar ke kecil\r\n16,8,28,10,23,47,13,50,65,46,32,25\r\n', '8,10,13,16,23,28,25,32,46,47,50,65', '8,10,13,16,23,25,28,32,46,48,50,65', '8,10,13,16,25,26,28,32,46,47,50,65', '8,10,13,16,23,25,28,32,46,47,50,65', '8,10,13,16,23,25,28,32,46,47,52,65', NULL, NULL, NULL, NULL, NULL, NULL, '8,10,13,16,23,25,28,32,46,47,50,65', 10663),
(71, 'COMPUTATIONAL THINKING', '2.1', 'TENTUKAN UNTUK ISIAN.....\r\nBERDASARKAN DERET POLA DIBAWAH INI\r\nGAJAH AYAM BEBEK GAJAH .... BEBEK', 'BEBEK', 'AYAM', 'ANJING', 'GAJAH', 'SALAH SEMUA', NULL, NULL, NULL, NULL, NULL, NULL, 'AYAM', 10663),
(72, 'COMPUTATIONAL THINKING', '2.1', 'YANG TIDAK TERMASUK PADA POLA YANG SAMA ADALAH... \r\n', 'GARPU', 'SENDOK', 'PIRING', 'BASKOM', 'MANGKOK', NULL, NULL, NULL, NULL, NULL, NULL, 'BASKOM', 10663),
(73, 'COMPUTATIONAL THINKING', '2.1', 'Pada gambar,   tuliskan angka yang bentuk tangram tersebut, diurutkan dari yang terkecil... \r\n', '1,3,4,13,15,17,20,29,30,31,32,39,40,42,43,44,47,48', '1,3,4,13,15,17,20,29,30,31,32,39,40,41,42,43,44,47', '1,3,4,13,15,17,20,29,30,31,32,33,39,40,41,42,43,44', '1,3,4,13,15,17,20,29,30,31,32,39,40,41,43,42,44,47', '1,3,4,13,15,17,20,29,30,31,32,39,40,41,43,42,44,47', 'TANGRAM1DAN2.jpg', NULL, NULL, NULL, NULL, NULL, '1,3,4,13,15,17,20,29,30,31,32,39,40,41,42,43,44,47', 10663),
(74, 'COMPUTATIONAL THINKING', '2.1', 'Gajah memiliki belalai, lebah memiliki sengat, kucing memiliki cakar. \r\n....... memiliki belalai / sengat / cakar. \r\n', 'variabel', 'input', 'output', 'loop', 'SALAH SEMUA', NULL, NULL, NULL, NULL, NULL, NULL, 'variabel', 10663),
(75, 'COMPUTATIONAL THINKING', '2.1', 'Gajah memiliki belalai, lebah memiliki sengat, kucing memiliki cakar. \r\nbelalai/sengat/cakar adalah .....\r\n', 'variabel', 'input', 'output', 'loop', 'SALAH SEMUA', NULL, NULL, NULL, NULL, NULL, NULL, 'output', 10663),
(76, 'COMPUTATIONAL THINKING', '2.1', 'Gajah memiliki belalai, lebah memiliki sengat, kucing memiliki cakar. Hasil belalai / sengat / cakar tergantung dari... \r\n', 'variabel', 'input', 'output', 'loop', 'SALAH SEMUA', NULL, NULL, NULL, NULL, NULL, NULL, 'input', 10663),
(77, 'COMPUTATIONAL THINKING', '2.1', 'Pada gambar, Input 1 dan 2 untuk menghasilkan angka 6 berwarna HIJAU adalah.. \r\n', '4 BIRU DAN 2 MERAH', '4 BIRU DAN 4 KUNING', '4 BIRU DAN 2 KUNING', 'BIRU DAN MERAH', '3 BIRU DAN 2 MERAH', '4a.png', NULL, NULL, NULL, NULL, NULL, '4 BIRU DAN 2 KUNING', 10663),
(78, 'COMPUTATIONAL THINKING', '2.1', 'Pada gambar, Input 1 dan 2 untuk menghasilkan BULAT  berwarna PINK adalah.. \r\n', 'BULAT MERAH DAN BULAT HIJAU', 'BULAT MERAH DAN BULAT PUTIH', 'BULAT MERAH DAN 1 PUTIH', 'BULAT MERAH DAN BULAT KUNING', '4 MERAH DAN 2 PUTIH', '4B.png', NULL, NULL, NULL, NULL, NULL, 'BULAT MERAH DAN BULAT PUTIH', 10663),
(79, 'COMPUTATIONAL THINKING', '2.1', 'Saat hendak memasak, membutuhkan resep.\r\nPada resep, ada bagian dilakukan berulang ulang untuk semua bahan. Langkah berulang pada proses yang sama disebut dengan...\r\n', 'variabel', 'input', 'output', 'loop', 'SALAH SEMUA', NULL, NULL, NULL, NULL, NULL, NULL, 'loop', 10663),
(80, 'COMPUTATIONAL THINKING', '2.1', 'Pada gambar, langkah untuk mencapai lokasi adalah… \r\n', 'move forward', 'move forward, move forward', 'repeat until location do move forward ', 'repeat until location do move forward move forward', 'SALAH SEMUA', '5q.png', NULL, NULL, NULL, NULL, NULL, 'repeat until location do move forward ', 10663),
(81, 'COMPUTATIONAL THINKING', '2.1', 'Pada gambar peta harta karun, Temukan harta karunnya berdasarkan peta ini,\r\nMulai dari pulau biru\r\nMaju 3 pulau ke timur\r\nMaju 3 pulau ke selatan\r\nMaju 2 pulau ke barat\r\nMaju 2 pulau ke utara\r\nMaju 1 pulau ke barat\r\nMaju 1 pulau ke selatan', '16', '17', '6', '11', '12', '6q.png', NULL, NULL, NULL, NULL, NULL, '11', 10663),
(82, 'COMPUTATIONAL THINKING', '2.1', 'Semua meja di sebuah sekolah bentuknya adalah segitiga sama sisi dan dapat ditempat oleh 3 orang dari tiap sisinya. Kalau 2 meja digabungkan, 4 siswa dapat menempati. Kalau 3 menjadi digabungkan, 5 siswa dapat menempati. Dan seterusnya. Berapa banyak meja', '140', '152', '138', '142', '144', NULL, NULL, NULL, NULL, NULL, NULL, '138', 10663),
(83, 'COMPUTATIONAL THINKING', '2.1', 'Pada Gambar, Langkah yang tepat untuk mencapai tujuan adalah... \r\n', 'LURUS, BELOK KIRI, BELOK KANAN, LURUS', 'LURUS, BELOK KIRI, LURUS, BELOK KANAN, LURUS', 'LURUS, BELOK KANAN, LURUS, BELOK KIRI, LURUS', 'LURUS, BELOK KANAN, LURUS, BELOK KIRI', 'LURUS, BELOK KANAN,BELOK KIRI, LURUS', '7q.png', NULL, NULL, NULL, NULL, NULL, 'LURUS, BELOK KIRI, LURUS, BELOK KANAN, LURUS', 10663),
(84, 'COMPUTATIONAL THINKING', '2.1', 'Pada Gambar, langkah yang tepat untuk mencapai sangkar burung dan membawa makanan untuk anak anaknya adalah… \r\n', 'Terbang 90 derajat', 'Terbang 180 derajat', 'Terbang 45 derajat', 'Terbang 450 derajat', 'Terbang 270 derajat', '8Q.png', NULL, NULL, NULL, NULL, NULL, 'Terbang 45 derajat', 10663),
(85, 'COMPUTATIONAL THINKING', '2.1', 'Bebras menemukan sebuah lorong yang terdiri dari sederet kotak, setiap kotak berisi permen loli atau sikat gigi. Ia harus berjalan sepanjang lorong menuju ujung kanan dan tidak boleh mundur atau balik arah ke ujung kiri. Bebras dapat menggosok gigi kalau ', '3', '2', '4', '5', '6', 'LOLIPOP.jpg', NULL, NULL, NULL, NULL, NULL, '3', 10663),
(86, 'COMPUTATIONAL THINKING', '2.1', 'Pada Gambar, Berapa banyak click  paling sedikit yang harus dipilih untuk merubah warna tersebut ? \r\n', '2', '3', '4', '5', '8', '12.png', NULL, NULL, NULL, NULL, NULL, '3', 10663),
(87, 'COMPUTATIONAL THINKING', '2.1', 'Berapa kali angka 7 muncul di antara bilangan 1 sampai 100? \r\n', '18', '20', '19', '21', '22', NULL, NULL, NULL, NULL, NULL, NULL, '20', 10663),
(88, 'COMPUTATIONAL THINKING', '2.1', 'Berapa jumlah angka 1 sampai 120 ?', '7000', '2460', '7260', '5260', '7160', NULL, NULL, NULL, NULL, NULL, NULL, '7260', 10663),
(89, 'COMPUTATIONAL THINKING', '2.1', 'Sebuah jam dinding berdentang 1 kali pada jam 1, 2 kali pada jam 2, dan seterusnya hingga berdentang 12 kali pada jam 12. Pada jam 3, jam dinding tersebut berdentang selama 3 detik. Berapa detik yang diperlukan jam dinding tersebut untuk berdentang pada j', '7,5 detik', '6,5 detik', '6 detik', '5,5 detik', '7', NULL, NULL, NULL, NULL, NULL, NULL, '7,5 detik', 10663),
(90, 'COMPUTATIONAL THINKING', '2.1', 'Pada Gambar, Kalau mulai dari A dengan urutan angka 3 1 3 2 3,  akan berakhir di? \r\n', 'A', 'B', 'C', 'D', 'SALAH SEMUA', '11A.png', NULL, NULL, NULL, NULL, NULL, 'B', 10663),
(92, 'awdbbbbbbbbbbb', '1.3', 'abc', 'aaa', 'aa', 'a', 'aaaa', 'aaaaa', 'Koala.jpg', 'Koala.jpg', NULL, NULL, NULL, NULL, 'a', 1672013),
(93, 'logo', '1.1', 'Logo apakah ini', 'Logo SMA', 'Logo SD', 'Logo SMP', 'Logo Ultah Talenta ke 14', 'Logo TK', 'logo1.png', NULL, NULL, NULL, NULL, NULL, 'Logo Ultah Talenta ke 14', 18070220);

-- --------------------------------------------------------

--
-- Table structure for table `ujian`
--

DROP TABLE IF EXISTS `ujian`;
CREATE TABLE `ujian` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `waktu` int(50) NOT NULL,
  `tipe` varchar(50) NOT NULL,
  `jenis` varchar(20) NOT NULL,
  `status` varchar(10) NOT NULL,
  `nik` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ujian`
--

INSERT INTO `ujian` (`id`, `nama`, `waktu`, `tipe`, `jenis`, `status`, `nik`) VALUES
(8, 'BEBRAS LEVEL 1', 5, 'Tunggal', 'Pilihan Ganda', 'aktif', 10663),
(9, 'KELAS 7 - UHBAB2', 40, 'Tunggal', 'Pilihan Ganda', 'aktif', 10663),
(10, 'test1', 12, 'Tunggal', 'Pilihan Ganda', 'aktif', 1672013),
(11, 'Ulangan_01', 30, 'Tunggal', 'Pilihan Ganda', 'aktif', 18070220);

-- --------------------------------------------------------

--
-- Table structure for table `ujian_gabungan_has_soal`
--

DROP TABLE IF EXISTS `ujian_gabungan_has_soal`;
CREATE TABLE `ujian_gabungan_has_soal` (
  `id` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `tipe` varchar(20) DEFAULT NULL,
  `id_ujian` int(11) NOT NULL,
  `nik` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ujian_has_soal`
--

DROP TABLE IF EXISTS `ujian_has_soal`;
CREATE TABLE `ujian_has_soal` (
  `id` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `id_ujian` int(11) NOT NULL,
  `nik` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ujian_has_soal`
--

INSERT INTO `ujian_has_soal` (`id`, `id_soal`, `id_ujian`, `nik`) VALUES
(41, 56, 10, 1672013),
(73, 56, 9, 10663),
(74, 57, 9, 10663),
(75, 58, 9, 10663),
(76, 59, 9, 10663),
(77, 60, 9, 10663),
(78, 61, 9, 10663),
(79, 62, 9, 10663),
(80, 63, 9, 10663),
(81, 64, 9, 10663),
(82, 65, 9, 10663),
(83, 66, 9, 10663),
(84, 57, 9, 10663),
(85, 58, 9, 10663),
(86, 59, 9, 10663),
(87, 60, 9, 10663),
(88, 61, 9, 10663),
(89, 62, 9, 10663),
(90, 63, 9, 10663),
(91, 64, 9, 10663),
(92, 65, 9, 10663),
(93, 76, 9, 10663),
(94, 57, 9, 10663),
(95, 58, 9, 10663),
(96, 59, 9, 10663),
(97, 60, 9, 10663),
(98, 61, 9, 10663),
(99, 62, 9, 10663),
(100, 63, 9, 10663),
(101, 64, 9, 10663),
(102, 65, 9, 10663),
(103, 92, 10, 1672013),
(104, 93, 11, 18070220);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nik` varchar(25) NOT NULL,
  `password` varchar(200) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nik`, `password`, `nama`, `unit`, `kelas`, `role`) VALUES
(1, '1672013', '21232f297a57a5a743894a0e4a801fc3', 'Bryan', 'SMA', NULL, 'Guru'),
(4, '1672001', '098f6bcd4621d373cade4e832627b4f6', 'Test', 'SD', NULL, 'Guru'),
(17, '10663', '8199c07da4a53223f42a7d1d2bd20483', 'SICILIA', 'SMP', NULL, 'Guru'),
(18, '19207001', 'e10adc3949ba59abbe56e057f20f883e', 'AGISNA STEPANUS', 'SMP', '7A', 'Siswa'),
(19, '19207002', 'af09b8e7e9aff201acf9f69cdaef9182', 'ALEXANDER DWIKY PASKAHARY', 'SMP', '7A', 'Siswa'),
(20, '19207003', '6083ee297ed7adfb1546c1083fbed007', 'ANGELINE NEEVA HADINATA', 'SMP', '7A', 'Siswa'),
(21, '19207004', 'd3b87195eba21d09023061a6c1633b70', 'AUREL ANANDYTA', 'SMP', '7A', 'Siswa'),
(22, '19207005', '8e32971e0a46161856b0bb105449265b', 'BENNETT JUSTIN WIJAYA', 'SMP', '7A', 'Siswa'),
(23, '19207006', 'b33bc8450ff4f51b30c090bb06ad4362', 'CINDY VIORYNCIA', 'SMP', '7A', 'Siswa'),
(24, '19207007', '9be800a78a945cf352711a889a043dcb', 'CLARISSA DANIELLE NG', 'SMP', '7A', 'Siswa'),
(25, '19207008', 'f53f08eb38ed18686039f2b7dfe40356', 'CLIFF JEREMY', 'SMP', '7A', 'Siswa'),
(26, '19207009', '2e22b46f144c970d0de20eb1c0595c6a', 'DEVEN GERRARD KARTAMIHARDJA', 'SMP', '7A', 'Siswa'),
(27, '19207010', 'a4ec00751ec621c8b449ff1c20ac5d19', 'DOMINIKUS JEVON HARTANTO', 'SMP', '7A', 'Siswa'),
(28, '19207011', '7be4f9d861cdcc8eb6ad43ef55d147fe', 'EDBERT JONATHAN FELIX', 'SMP', '7A', 'Siswa'),
(29, '19207012', 'db3b68b63e7aad380109cc8fe76fab40', 'EVAN NELSON NATANAEL', 'SMP', '7A', 'Siswa'),
(30, '19207013', 'efc72f8b640035f3d716b2e5f624f01f', 'GIZELLE VIRGINIA', 'SMP', '7A', 'Siswa'),
(31, '19207014', 'b0ded8714c659e0d534c045c8394d9cc', 'JAQUELIND SHEARENE', 'SMP', '7A', 'Siswa'),
(32, '19207015', '3c893e8586da1bbd9ed172c1ccf784bd', 'JESSICA EVELYN PAULINA', 'SMP', '7A', 'Siswa'),
(33, '19207016', '42ebc028c6a660a781a1d7949bcef59e', 'JESSLYN NIKITA CHRISTIAN', 'SMP', '7A', 'Siswa'),
(34, '19207017', '7684fa443e7b46f12a95d29701e3747e', 'JONATHAN ALOYSIUS CAHYO SAPUTRO HUTABARAT', 'SMP', '7A', 'Siswa'),
(35, '19207018', 'e40e6af26f31febc799f26f9834057c5', 'JUSTIN MATTHEW CHAROEN', 'SMP', '7A', 'Siswa'),
(36, '19207019', '4633cebd84d44be9afec9b539db82b42', 'KARISMA BINTANG TIMOER', 'SMP', '7A', 'Siswa'),
(37, '19207020', '687e61a5f3fa400f4ce7352da0dcefbf', 'KEANU SULTHAN BRATADIKARA', 'SMP', '7A', 'Siswa'),
(38, '19207021', '1127d62650a7b621df887c2521cbb2bb', 'KEISHA NATALIA AVIANTO', 'SMP', '7A', 'Siswa'),
(39, '19207022', 'd165478de8e9a011e2549c8977ca0676', 'NICKY', 'SMP', '7A', 'Siswa'),
(40, '19207023', 'a0cdc17c8c2ae98d47408850af6ce5a1', 'NIGEL FERN', 'SMP', '7A', 'Siswa'),
(41, '19207024', '8032fdf2c3df69b20cd96e109fd24c18', 'PATRICIUS MARSHALL ANDIKA', 'SMP', '7A', 'Siswa'),
(42, '19207025', '0cf5750d363dc18d16f29dd929201ebd', 'PETER HANSEL SANJAYA WANGSAPUTRA', 'SMP', '7A', 'Siswa'),
(43, '19207026', 'd4dbdf45c1ac5018c3f07ccaf9a570f8', 'PHOENIX PUTRA ISKANDAR', 'SMP', '7A', 'Siswa'),
(44, '19207027', 'b14b9476f9e537a5e125dad54c83d90e', 'PUTRI NAOMI MAWUNTU', 'SMP', '7A', 'Siswa'),
(45, '19207028', '28ec89e028e0d550e6961d4b8e20d76f', 'RAFAEL CLAREN DINATA', 'SMP', '7A', 'Siswa'),
(46, '19207029', 'fa98cc212ad1319d5898c70c59d5c13f', 'RICHELLE', 'SMP', '7A', 'Siswa'),
(47, '19207030', '0358ff36913ee1b99a4299c6bbb1f426', 'VALENTINA AYLINE TANIKA', 'SMP', '7A', 'Siswa'),
(48, '19207031', '145f8f2702f010e830d347c3c8a2ae65', 'ADELLIA REVA SUDARNA', 'SMP', '7B', 'Siswa'),
(49, '19207032', '559d43b331b4295890fd88a1d4a39f3a', 'AMELIA ANGEL WIDJAJA', 'SMP', '7B', 'Siswa'),
(50, '19207033', 'fb0711a14be3975b09441d22d12d13a3', 'BLESSLEY NATIRADSA', 'SMP', '7B', 'Siswa'),
(51, '19207034', '9fcb29265c9b76767ee9958389427dca', 'CATHERINE SUSANTO', 'SMP', '7B', 'Siswa'),
(52, '19207035', '436c4f9d7406f37109c8a2fc60455119', 'CHELSEA BELLA BELINDA YAP', 'SMP', '7B', 'Siswa'),
(53, '19207036', '7634d52d2a36af6739201dc9a2c56373', 'DARREN WILLIAM JUNIOR', 'SMP', '7B', 'Siswa'),
(54, '19207037', 'dd66e6e792347978209038e6488f1d3a', 'DAVA SANTOSA', 'SMP', '7B', 'Siswa'),
(55, '19207038', '3fa6b50e8918cd33060a04a1d39e369a', 'DOMINICA JEVELYN HARTANTO', 'SMP', '7B', 'Siswa'),
(56, '19207039', '01c14d71d5e248e263adc847fe99c805', 'EVELYN NIKITA GUNAWAN', 'SMP', '7B', 'Siswa'),
(57, '19207040', 'fa26f48ec24714fc0918a5dad2adda50', 'FAYELYNN CARLA CAHYADI', 'SMP', '7B', 'Siswa'),
(58, '19207041', '05258a472d85576223950db7d07d3501', 'FIECHELLA CLARISTA', 'SMP', '7B', 'Siswa'),
(59, '19207042', '8219ebe6fd322fd69ab474231e78de2b', 'JASON ALEXANDER', 'SMP', '7B', 'Siswa'),
(60, '19207043', '966effc24354cfa13c0566781969afd6', 'JONATHAN SEBASTIAN DONNY', 'SMP', '7B', 'Siswa'),
(61, '19207044', 'ebdb5d2fc80eb3953a5a8e019a34c0fd', 'KENNETH AURELIAN NATHANIELLE UNTUNG WIDJAJA', 'SMP', '7B', 'Siswa'),
(62, '19207045', 'e4c09827afa6f07edda84cd14abf2f9d', 'KEVANCEA IKEA DJUNAEDI', 'SMP', '7B', 'Siswa'),
(63, '19207046', '23def0f79834f9a9231f9c3744eaa9e3', 'LIE WILLIAM', 'SMP', '7B', 'Siswa'),
(64, '19207047', '10352b75a01ab02b822880a6255ce7bc', 'LIONEL KEVIN SETIAWAN ', 'SMP', '7B', 'Siswa'),
(65, '19207048', 'caf65b5a03794ecf244070421b03f147', 'MADELAINE ANGELIQUE TENGGANA', 'SMP', '7B', 'Siswa'),
(66, '19207049', '8b215357e9a45aecfe237f9d274abb97', 'MESSI', 'SMP', '7B', 'Siswa'),
(67, '19207050', '22ca2f60d7ffcb255aa75f8bf60cf31d', 'MOSES FEBRIANO ADRIAN', 'SMP', '7B', 'Siswa'),
(68, '19207051', '07afe3e9a07074b63f1452d8168bc8c6', 'NATHANAEL GUNAWAN BUDHI', 'SMP', '7B', 'Siswa'),
(69, '19207052', 'aba55135af12f8e35fb0a197f6433982', 'NATHANAEL SETIADY', 'SMP', '7B', 'Siswa'),
(70, '19207053', '39f877be46f2429adcc8cc4b2dd29c27', 'PETER GABRIEL WIDJAYA', 'SMP', '7B', 'Siswa'),
(71, '19207054', '1c146a9ddd1c0dea6f7dba3caceedcb1', 'REVELLIUS ANDY SUTRISNO', 'SMP', '7B', 'Siswa'),
(72, '19207055', '5a24c040989971b099d13bf67704ca5c', 'SANTY MITTA DEWI', 'SMP', '7B', 'Siswa'),
(73, '19207056', '6f724c7223306a1b3f386f448c968003', 'SHARON ELIZABETH SHAFIRA NUGRAHA', 'SMP', '7B', 'Siswa'),
(74, '19207057', 'f5398c990eda511874e09b0bbe3c6ade', 'VIVIENNE LAURETTA', 'SMP', '7B', 'Siswa'),
(75, '19207058', '39b858d07ba5676852105eb6e516fd5e', 'WILLIAM LIANGGARA', 'SMP', '7B', 'Siswa'),
(76, '19207059', '75c55a0e1d2806dcfa828510fa1e7e84', 'WILSON HERLYANTO', 'SMP', '7B', 'Siswa'),
(77, '19207060', '0d2395337e008d859351116abdfe6a32', 'YOHANES FARRELL HARYANTO', 'SMP', '7B', 'Siswa'),
(78, '19207061', '1eafc8b2e73d5626a8c48d3d0830e2ad', 'YUNITA PRATIWI CHRISTIANI ZEGA', 'SMP', '7B', 'Siswa'),
(79, '19207062', 'ffa9782294d9ac724de5ea3104a0d0fa', 'ALBERTA GENEVINE LENDENG', 'SMP', '7C', 'Siswa'),
(80, '19207063', '4c9fc4978dcd474fdacba736f3f21410', 'ANDARA HANIA PUTRI SURYANA', 'SMP', '7C', 'Siswa'),
(81, '19207064', 'b39bc2d020dd4b04fc764b8739867c64', 'AZRIEL PIETERSSEN JEREMYA HERNAWAN', 'SMP', '7C', 'Siswa'),
(82, '19207065', '2ad591931af0f88120003ec9bda2cbcc', 'BRANDON ANTONIUS SONTANI', 'SMP', '7C', 'Siswa'),
(83, '19207066', '4ba3678b942f1b8e13236fc3d38b9d83', 'CHAVEL FAYOLA SUPRIADY', 'SMP', '7C', 'Siswa'),
(84, '19207067', '34ce3ed99481b95a5a00647eb9201a71', 'CHRISTIAN JONATHAN HALIM', 'SMP', '7C', 'Siswa'),
(85, '19207068', '8dc41b993c52c822e9224b25d24fd1a0', 'CHRISTIANO DARREN ISKANDAR', 'SMP', '7C', 'Siswa'),
(86, '19207069', '45d0b36bc0f83890aec14bc87acd1f0d', 'DANIEL HIZKIA KIMBERLEY', 'SMP', '7C', 'Siswa'),
(87, '19207070', '412ec5d129a085cc6fd0200c7fc88331', 'DEVON KENRICK TANUSAPUTRA', 'SMP', '7C', 'Siswa'),
(88, '19207071', 'bf072c57505d99b23e7a003803c5cc08', 'EVANDER BERNICO CHRISTIAN', 'SMP', '7C', 'Siswa'),
(89, '19207072', 'b4fcd6f40b3cd2387cb32d37c36f7877', 'FERDINAND VINCENCIUS', 'SMP', '7C', 'Siswa'),
(90, '19207073', 'ccc6c36ae2f06542b69d31aba2258420', 'FRANKIE CHRSTIAN RAFAEL', 'SMP', '7C', 'Siswa'),
(91, '19207074', '532c18762e085298610b61f1997cc2b7', 'GERREN FERDINAND ASKANIO', 'SMP', '7C', 'Siswa'),
(92, '19207075', '85bd42572ea5f2b46e61ad7f2f30414a', 'GRACIELA KIRANA PUTRI WARSITO', 'SMP', '7C', 'Siswa'),
(93, '19207076', '4b2fa0ed04d6a7e3e9c35a27157a9352', 'JEANNIFER ARISKA GENEVIE', 'SMP', '7C', 'Siswa'),
(94, '19207077', 'f8ffb5d29f699ad68c4a7d97b98829b8', 'JENNIFER STEFFANIE YOSEPH', 'SMP', '7C', 'Siswa'),
(95, '19207078', '986b1cffb684652206f87f6a671459f9', 'JHUAN CARLOS IMANUEL SITIO', 'SMP', '7C', 'Siswa'),
(96, '19207079', '371dfa1e27daec9c765d2a2a63ea3dd8', 'KARYN EVELYN VERSTRAETAN', 'SMP', '7C', 'Siswa'),
(97, '19207080', 'f52cbb1084d8b958284e74446c69ee91', 'KEIRA JENNIFER', 'SMP', '7C', 'Siswa'),
(98, '19207081', '7fc1a518fac4a7c967a043f6053c2717', 'KENNETH MARTIN NATHANAEL', 'SMP', '7C', 'Siswa'),
(99, '19207082', '37dbdee7225a28582dc20dbf8a60a943', 'KEVIN ALROY CHOUWNATA', 'SMP', '7C', 'Siswa'),
(100, '19207083', '5a175751770147f9bf1a450a8c198911', 'MARCELINA CRISTABELLA', 'SMP', '7C', 'Siswa'),
(101, '19207084', 'd384e13c8546f3bd705cb3d13637ea46', 'MARIA RENATA ARDIYANTI', 'SMP', '7C', 'Siswa'),
(102, '19207085', 'df9bba5d5c86d6980aaa8d76e72be5a1', 'PATRICIA AUDREY', 'SMP', '7C', 'Siswa'),
(103, '19207086', 'ea98914515309006d475fd0c4926ccde', 'REYNARD ARKANANTA KAESES', 'SMP', '7C', 'Siswa'),
(104, '19207087', '73f9696300f23ed2701514c575fcba6f', 'SHAREEN STEPVANIA', 'SMP', '7C', 'Siswa'),
(105, '19207088', 'd8ad35627b8ddea89ce2f47e96f4bd39', 'SILALAHI, LORENZO JULIO PARDAMEAN', 'SMP', '7C', 'Siswa'),
(106, '19207089', '0ccdf9814cf77a14a9fe997bc545c7b3', 'STEVANNY', 'SMP', '7C', 'Siswa'),
(107, '19207090', '089ff7a4a26234124251b08d86827109', 'TIFFANY VALERIE CHANDRA', 'SMP', '7C', 'Siswa'),
(108, '19207091', '4a0ee7f5955569b391ed0813f7cf9ee1', 'TIMOTIUS ARDITO PRAMA', 'SMP', '7C', 'Siswa'),
(109, '19207092', '65d95ef9d0793a1633d680bc67197541', 'VARREL JOVAN STEFANUS', 'SMP', '7C', 'Siswa'),
(110, '19207093', '05a028d39280babe827e854093198139', 'ADDYSON BAYLEE', 'SMP', '7D', 'Siswa'),
(111, '19207094', 'bef9883e95b07ebcdf49a27e01d1a48f', 'ADRIEL CHRISTOPHER', 'SMP', '7D', 'Siswa'),
(112, '19207095', '48cef1868627c6a64d381828fd7934cb', 'AIR GANGGA IYANA', 'SMP', '7D', 'Siswa'),
(113, '19207096', '6a16c778dde84ae871ad66f0684759b1', 'ALEXANDRA ALETA LIVIA JUDIHARDJO', 'SMP', '7D', 'Siswa'),
(114, '19207097', '6ba2a293f4e5632c4772271393dc0faa', 'ALEXANDRIA CHRYSALLIS GUNAWAN', 'SMP', '7D', 'Siswa'),
(115, '19207098', '66b27a1908b9a008e07c9bc4a7fdc581', 'ANDRE WIJAYA', 'SMP', '7D', 'Siswa'),
(116, '19207099', '1bb5d9a72b43793bc33aea96d7431b42', 'AUDRICK ESTRELLO', 'SMP', '7D', 'Siswa'),
(117, '19207100', '3b790a71e0e74a32a66d972ddb1c4f64', 'CATHERINE VALENCIA ANDHI', 'SMP', '7D', 'Siswa'),
(118, '19207101', '2b4eb9bcc5349306af746a708d1f9dfa', 'CECILLE VELOVE PRASETYA', 'SMP', '7D', 'Siswa'),
(119, '19207102', '83761e6abb9237234c69c31d174863d1', 'DAVID EMMANUEL', 'SMP', '7D', 'Siswa'),
(120, '19207103', '58f8c71528d77bcda4beccc0dbdb2e80', 'EMMYLIANA HAPPY YONA PR', 'SMP', '7D', 'Siswa'),
(121, '19207104', 'd040fdf43bacd0cf195d67868e76427b', 'FREDRICK JASON WIDJAJA', 'SMP', '7D', 'Siswa'),
(122, '19207105', '7f73cf8dc1f40f3688f703153630cd56', 'GHEA AURELIA AGATHA', 'SMP', '7D', 'Siswa'),
(123, '19207106', 'f45a67ed461a9b86d23324db6b368547', 'GRACE CAROLINE WIJAYA', 'SMP', '7D', 'Siswa'),
(124, '19207107', '2c00fbbadf500aa5c27cb1ac520fa111', 'HERI BAMBANG YANTO', 'SMP', '7D', 'Siswa'),
(125, '19207108', 'cfea8d4a74190d60b261eb5c5d9d0b43', 'JEREMY CHRISTIAN WIGUNA', 'SMP', '7D', 'Siswa'),
(126, '19207109', '63ba71a7d4d4e2aeba9f0460e972aa95', 'JOSAFAT FAREL SUTANTIO', 'SMP', '7D', 'Siswa'),
(127, '19207110', '92341ab16bcdf2382c9eeb008d70a2df', 'JOSE ALFREDO LIMAN', 'SMP', '7D', 'Siswa'),
(128, '19207111', '02558d0d287fadd87554248db4a4bd2e', 'JOSHUA CHRISTOPHER GUNAWAN', 'SMP', '7D', 'Siswa'),
(129, '19207112', '676a86459bcdeb42b7d874bcfef79c0e', 'KEANNE KEVANEN HO', 'SMP', '7D', 'Siswa'),
(130, '19207113', '88e2baa03222189ceef9243c7899a307', 'KENTARO AURELIO DRINOV', 'SMP', '7D', 'Siswa'),
(131, '19207114', 'c4a80962ab783434c1a323a1d582097a', 'NATHANAEL WIJAYA', 'SMP', '7D', 'Siswa'),
(132, '19207115', '1d17ef7acbe95a2600685ecdaf54889e', 'OFELIA MAUREEN BUDIANTO', 'SMP', '7D', 'Siswa'),
(133, '19207116', '4c0f27eb9f91b6702f81fa8603d53e0c', 'PATRICIA CLORIEND CELLESTA', 'SMP', '7D', 'Siswa'),
(134, '19207117', 'b61adb7a822b96df952aede1a4f8a0dc', 'RENATA ANGELINA', 'SMP', '7D', 'Siswa'),
(135, '19207118', '036ef167c7dc56d342ff9f11161b1d6f', 'RICHARDSON TANUJAYA', 'SMP', '7D', 'Siswa'),
(136, '19207119', 'cc3724d2fc455fbdc5a006f1499c7f2e', 'SANTI', 'SMP', '7D', 'Siswa'),
(137, '19207120', '39427d800619c0214c85a0545c4258a1', 'VALLEN CHRISTINA RAFAELA IBRAHIM', 'SMP', '7D', 'Siswa'),
(138, '19207121', 'aab99ac60f3206d77d829a1e75dd2b59', 'VIENY AVRILYN', 'SMP', '7D', 'Siswa'),
(139, '19207122', '67251b0598b0b3225f836d1667cd4e35', 'VINCENT THEO SUDARSONO', 'SMP', '7D', 'Siswa'),
(140, '19207123', '1413b230a855ab2aff4f09c43c2240a1', 'WIBI ARJANTA SENJAYA', 'SMP', '7D', 'Siswa'),
(141, '19207124', '785bdabc67aa70526881ea7c66370d41', 'ANTONIUS HARTANTO', 'SMP', '7E', 'Siswa'),
(142, '19207125', 'cbd485e29f7b09b26a175832d27c300b', 'ARTHUR FRANCE DEWANTORO', 'SMP', '7E', 'Siswa'),
(143, '19207126', '57370ad3b2b042052b1c67538e42a2a1', 'CECILIA GUNAWAN', 'SMP', '7E', 'Siswa'),
(144, '19207127', '6a5767b1580a92c9275a5339a21e9e26', 'CHRISTABEL ANGELICA', 'SMP', '7E', 'Siswa'),
(145, '19207128', '1dda8deb94a7e505cb8c15864bb82197', 'DAIN NATHAN ZHU', 'SMP', '7E', 'Siswa'),
(146, '19207129', '3c16b13a66848b724dca9a4ab30f4ae4', 'FELICIA SEPTIANY ARYAWAN', 'SMP', '7E', 'Siswa'),
(147, '19207130', '5c3a6f994107d6cca5a261a7295662df', 'GERALD ALEXANDER', 'SMP', '7E', 'Siswa'),
(148, '19207131', 'ca53b04d265e058551bf64141335f41a', 'GIOVANNA EDELINE TANUDJAJA', 'SMP', '7E', 'Siswa'),
(149, '19207132', '6762788a43e5de825722ea1eb49b7a4a', 'GLEN CAHYADI', 'SMP', '7E', 'Siswa'),
(150, '19207133', 'c64cd061e5fc3aa9dcd4a378082dd38b', 'HANS DARIUS TANOTO', 'SMP', '7E', 'Siswa'),
(151, '19207134', '5f23bde8591010a8a47ab5158457b32c', 'HANSEN KURNIAWAN', 'SMP', '7E', 'Siswa'),
(152, '19207135', '42040ddbe97baffffe7f430f6864d7ee', 'JESSICA KUSUMA', 'SMP', '7E', 'Siswa'),
(153, '19207136', '711626aac4d945b2f23b4d85b17f36c0', 'JOCELYN ABIGAIL YOSIANA SIE', 'SMP', '7E', 'Siswa'),
(154, '19207137', 'd3e85a61bf2c2ec577940f46c3470c42', 'JONATHAN FELIX GARCIA VON Y', 'SMP', '7E', 'Siswa'),
(155, '19207138', 'eb4ea91a7b405be24a5e5fba0acb409e', 'JUAN PETRIX MANUEL', 'SMP', '7E', 'Siswa'),
(156, '19207139', 'fb1246bd734a8bad1a68eead3940f176', 'KATHLEEN NATHANIA LILI', 'SMP', '7E', 'Siswa'),
(157, '19207140', 'e1e834f59a74a5f86438344846290752', 'KEEFFE VALENT ELDRIAN', 'SMP', '7E', 'Siswa'),
(158, '19207141', 'c90e58c62842481b44ec4f10438b22a7', 'KENNETH AUSTIN SUJANTO', 'SMP', '7E', 'Siswa'),
(159, '19207142', 'f234c0d4bac28f0dd9df710c0e150932', 'LETICIA AGATHA ROSELLE', 'SMP', '7E', 'Siswa'),
(160, '19207143', '8648ce8bd6d3324832f58e7f525a7ce1', 'LYNELL NATANIA HARYADI', 'SMP', '7E', 'Siswa'),
(161, '19207144', 'e4c67ad3f790763b7f80490d9e3cdc0a', 'MARRYANNE JOSEPHINE SETIAWAN', 'SMP', '7E', 'Siswa'),
(162, '19207145', '372bd4d0a6a3ac4eb54f58ce0d4faeb7', 'NAOMI CRYSANTA MILKA', 'SMP', '7E', 'Siswa'),
(163, '19207146', 'c18464df60add000c219d6b853167267', 'RAFAELLO MANUEL SUTOMO ', 'SMP', '7E', 'Siswa'),
(164, '19207147', 'b1e80ecf2913e6e3a46aea1a74402d23', 'RICHARD JONATHAN', 'SMP', '7E', 'Siswa'),
(165, '19207148', '03cbd5099913113a6268ebbef8a5578d', 'RICHARDO NATA WIJAYA', 'SMP', '7E', 'Siswa'),
(166, '19207149', '4a42b11d40d8b8edb00a207195a6aeff', 'STEFANIE AURELIA', 'SMP', '7E', 'Siswa'),
(167, '19207150', 'aa8841d04b897d3bb42e4f2195931802', 'STEVEN OFELIUS ASIH', 'SMP', '7E', 'Siswa'),
(168, '19207151', '239a4bedf105f72b9845deacca604261', 'TAN,FEBRYAN HANSEL', 'SMP', '7E', 'Siswa'),
(169, '19207152', 'c05e7f584820c4bd439d1d39e04933f4', 'VALERIE LAUREN ANDERSON', 'SMP', '7E', 'Siswa'),
(170, '19207153', '16ad06a4919b8c5b4cb36e663d05562a', 'YOSEPHINE NADYA WIJAYANTI', 'SMP', '7E', 'Siswa'),
(171, '18197053', '44ae846754f689fe67b7709c85d1b745', 'ALDRICH LIM', 'SMP', '8A', 'Siswa'),
(172, '18197002', '6bd1e3f1530d1d24e6fe01ac8cc234d7', 'ANASTASIA CYNTHIA CAHYANA', 'SMP', '8A', 'Siswa'),
(173, '18197005', 'e45b642e6073ec845e48722ac4293df0', 'BENEDICTA FLORENCE HERWANTO', 'SMP', '8A', 'Siswa'),
(174, '18197084', 'f6c5c4ef568993ca8839be19a86c3b42', 'BENEDICTUS JASON CHANDRA', 'SMP', '8A', 'Siswa'),
(175, '17187079', '3b13b0dca319b649b216ad23ebc60738', 'BRYAN VALENTINO', 'SMP', '8A', 'Siswa'),
(176, '18197057', '89c19a0961df92334af70b4a802cb3c1', 'CHIQUITA LORRAINE', 'SMP', '8A', 'Siswa'),
(177, '18197088', '7ee90f2c7adf7028be8cd80b47b1b867', 'CHRISTINA LILIANTO', 'SMP', '8A', 'Siswa'),
(178, '18197111', 'c76be3cbc309aaab04b1ee0de7000295', 'CLEVERIO SUGIJANTO', 'SMP', '8A', 'Siswa'),
(179, '18197093', '54ad3c238ffb9bccd13bc3b0cb05d9ef', 'FARREL OBRIN PRATANAYA LUMBANTORUAN', 'SMP', '8A', 'Siswa'),
(180, '18197032', '3b2a4687b7b6e980546d601ebd209b31', 'FLORENSIA KAREN TANGSUL ALAM', 'SMP', '8A', 'Siswa'),
(181, '18197011', '635fe0b94514d14d3f56c653c058d9f1', 'GABRIELLA CHRISTIAN', 'SMP', '8A', 'Siswa'),
(182, '18197094', '38464808452c27343c40f6ea8061a44c', 'GALEN ARLYANNO', 'SMP', '8A', 'Siswa'),
(183, '18197097', '5adbbf80549ff1ba72fb86a0e0edaa3c', 'GO, CHRISTOPHER JOVI', 'SMP', '8A', 'Siswa'),
(184, '18197062', '5ca589dcb503349d40d1b3ae42ce3f65', 'IMMANUEL ALVINE WIJAYA', 'SMP', '8A', 'Siswa'),
(185, '18197063', 'dc92251dbb963b903ffb410e8f10e6a6', 'IRENE NATHANAEL', 'SMP', '8A', 'Siswa'),
(186, '18197099', '551a306e33448b0f6f19d753e642f72c', 'JEANE CHRISTABEL WIJAYA', 'SMP', '8A', 'Siswa'),
(187, '18197115', '6a05a5c3a1477705e6d48ca571cfcc3e', 'JOCELYN AUDREY WIJAKSANA', 'SMP', '8A', 'Siswa'),
(188, '18197101', 'ce852e196cff9c40f9f37e2782ef2a3b', 'MANDA ELVINA HARYANI', 'SMP', '8A', 'Siswa'),
(189, '18197069', 'd6d9944a665941b8c3527f9334c6b614', 'MICHAEL STEFANUS TANOTO', 'SMP', '8A', 'Siswa'),
(190, '18197120', '5959d48290591657eff48c3d7cf40a36', 'MICHAEL VALENTINO', 'SMP', '8A', 'Siswa'),
(191, '18197043', '753cad1e010dae9ee18d0f7e61ba2eee', 'MICHELLE POEY', 'SMP', '8A', 'Siswa'),
(192, '18197020', '82e1fc7b230bcded4543f034c7095e60', 'MICKY CHRISTIAN HARYANTO', 'SMP', '8A', 'Siswa'),
(193, '18197075', '095455fb5d52448e553ae237c898cdc1', 'TERESA CARMELIA HERMAWAN', 'SMP', '8A', 'Siswa'),
(194, '18197025', 'f959a7f6b04cdb7e564f2da786cdedb9', 'VINCENT VALENTINO OEMAR', 'SMP', '8A', 'Siswa'),
(195, '18197051', 'b3d28dc17c2c13db80df0d6d9cdc2c83', 'WONG, WILLIAM ALAN', 'SMP', '8A', 'Siswa'),
(196, '18197052', 'af989f9923fc03e1951b4b7f6cc25f74', 'YASA KANAYA TEDJO', 'SMP', '8A', 'Siswa'),
(197, '18197130', '3c0e7c68df0902188418af9f391421b3', 'YOSUA ARMALI', 'SMP', '8A', 'Siswa'),
(198, '18197079', 'bdb393f396827799a1790b7c5bfd870b', 'AARON NIKOLAS TONDOSAPUTRO', 'SMP', '8B', 'Siswa'),
(199, '18197105', '4a55b941584fac520b5daf0c165f3be5', 'ADELIA MARITO HUTABARAT', 'SMP', '8B', 'Siswa'),
(200, '18197082', '2f0854113085941da4fa8ccaa3a10332', 'ALEXANDRA APRILIA SONJAYA', 'SMP', '8B', 'Siswa'),
(201, '18197001', '474cd2c54daf6085ce042e846d0fc442', 'ALI PANGESTU KURNIAWAN', 'SMP', '8B', 'Siswa'),
(202, '18197083', 'f95f8428d35aa3cebffcd327eef50a9f', 'ALINE FAYOLA LINARDO', 'SMP', '8B', 'Siswa'),
(203, '18197107', '6110331bfb6045900ddc497b9fcdc0e8', 'ANDREA VERONIQUE YONATAN', 'SMP', '8B', 'Siswa'),
(204, '18197003', '992399b75d81910e23da512698880013', 'ANTHONY JONATHAN', 'SMP', '8B', 'Siswa'),
(205, '18197109', 'e3061507bec587dfeb27873fc4bfd4e8', 'BILLY ANANDA ARIANTO', 'SMP', '8B', 'Siswa'),
(206, '18197055', '2c36accc1925b5be99c77e661be6a6c1', 'CATHERINE AMANDA', 'SMP', '8B', 'Siswa'),
(207, '18197090', '638571a2dee282fdc1e2f4e7dd9dfa1f', 'DAVE HANSELL EMMANUEL', 'SMP', '8B', 'Siswa'),
(208, '18197112', 'f5a90a3a2fff0df69c5b0d495b5644c9', 'FELIX TANU EDRIA', 'SMP', '8B', 'Siswa'),
(209, '18197060', '88380c7f21430947fe3a3e6adf298881', 'FIONA THEJA', 'SMP', '8B', 'Siswa'),
(210, '18197013', '04c41308ecb43db4da3e563126af4594', 'IVORY LILY', 'SMP', '8B', 'Siswa'),
(211, '18197037', '94b2711150ba2900aa43908acafc1e95', 'JANICE KAY KURNIAWAN', 'SMP', '8B', 'Siswa'),
(212, '18197038', 'd58ee247b7606ce6e6cf621637e44593', 'JARED KHOVOSKY', 'SMP', '8B', 'Siswa'),
(213, '18197041', '3dcad3189dd80e8323ba711eb8dac2e1', 'JUSTIN ENRICO', 'SMP', '8B', 'Siswa'),
(214, '18197064', '057408f39fb2d7af1ccfe0e1a45deb0d', 'KESYA LAURENCIA PUTRI VIANDRA', 'SMP', '8B', 'Siswa'),
(215, '18197017', 'c9383a50c41e2ed5b084a76c42d8ca89', 'MATTHEW BRANDON SETIADI', 'SMP', '8B', 'Siswa'),
(216, '18197042', '58d346855579d95a1e305b0aecbf062f', 'MICHELLE', 'SMP', '8B', 'Siswa'),
(217, '18197121', '7ccd6b5a3c215581413b4e5b6c7e7326', 'NADYA PONTOH', 'SMP', '8B', 'Siswa'),
(218, '18197045', '6bfb99e33a5eebed1c4fc058dbb03390', 'RICHARDO SUSANTO', 'SMP', '8B', 'Siswa'),
(219, '18197046', '91fbd1574eab37719bf8b693b6f85628', 'ROY MICHAEL  HALIM', 'SMP', '8B', 'Siswa'),
(220, '18197126', 'c3bb0d960cd1af38fd9b5ba5172ff251', 'SAMPERURA, ANGELIA GRACIA', 'SMP', '8B', 'Siswa'),
(221, '18197128', 'c7462738641019990cf4a823d936c789', 'VALDY PRACIO INDRAJAYA', 'SMP', '8B', 'Siswa'),
(222, '18197129', '17f88ea71530261d1d9b8acc774f62b2', 'VALENT STEVE ASMAN', 'SMP', '8B', 'Siswa'),
(223, '18197023', '840dcc2b4ec0596b89e715261a29228d', 'VARREL PATRICIUS', 'SMP', '8B', 'Siswa'),
(224, '18197081', 'd20ff07488191b5aa2cb30eb031885a0', 'ADRIAN PRAYOGA', 'SMP', '8E', 'Siswa'),
(225, '18197027', '49e7efc5eb67c964b71dbc4075b374ee', 'ANGELINE CHRISTY SETIAWAN', 'SMP', '8E', 'Siswa'),
(226, '18197028', '4f37d5b9a2a1374b9d25fc80f970dce8', 'BAMBANG ARANAYA SAPUTRA', 'SMP', '8E', 'Siswa'),
(227, '18197108', '4753d27e229ad345dbcc08931515f997', 'BERNADETTE SEKAR AYU KARMELITA', 'SMP', '8E', 'Siswa'),
(228, '18197006', 'd2e4770bff9764801e6b623800ff2ee6', 'CELIA CHRISTABELLE OESMAN', 'SMP', '8E', 'Siswa'),
(229, '18197056', 'fa8f9dfd152ed25755977121768a5984', 'CHANTAL MAGNIFICENTSA HELENA NOBHO', 'SMP', '8E', 'Siswa'),
(230, '18197030', '38aa5bc35b251869bf6b51f4ae465d9b', 'DANIEL MARIO TARIGAN', 'SMP', '8E', 'Siswa'),
(231, '18197091', 'd19af87acab066c4c958c7e4964a31cf', 'DEBI WIBELA', 'SMP', '8E', 'Siswa'),
(232, '18197059', '6603279c193225d60a186246305fb7e8', 'FEDERICO ALEX MARLIAN', 'SMP', '8E', 'Siswa'),
(233, '18197009', '9197e84db499b10d4ed2a57412a9de94', 'FENDO WIJAYA LIE', 'SMP', '8E', 'Siswa'),
(234, '18197033', '57b334b04128bf751c2cd754ec98cc19', 'GABRIEL BRIAN WIBOWO', 'SMP', '8E', 'Siswa'),
(235, '18197061', '9bc68c5e5449feb272dcc02bc3320de0', 'HANSEL ALEXANDER', 'SMP', '8E', 'Siswa'),
(236, '18197113', '5e172d01dade2a7df2aac00891cc716b', 'IVANNA TANTASAPUTRA', 'SMP', '8E', 'Siswa'),
(237, '18197114', 'ed8abc637b020ebe636c9d08b496dddf', 'JEHUDA SATRIO NACHEMYA', 'SMP', '8E', 'Siswa'),
(238, '18197116', '73cea4013b77c24b761c1e6d9fb5f2ed', 'JOSEPHINE ANDREA SANJAYA', 'SMP', '8E', 'Siswa'),
(239, '18197117', 'ccef4d852ad9539fc4ba5daf02f0d3c1', 'JOVIAN NATHANIEL SUTJIONO', 'SMP', '8E', 'Siswa'),
(240, '17187120', 'e3af709f6c4e9c52a3cf94fd85d5e8de', 'KAY KENTMOSS SUNARYA', 'SMP', '8E', 'Siswa'),
(241, '18197102', 'e176c98f018c8fac093464a180b5a9f3', 'MANUELL SUNG INDRAPUTRA', 'SMP', '8E', 'Siswa'),
(242, '18197070', 'fcba6c73dbff12ca622a4aef6be531b8', 'NATANAEL BRAM WICAKSONO', 'SMP', '8E', 'Siswa'),
(243, '18197122', 'e63f652dfc2991e233ee0d51455feec9', 'PHELIA NATHANIA WOWOR', 'SMP', '8E', 'Siswa'),
(244, '18197022', 'b8d2f8c9f4f08e9db96537f746126995', 'RICHARD VALDEMA WIDJAYA', 'SMP', '8E', 'Siswa'),
(245, '18197048', '1640fd523c5214e8fdffe148386b9fe8', 'SILVANUS ELYASIB PRATIGNO', 'SMP', '8E', 'Siswa'),
(246, '18197127', 'c2c34087ac1511dc8edc4feae5e02a2b', 'STEVANIE KURNIAWAN', 'SMP', '8E', 'Siswa'),
(247, '18197103', '2f0cc04294f9503d8a05fdb2a155221d', 'TIFFANY CHRISTINA DJOKO S', 'SMP', '8E', 'Siswa'),
(248, '18197024', '13c3b3d37ef4dea83b2f73766543ba07', 'VICKY SHUEN', 'SMP', '8E', 'Siswa'),
(249, '18197026', '7c0eda818e8cc3b12ce569ce92b38a65', 'YOSEPHINE EMANUELLE EKAPUTRI SUWARNO', 'SMP', '8E', 'Siswa'),
(250, '18197080', '305cd69814982ecc79505f2d6585e917', 'ABIGAIL KRISTUTIARA K', 'SMP', '8C', 'Siswa'),
(251, '18197086', '640ac240f45f37e443f2e082d823f2e8', 'CATHERINE NATHANAEL', 'SMP', '8C', 'Siswa'),
(252, '18197110', '5a454e21a10176bd6a9473148a9e1e34', 'CHRISTIAN RAFAEL WITONO', 'SMP', '8C', 'Siswa'),
(253, '18197058', 'af7aaed952c6af78bdcf89b5765e359c', 'CINDY FRANSISCA SUWANDI', 'SMP', '8C', 'Siswa'),
(254, '18197092', '25e24019627a38b251f0684d14c58fb6', 'ELIZABETH JACELYN RIKIYANTO', 'SMP', '8C', 'Siswa'),
(255, '16177064', 'b4d6401394a6593fd198e0b1737f46bb', 'FELICIA AURELIA ', 'SMP', '8C', 'Siswa'),
(256, '18197010', '6d4c500033a0e6d1cee0829113e193dd', 'FLORENCE STEFANIE', 'SMP', '8C', 'Siswa'),
(257, '18197095', '7255c8d0bff53aea0ae99c83f4feac3c', 'GALEN WHARTON', 'SMP', '8C', 'Siswa'),
(258, '18197096', 'fe5f09f24c5fd203d2e6308e44076aee', 'GERALDUS SEAN GUNAWAN', 'SMP', '8C', 'Siswa'),
(259, '18197034', '76a033715c850857d7d958bcacffe1e8', 'GIOVANNI SEBASTIAN', 'SMP', '8C', 'Siswa'),
(260, '18197035', '38910894d975113f1fbfad0830ff8db3', 'GREGORIUS JONATHAN CHANDRA', 'SMP', '8C', 'Siswa'),
(261, '18197036', 'b24108f924f2ac97129990772775c173', 'IONA CORY', 'SMP', '8C', 'Siswa'),
(262, '18197098', '3558d6ca814c4bc8f157bf0fd933c15c', 'JAVIER LEANDER WIJAYA', 'SMP', '8C', 'Siswa'),
(263, '18197039', '34f78e2c4a02f8a2127060c39aec7aa2', 'JENNIFER ANGEL', 'SMP', '8C', 'Siswa'),
(264, '18197015', '5d3974a098bb8ec078d38d211317ba45', 'JOSH SATRIO NACHEMYA', 'SMP', '8C', 'Siswa'),
(265, '18197118', 'bf8e5d3a7e8cc84a5bc2ff7288a8f512', 'JUSTIN AUDRIAN CANDRA', 'SMP', '8C', 'Siswa'),
(266, '18197119', 'd67506587d44db40af91435d819baf90', 'LIBRAN ARGA KUSUMA', 'SMP', '8C', 'Siswa'),
(267, '18197019', '5c3936309d09dfe5305d43c3a48d49f5', 'MICHAEL KHISLEW SEMBIRING', 'SMP', '8C', 'Siswa'),
(268, '19208154', '112f90fd650f76186fe6674ed9ad0a81', 'NARENDRA BUMI', 'SMP', '8C', 'Siswa'),
(269, '18197125', '91189e8c087a9650f483b42b7777f4e6', 'RHEA ELVIRA CHANG', 'SMP', '8C', 'Siswa'),
(270, '18197044', '6cb602f96ba835796e4e6ac1b0d288a5', 'RIALAN DUMARIS SIHOMBING', 'SMP', '8C', 'Siswa'),
(271, '18197073', 'b8d1eb09655133a65fb42e847d780d40', 'RIBY RENATA FIDELIA', 'SMP', '8C', 'Siswa'),
(272, '18197074', 'edd12d7655828a90967dd1d673e71558', 'RICO DHARMAWAN', 'SMP', '8C', 'Siswa'),
(273, '18197047', 'b3d67baf07229bb4921617959ed18558', 'SIBURIAN, SUSAN HUMERI', 'SMP', '8C', 'Siswa'),
(274, '18197049', '0f26a678444a7283ead5509ffa90c7f8', 'STEVEN APTARINO PRATAMA SIRINGORINGO', 'SMP', '8C', 'Siswa'),
(275, '18197076', '4de5abc5da4d97f425cc67704cabd372', 'VALERIANUS MYCOLA LENGKONG', 'SMP', '8C', 'Siswa'),
(276, '18197106', 'c8c29f29b98184c38ebd32f5b7d657c8', 'ADRIENNE AMALIA TEDJA SAPUTRA', 'SMP', '8D', 'Siswa'),
(277, '18197054', '070c909100f17640c10813deee00a504', 'ANGELLICA DARMAWAN ', 'SMP', '8D', 'Siswa'),
(278, '18197004', 'df707b6c5aafa3fd57188554bbb35029', 'ARIMA, KEN', 'SMP', '8D', 'Siswa'),
(279, '18197085', 'd308bebd061790ef860d8d15da82c7e3', 'CALLISTA CLARABELLE', 'SMP', '8D', 'Siswa'),
(280, '18197087', 'f113111eeb25d480e4bbf85e273ef778', 'CHELSEA OLIVIA', 'SMP', '8D', 'Siswa'),
(281, '18197029', '8d8b63378a22abfea807fc9b672ebb8d', 'CHRISTELLA CINDY WIJAYA', 'SMP', '8D', 'Siswa'),
(282, '18197089', '62a1775f0ee91d2b876239762d8c2e49', 'CLIFFORD LIE', 'SMP', '8D', 'Siswa'),
(283, '18197031', '5990563e8eb00fa4fa682e17f344e4bb', 'FABIO RAFFAEL WOWOR', 'SMP', '8D', 'Siswa'),
(284, '18197007', '9390bc0056e5f4041ac8ca354d422c9e', 'FELICIA AUDREY SUSANTO', 'SMP', '8D', 'Siswa'),
(285, '18197012', 'c17cfcbb3380f4357f307d361d60d9c5', 'GRACIELLA BRENDA LINATA', 'SMP', '8D', 'Siswa'),
(286, '18197040', '80d6723b9c98f59790c8b97a2e14544d', 'JENNIFER GERALDINE CLARISSA', 'SMP', '8D', 'Siswa'),
(287, '18197014', '893b60919797c1f7b75f70d9d25f0dff', 'JEVIRLY GLORIA WIJAYA PUTRI', 'SMP', '8D', 'Siswa'),
(288, '19208155', '5d75fe04090893a67e255cbfba65e22d', 'JOEL SEBASTIAN PURBA', 'SMP', '8D', 'Siswa'),
(289, '18197100', '0c2c5b34783fb2d987790c10237eab3b', 'KEITARO', 'SMP', '8D', 'Siswa'),
(290, '18197065', '0dcabf4e13599c1f38ed2257eb7b9714', 'KEZIA CLAIRE ADAM', 'SMP', '8D', 'Siswa'),
(291, '18197066', '1aa9a379e3855eb20904430384b0e3a3', 'MARCELLINO JUAN CAHYA', 'SMP', '8D', 'Siswa'),
(292, '18197016', 'afa316c608e22f0edcc1376d066915f3', 'MARVIN HERMAWAN', 'SMP', '8D', 'Siswa'),
(293, '18197018', 'b52c99507e948ecea136f19cde888ad0', 'MATTHEW RIOVANO', 'SMP', '8D', 'Siswa'),
(294, '18197067', '147d48a2552b5097c3aa88d0a889553a', 'MELVIN IGNATIUS INDRA', 'SMP', '8D', 'Siswa'),
(295, '18197068', '8c94969387b6a7f547ee284794e97430', 'MICHAEL FELIX WIBOWO', 'SMP', '8D', 'Siswa'),
(296, '18197071', '28abd341b8b4b64d3b1136b990d1d5cb', 'NATHANIA ROSALIND SUMARDI', 'SMP', '8D', 'Siswa'),
(297, '18197072', '9ad6d5aa809a0bbc14d568de00254f33', 'NATHANIEL CHRIST ANDERSON', 'SMP', '8D', 'Siswa'),
(298, '18197021', 'b8cd35b23178ea5687ce469b215de5fb', 'NICHOLAS ZEFANYA TJAHYADI', 'SMP', '8D', 'Siswa'),
(299, '18197123', '100a0df0bdad8a0fc6c68fce3443f068', 'PUAN CARMELA PARAMITA', 'SMP', '8D', 'Siswa'),
(300, '18197104', '4cc561c93879ea043cfaa4564c124ac0', 'TIMOTHY ALEXIS DEBATARAJA', 'SMP', '8D', 'Siswa'),
(301, '18197078', '7dbd7db240ff324380a4bdd0d73eff57', 'WILLIAM GIBREN PASKAH MANIH', 'SMP', '8D', 'Siswa'),
(302, '17187104', '2de49f82f61cb784d527cbb5903e96a2', 'AGATHA PARAMESWARI GALUH S', 'SMP', '9A', 'Siswa'),
(303, '17187052', '9d70e2c1f0747b0e64e051b986fe73ca', 'AILEEN CHANDRA WIJAYA', 'SMP', '9A', 'Siswa'),
(304, '17187003', '08d2bf7702e6f91dcce4e271a05a1275', 'ANGELA ANDRIANA', 'SMP', '9A', 'Siswa'),
(305, '17187055', 'a1547a7cee7087cfc79af3ae9ac810de', 'BERNADUS ANDRE NARAWANGSA', 'SMP', '9A', 'Siswa'),
(306, '17187082', 'fc3ce2c5c8f61edee7c1eaa1284adf9a', 'CHRISTABEL VICTORIA MITZY', 'SMP', '9A', 'Siswa'),
(307, '17187109', '55d6dfd37dbe57d007585c1e959c1453', 'CHRISTOPHER BRYAN BUDI S', 'SMP', '9A', 'Siswa'),
(308, '17187117', '41324c6b2822c098742a3ce41c11c4d3', 'FLORENCIA JEANNY NOVITA', 'SMP', '9A', 'Siswa'),
(309, '17187086', '3d17ca36b456c969b0c64b7a55fb5a01', 'FREDERICKO KEVIN TANGSUL A', 'SMP', '9A', 'Siswa'),
(310, '17187038', '0fda8b474738056cd99bd6fe6ec66722', 'GERARDUS VERREL EGA WIDIATMOKO', 'SMP', '9A', 'Siswa'),
(311, '17187089', '1b9953d6afbacd3c2a0e4a9a5c063338', 'JEREMY SEAMAND HO', 'SMP', '9A', 'Siswa'),
(312, '17187041', '9b87c0b899cbb6d8796959f2c1ca9831', 'JOCELYN FELICIA GERALDINE V', 'SMP', '9A', 'Siswa'),
(313, '17187013', 'f4b72a5305a454ab83180c16c00989bd', 'JONATHAN NATHANAEL', 'SMP', '9A', 'Siswa'),
(314, '17187091', 'd7b514c5f0f272547450d649ebf9b95d', 'JOSHUA ABRAHAM ', 'SMP', '9A', 'Siswa'),
(315, '17187094', '37fa7bce713eb6baea01a630c9de3df5', 'MARVIN DARREN', 'SMP', '9A', 'Siswa'),
(316, '17187067', '93ab14f6b944e0cf5ab81c6261ada5c9', 'MICHAEL KENZO SUYANTO', 'SMP', '9A', 'Siswa'),
(317, '17187050', '7f6895c75a4082ca185ac1dcb4c9c18d', 'OCTAVIA ANJELITA', 'SMP', '9A', 'Siswa'),
(318, '18198131', '17713b517043b257541de8f84cb422de', 'OWEN LIANGGARA', 'SMP', '9A', 'Siswa'),
(319, '17187069', 'fece5592f1f290998b5cbd64079af997', 'RENATA KATELYN SANJAYA', 'SMP', '9A', 'Siswa'),
(320, '17187125', '748b191a966be966ac4bc58563eec0fd', 'RENATA KEISHIA ', 'SMP', '9A', 'Siswa'),
(321, '17187127', '57857cd7efb7a7a44db3171b77fd33df', 'SHERRI AURELIA ', 'SMP', '9A', 'Siswa'),
(322, '18198133', '30e0f8d16a120f30cda773f8f69521b4', 'STELLA AMORA PRASETYA', 'SMP', '9A', 'Siswa'),
(323, '17187101', '64e8901473d1ed5403a81998a1cd9f16', 'VANDYKA SURYADI', 'SMP', '9A', 'Siswa'),
(324, '17187103', '478bb7808bcc3c9e3bb837f533c8fb2a', 'VIRLY SETIADI PRATAMA', 'SMP', '9A', 'Siswa'),
(325, '17187026', 'd1ed68f0904890d39f0ae881b088c719', 'WIDYA CALLISTA', 'SMP', '9A', 'Siswa'),
(326, '16177055', 'e56fda017d2a100783d43397d698a51e', 'WILLIAM HERLYANTO', 'SMP', '9A', 'Siswa'),
(327, '17187002', '67e82151c1d634572cff2a4257b14d8c', 'ALETTHA DINI ZAKARIA S', 'SMP', '9B', 'Siswa'),
(328, '17187053', '493f2ef8e85cfa0318d4fd83e670434d', 'ALEXANDRA STEVANNY MARETTA', 'SMP', '9B', 'Siswa'),
(329, '17187027', '0a98e49713c29e7ac07eb5dc37fc7a4e', 'ANDREW KEVIN ALEXANDER', 'SMP', '9B', 'Siswa'),
(330, '17187106', 'c79d2743e440f74b6e1f7260ab9321d3', 'ASTON MARTIN BENEDICT G', 'SMP', '9B', 'Siswa'),
(331, '17187078', 'e8d820b7f1d3a36cf87ab01d3fb12dd5', 'AUDREY FELICIA', 'SMP', '9B', 'Siswa'),
(332, '17187004', '82c11f2376f91d4b0a5f4dbdf7456807', 'AURELIA CALLISTA', 'SMP', '9B', 'Siswa'),
(333, '17187031', '15b3970ff2a3e062a70fe903039a1ddc', 'CARLA ANGELICA', 'SMP', '9B', 'Siswa'),
(334, '17187108', '8d2338258b5fdc202ffb8791bc7c532e', 'CASEY NATASHA AURELIN', 'SMP', '9B', 'Siswa'),
(335, '17187034', '6fa2613e32eb8f465d62dac52306a99e', 'DAVIN CHANDRA SETIAWAN', 'SMP', '9B', 'Siswa'),
(336, '17187057', '916c8f65de1859a3026702d56b0f7778', 'DINA DELIA', 'SMP', '9B', 'Siswa'),
(337, '17187114', '67d2da2e0763a7263cd880357fa8de1a', 'ESTHER VANIA', 'SMP', '9B', 'Siswa'),
(338, '17187009', '92c1517b27bf74ffad3916d0a88c7b95', 'FEBY CHRISANDRA', 'SMP', '9B', 'Siswa'),
(339, '17187039', '91b4bac88da6032f448c3acc6c85844d', 'HOSEA STEFAN WINARTO', 'SMP', '9B', 'Siswa'),
(340, '17187012', '0a9998a932ca3fbcd796a0472cc2ca3e', 'JOHANNES DARREN YEHUDA', 'SMP', '9B', 'Siswa'),
(341, '17187060', '2268797b69928889c065dc10ac0c0507', 'JONATHAN TRITO PALLAS', 'SMP', '9B', 'Siswa'),
(342, '17187014', '8754f77d39840cde3124b00adfb8c410', 'JOVITA EVELYN TANAKA', 'SMP', '9B', 'Siswa'),
(343, '17187015', 'afb892be461f60a3642af23e6d30e2eb', 'JUSTIN FARRELL KRISTIANTO', 'SMP', '9B', 'Siswa'),
(344, '17187093', 'a68bb53f2f8c4532ceca7a8b2b9264c5', 'MARIPOL TUA DANIEL SITANGGANG', 'SMP', '9B', 'Siswa'),
(345, '17187049', '934f149cec1d59ffb2d157822c91fd82', 'MICHAEL ALFONS WONG', 'SMP', '9B', 'Siswa'),
(346, '17187020', 'db8376646f7c976154732fa35f409784', 'NATHANAEL HANS', 'SMP', '9B', 'Siswa'),
(347, '17187071', '2066fad287db78183c44ea24437387e1', 'RICHITA MAE', 'SMP', '9B', 'Siswa'),
(348, '17187072', '27da3af2788b592ae29b2c3d2c4a0ec6', 'RONALD WIDURA', 'SMP', '9B', 'Siswa'),
(349, '17187022', 'c2e029f9824670482784170c3866a8d1', 'SAMUEL ALVIN FERDINAND', 'SMP', '9B', 'Siswa'),
(350, '17187099', 'f58078c803e126888d68e89037014406', 'SHARLEEN CHANDRA ', 'SMP', '9B', 'Siswa'),
(351, '17187073', 'caa5cff48c1613c385c8dd11d6e9a3b8', 'SHERLY MARDINA LODANI', 'SMP', '9B', 'Siswa'),
(352, '17187054', '2909a84c3c4c0a2180dcd72a260fa223', 'ARIEL WILLIAM VERSTRAETEN', 'SMP', '9C', 'Siswa'),
(353, '17187005', 'ad60698f66c8c30f65bdb717986c3357', 'BENEDIKTUS EVANDY UNAMO', 'SMP', '9C', 'Siswa'),
(354, '17187080', 'ab99a20407f1234a7be2cbde9374c21f', 'CAVELL GUNAWAN', 'SMP', '9C', 'Siswa'),
(355, '17187083', '459f10c4da57bb7873411371719d365f', 'CHYNTHIA CHANDRA', 'SMP', '9C', 'Siswa'),
(356, '17187007', 'b4d7eaaffc69e08265ee839b4dc933e2', 'DYTA NADYA FLORENTYNA', 'SMP', '9C', 'Siswa'),
(357, '17187113', 'bde89d3ef5ebba603ff1da5caed6ce29', 'ELISABETH MARSELLA', 'SMP', '9C', 'Siswa'),
(358, '17187035', '361a73e96071670dc9ce23896ed442d2', 'EVAN WAHYUDI', 'SMP', '9C', 'Siswa'),
(359, '17187008', 'fd3768b9b44278ac1e8432d3cd399a8f', 'EVODIUS OKKI VERREL', 'SMP', '9C', 'Siswa'),
(360, '17187036', 'dbf0559300a9ecf218671474c5b82263', 'FELISHA ANGELA CHRISTIAN', 'SMP', '9C', 'Siswa'),
(361, '17187085', 'a571cce93efbebd6aca6d0938f56db95', 'FILEMON JOAN SUMITRO', 'SMP', '9C', 'Siswa'),
(362, '17187116', '789241e2e227a25f8b5f4f9f0d4253ea', 'FLARENTHIA ABIGAIL YAKIM', 'SMP', '9C', 'Siswa'),
(363, '17187059', 'fba6c8de21466e6ea788f1fb7629eecd', 'IVANA REGINA PATRICIA', 'SMP', '9C', 'Siswa'),
(364, '17187043', 'ca426aa534238cb7cd9c2c04233922db', 'JOVITA CORNELIA PUTRI HIZKIA', 'SMP', '9C', 'Siswa'),
(365, '17187063', '4430af486ed40d923a90fe46aca5f61b', 'KENNETH NATHANAEL', 'SMP', '9C', 'Siswa'),
(366, '17187122', '868adc0d8643e9374f31b912c85725b7', 'LAURENCIA HARYANTO', 'SMP', '9C', 'Siswa'),
(367, '17187017', 'c809aa2b6a83374c8c875e84af0c67db', 'LEONARDO STANLEY HUBERT', 'SMP', '9C', 'Siswa'),
(368, '17187095', '37d28913a91f2430fe10046fff87ee36', 'MARVIN LUCKYANTO', 'SMP', '9C', 'Siswa'),
(369, '17187021', 'bed6e15fe2e84e8480dd65279f243b7c', 'NATHASYA AGUSTA CAROLINE S', 'SMP', '9C', 'Siswa'),
(370, '17187070', '33bbea949ccb90cca084499b16ca3178', 'RENATA MELIANA SUTARSA', 'SMP', '9C', 'Siswa'),
(371, '17187126', '685e24e6ca79f5d10536dcfaa3243959', 'ROSSALINA', 'SMP', '9C', 'Siswa'),
(372, '17187023', 'e7c9f58b7fd67db8a5013419b0132664', 'SEBASTIAN JONATHAN', 'SMP', '9C', 'Siswa'),
(373, '17187100', '6bb2d7584128ca41133bce34b1316457', 'STARLA OLIVIA HARTANTO', 'SMP', '9C', 'Siswa'),
(374, '17187128', 'bbceabedc4455c7191f6f4958981c54b', 'STEFANUS FERNANDO', 'SMP', '9C', 'Siswa'),
(375, '17187074', '6283a0bb9acba7a89607b4868e60995b', 'TEODORUS NATHAN ', 'SMP', '9C', 'Siswa'),
(376, '17187105', '5c5fbcca878923eb8d33f2932fac4c4b', 'ANGELINA WIDJAJA', 'SMP', '9D', 'Siswa'),
(377, '17187028', 'c095ac8c6b524508b6e7782d8e120a53', 'ANGELIQUE MARCELLA DEVALINDA SONTANI', 'SMP', '9D', 'Siswa'),
(378, '17187107', '688e65a50a4f60b7bd5a0593c6d90540', 'AXEL CHRISTIAN', 'SMP', '9D', 'Siswa'),
(379, '17187056', '5c74a55a5155ad5e2f84231bdde4061b', 'BRIGITTA SHARON CHAVARA TANUBERATA', 'SMP', '9D', 'Siswa'),
(380, '17187006', '1c9c86f92a366e10f511191822aec211', 'CHERYL AURELIA SUPRIADY', 'SMP', '9D', 'Siswa'),
(381, '17187110', '8d6c968e856dc85e60d1912a11771b9a', 'CHRISTOPHER EVAN WIJAKSANA', 'SMP', '9D', 'Siswa'),
(382, '17187032', 'd0ed174b3aa34ac34179ca756a73360a', 'CLEOSA ESTHER VALERIE', 'SMP', '9D', 'Siswa'),
(383, '17187112', '555a62fb7cb975eda813bdccf4e87a7c', 'DAVE SANTOSA', 'SMP', '9D', 'Siswa'),
(384, '17187010', '3eb389d3116e5f4d6565ac9b27349443', 'FELICIA FRANCINE KURNIAWAN', 'SMP', '9D', 'Siswa'),
(385, '17187118', 'a039b0d961232088ce22ab764890cd67', 'GABRIELLA AUBREY SEKARAYU HUTOMO', 'SMP', '9D', 'Siswa'),
(386, '17187040', 'd9a6cf0961309aff31fc93daeb75089f', 'JESSICA ALEXANDRA TJIANG', 'SMP', '9D', 'Siswa'),
(387, '17187090', '09fc83eb09926d6e219534211817c5ee', 'JOSEPH DAVIN CHRISTIAN', 'SMP', '9D', 'Siswa'),
(388, '17187062', 'ae5afb62563122cb992886c5c3ec059c', 'KAREN GABRIELLE', 'SMP', '9D', 'Siswa'),
(389, '17187016', '15cf03370e19c5a0250d79ae17ce203e', 'KENZIE GUNAWAN ', 'SMP', '9D', 'Siswa'),
(390, '17187092', '147de6feda48db1773ef446a3f7237e2', 'LEVIN FAMOSA INDRAJAYA', 'SMP', '9D', 'Siswa'),
(391, '17187019', 'e629278cd97ff934615c4baa82bc8c82', 'MELVIN LAWRENTIUS', 'SMP', '9D', 'Siswa'),
(392, '17187123', 'be5beabe20610ca6333d531799f6b6c6', 'MICHAEL ANTHONY CAHYO ', 'SMP', '9D', 'Siswa'),
(393, '17187068', 'd401fb1e4887a02d5839b6c4f6386edf', 'NATASYA', 'SMP', '9D', 'Siswa'),
(394, '17187124', 'db405deaa9e62e8fafb8c2a518e05c9c', 'NELSON FERDINAND WANGSAPUTRA', 'SMP', '9D', 'Siswa'),
(395, '17187096', '9980320a6bc5d5f4826be5392beff432', 'RAEYNARD OWEN TALASSA', 'SMP', '9D', 'Siswa'),
(396, '17187097', '35b5490a8915cc243413707120dd0712', 'REVALINA KEISHANY PUTRI LESMANA', 'SMP', '9D', 'Siswa'),
(397, '17187024', '1d85c0a96ebf7bc3e155dbdd67ae5a0b', 'STEFANUS DEAN KRISTIANTO', 'SMP', '9D', 'Siswa'),
(398, '17187075', '1a86b9a12d6c9e3317f66680d0a6b891', 'VALERIE VIVALDY JOSHUA', 'SMP', '9D', 'Siswa'),
(399, '17187102', '2fe4ede5d53cfee4333a65101b4107f6', 'VANIA ANGELICA', 'SMP', '9D', 'Siswa'),
(400, '17187077', 'ab38b2cee71daab982353c5b5a5df755', 'VIVIAN LYN ANDERSON', 'SMP', '9D', 'Siswa'),
(401, '17187001', 'f213288df7321a0de84ae4dca4fded4b', 'AGNES ZEANETA LARASATI AUDREY', 'SMP', '9E', 'Siswa'),
(402, '17187029', 'fa8564200bd7d967291c47996f11333a', 'AXEL JEREMY ', 'SMP', '9E', 'Siswa'),
(403, '17187030', '88599792321dc5b410525571e1933e79', 'CAENCILIA JESSICA', 'SMP', '9E', 'Siswa'),
(404, '17187111', 'e5219f46e42b2d699617d05de82c8f5f', 'CLEOSA SURYAJAYA', 'SMP', '9E', 'Siswa'),
(405, '17187033', '8672333373246559a76f3fcb71cdbb8e', 'DANNY', 'SMP', '9E', 'Siswa'),
(406, '17187084', '8ed26c68c803fc4076e6b2f37403e7ac', 'DESTINE EMELLY GUNAWAN', 'SMP', '9E', 'Siswa'),
(407, '17187037', '9ec1d58d4aee33c93ec30f231aed48f2', 'GABRIEL JOVITA', 'SMP', '9E', 'Siswa'),
(408, '17187058', 'dd5cfc7e8d8e33457e5f6004a2735c65', 'GAVRIEL REYNARD NATHANAEL', 'SMP', '9E', 'Siswa'),
(409, '17187119', 'adde72a9880e25d04086816b9be478d8', 'GODJHONSON FRANSISKUS NAINGGOLAN', 'SMP', '9E', 'Siswa'),
(410, '17187088', '5489e0318e9ed42a268dcf01d423cdf5', 'JANE AMERLLY', 'SMP', '9E', 'Siswa'),
(411, '17187011', '6673874eccfbbde62537dc574d5c36a0', 'JANICE CHARITY SANTOSO', 'SMP', '9E', 'Siswa'),
(412, '17187042', '70497c791aa816beeba09246fda8a546', 'JONATAN STEVEN HANDIKA', 'SMP', '9E', 'Siswa'),
(413, '17187061', '1ee6c6ff7154fcb89c508549b001743a', 'JOVITA HARIS', 'SMP', '9E', 'Siswa'),
(414, '17187121', 'fc395f3a067a347fcacb1936a8745646', 'KENNETH SIMEON ANDREAS', 'SMP', '9E', 'Siswa'),
(415, '17187044', 'ff63d595ab0d01cbad7b354299955ac9', 'KEYNEIVA WIJAYA', 'SMP', '9E', 'Siswa'),
(416, '17187046', '5c79f9d004e3cc8e720badd456ac08a9', 'KRISTOPHORUS KEVIN FISICHELLA', 'SMP', '9E', 'Siswa'),
(417, '17187064', '14bebcf87bf11671ae85684b99c3d5a2', 'MADELLINE MARGARETHA JEVINA', 'SMP', '9E', 'Siswa'),
(418, '17187065', '2f88e43f2564474c9e176bda14a97967', 'MARCELIUS STEVAN', 'SMP', '9E', 'Siswa'),
(419, '17187018', 'e47d18d2627f2a46e3b882ebfcf53111', 'MARIO CAHYANA', 'SMP', '9E', 'Siswa'),
(420, '17187066', '5412e1679757aa01e25edce366ad67cb', 'MICHAEL DEVON JANUAR', 'SMP', '9E', 'Siswa'),
(421, '17187098', '7c969d0c6d9756391e495f617121916b', 'SHANELLA CHRISTINA', 'SMP', '9E', 'Siswa'),
(422, '17187076', '3b3766e1cd6a7e6b18c0170ee2366783', 'VINCENTIUS DARREN SETIAWAN', 'SMP', '9E', 'Siswa'),
(423, '17187025', '65ebc38865e54ff25b6059059552cdae', 'VIVIAN FLORENCY', 'SMP', '9E', 'Siswa'),
(424, '17187051', '089fbd397e927219132feb16fbb46529', 'WILSON SETIAWAN ', 'SMP', '9E', 'Siswa'),
(452, '5038', 'dd84def751fcc0553dcb958dff81d166', 'Muhamad Ikhsan Alfaruq', 'SMP', NULL, 'Guru'),
(453, '10259', 'a390d99e45a8d110acd04724a2327e5a', 'Dra. ANASTASIA WASUTI', 'SMP', NULL, 'Guru'),
(454, '10413', 'ff31bb189b479ebcc60ef0fb0645b732', 'YOSEP YAYA KARYANA,S.Pd.', 'SMP', NULL, 'Guru'),
(455, '10591', '18fdc57c8d36124730dceb2d8410313e', 'DWI HERYANTI BUDI RAHAYU, S.T.', 'SMP', NULL, 'Guru'),
(456, '10788', '27b283a80a76dd9ae3ea31712bcbb091', 'SUNGGUL PANJAITAN, S.Pd.', 'SMP', NULL, 'Guru'),
(457, '10827', '371926ebad7500a9974533bc27ff3ea4', 'Yeni Sunarsih, S.Pd.', 'SMP', NULL, 'Guru'),
(458, '10829', 'a8de3a37a394fe4e8feee4edb52b5b95', 'RENNI MAGDALENA NAHAMPUN, S.Pd.', 'SMP', NULL, 'Guru'),
(459, '10878', 'a3a4db1912540dec0e593985c76a7b63', 'LASTRI PUTRI RISMAULY, S.Pd.', 'SMP', NULL, 'Guru'),
(460, '11088', '05fccf4550741f618635fc44827d61f7', 'Febrianiko Kristian', 'SMP', NULL, 'Guru'),
(461, '41437', '509f9                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       