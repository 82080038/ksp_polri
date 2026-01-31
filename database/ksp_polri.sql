-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 31 Jan 2026 pada 22.06
-- Versi server: 8.0.44-0ubuntu0.24.04.2
-- Versi PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ksp_polri`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `agents`
--

CREATE TABLE `agents` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `partner_id` int DEFAULT NULL,
  `jenis_agen` enum('ANGGOTA','PENGURUS','PENGAWAS','PIHAK_KETIGA') DEFAULT NULL,
  `wilayah_penjualan` varchar(255) DEFAULT NULL,
  `komisi_persen` decimal(5,2) DEFAULT NULL,
  `batas_kredit` decimal(15,2) DEFAULT '0.00',
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `agent_commissions`
--

CREATE TABLE `agent_commissions` (
  `id` int NOT NULL,
  `agent_id` int DEFAULT NULL,
  `agent_sale_id` int DEFAULT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `total_penjualan` decimal(15,2) DEFAULT NULL,
  `total_komisi` decimal(15,2) DEFAULT NULL,
  `status_pembayaran` enum('PENDING','PAID') DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `agent_sales`
--

CREATE TABLE `agent_sales` (
  `id` int NOT NULL,
  `agent_id` int DEFAULT NULL,
  `nomor_transaksi` varchar(50) DEFAULT NULL,
  `tanggal_penjualan` date DEFAULT NULL,
  `pelanggan_nama` varchar(255) DEFAULT NULL,
  `pelanggan_alamat` text,
  `pelanggan_telp` varchar(50) DEFAULT NULL,
  `total_nilai` decimal(15,2) DEFAULT NULL,
  `komisi` decimal(15,2) DEFAULT NULL,
  `status_approval` enum('PENDING','APPROVED','REJECTED') DEFAULT NULL,
  `bukti_transaksi_path` varchar(255) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `agent_sales_details`
--

CREATE TABLE `agent_sales_details` (
  `id` int NOT NULL,
  `agent_sale_id` int DEFAULT NULL,
  `produk_id` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `harga_jual` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL,
  `komisi_item` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id` int NOT NULL,
  `nrp` varchar(20) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `pangkat` varchar(50) DEFAULT NULL,
  `satuan` varchar(100) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `status` enum('AKTIF','NONAKTIF') DEFAULT 'AKTIF',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id`, `nrp`, `nama`, `pangkat`, `satuan`, `user_id`, `status`, `created_at`) VALUES
(1, '1234567890', 'Anggota Demo', 'Aipda', 'KSP POLRI', NULL, 'AKTIF', '2026-01-31 20:24:38'),
(2, '0987654321', 'Pengurus Demo', 'Ipda', 'KSP POLRI', NULL, 'AKTIF', '2026-01-31 20:24:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `angsuran`
--

CREATE TABLE `angsuran` (
  `id` int NOT NULL,
  `pinjaman_id` int DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_depreciations`
--

CREATE TABLE `asset_depreciations` (
  `id` int NOT NULL,
  `asset_id` int DEFAULT NULL,
  `periode` varchar(20) DEFAULT NULL,
  `nilai_depresiasi` decimal(15,2) DEFAULT NULL,
  `nilai_buku_setelah` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `aksi` varchar(100) DEFAULT NULL,
  `data_lama` text,
  `data_baru` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `capital_changes`
--

CREATE TABLE `capital_changes` (
  `id` int NOT NULL,
  `investment_id` int DEFAULT NULL,
  `jenis` enum('PENAMBAHAN','PENGURANGAN') DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `alasan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `capital_investments`
--

CREATE TABLE `capital_investments` (
  `id` int NOT NULL,
  `investor_id` int DEFAULT NULL,
  `nomor_perjanjian` varchar(100) DEFAULT NULL,
  `besar_modal` decimal(15,2) DEFAULT NULL,
  `tanggal_penyertaan` date DEFAULT NULL,
  `tanggal_berakhir` date DEFAULT NULL,
  `persentase_kepemilikan` decimal(5,2) DEFAULT NULL,
  `syarat_ketentuan` text,
  `dokumen_perjanjian_path` varchar(255) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `produk_id` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `id` int NOT NULL,
  `kode_akun` varchar(20) DEFAULT NULL,
  `nama_akun` varchar(255) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `saldo_awal` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `contracts`
--

CREATE TABLE `contracts` (
  `id` int NOT NULL,
  `partner_id` int DEFAULT NULL,
  `nomor_kontrak` varchar(100) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_berakhir` date DEFAULT NULL,
  `nilai_kontrak` decimal(15,2) DEFAULT NULL,
  `syarat_ketentuan` text,
  `dokumen_path` varchar(255) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_hash`
--

CREATE TABLE `dokumen_hash` (
  `id` int NOT NULL,
  `nomor_dokumen` varchar(50) DEFAULT NULL,
  `jenis_dokumen` varchar(50) DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `hash` varchar(64) DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dibuat_oleh` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('sent','failed','pending') DEFAULT 'pending',
  `error_message` text,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `id` int NOT NULL,
  `kode_aset` varchar(50) DEFAULT NULL,
  `nama_aset` varchar(255) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `nilai_perolehan` decimal(15,2) DEFAULT NULL,
  `tanggal_perolehan` date DEFAULT NULL,
  `metode_depresiasi` varchar(50) DEFAULT NULL,
  `umur_ekonomis` int DEFAULT NULL,
  `nilai_buku` decimal(15,2) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_categories`
--

CREATE TABLE `forum_categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT 0xF09F92AC,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `forum_categories`
--

INSERT INTO `forum_categories` (`id`, `name`, `description`, `icon`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Pengumuman', 'Pengumuman resmi dari pengurus KSP POLRI', '📢', 1, 1, '2026-01-31 19:42:50'),
(2, 'Diskusi Umum', 'Diskusi umum antar anggota', '💬', 2, 1, '2026-01-31 19:42:50'),
(3, 'Pertanyaan & Jawaban', 'Tanya jawab seputar KSP dan simpan pinjam', '❓', 3, 1, '2026-01-31 19:42:50'),
(4, 'Saran & Masukan', 'Saran dan masukan untuk kemajuan KSP', '💡', 4, 1, '2026-01-31 19:42:50'),
(5, 'Cerita Anggota', 'Berbagi cerita dan pengalaman sebagai anggota KSP', '📖', 5, 1, '2026-01-31 19:42:50'),
(6, 'Off Topic', 'Diskusi di luar topik KSP (dengan batas)', '🎭', 6, 1, '2026-01-31 19:42:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int NOT NULL,
  `thread_id` int NOT NULL,
  `content` text NOT NULL,
  `author_id` int NOT NULL,
  `parent_post_id` int DEFAULT NULL,
  `is_solution` tinyint DEFAULT '0',
  `edited_at` timestamp NULL DEFAULT NULL,
  `edited_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_subscriptions`
--

CREATE TABLE `forum_subscriptions` (
  `id` int NOT NULL,
  `thread_id` int NOT NULL,
  `user_id` int NOT NULL,
  `notify_replies` tinyint DEFAULT '1',
  `notify_mentions` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_threads`
--

CREATE TABLE `forum_threads` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int NOT NULL,
  `is_pinned` tinyint DEFAULT '0',
  `is_locked` tinyint DEFAULT '0',
  `is_sticky` tinyint DEFAULT '0',
  `view_count` int DEFAULT '0',
  `reply_count` int DEFAULT '0',
  `last_reply_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_reply_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_user_stats`
--

CREATE TABLE `forum_user_stats` (
  `anggota_id` int NOT NULL,
  `thread_count` int DEFAULT '0',
  `post_count` int DEFAULT '0',
  `reputation` int DEFAULT '0',
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `general_ledger`
--

CREATE TABLE `general_ledger` (
  `id` int NOT NULL,
  `account_id` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT '0.00',
  `kredit` decimal(15,2) DEFAULT '0.00',
  `saldo` decimal(15,2) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `investors`
--

CREATE TABLE `investors` (
  `id` int NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `jenis` enum('ANGGOTA','PIHAK_KETIGA','PENGURUS','PENGAWAS') DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `alamat` text,
  `telepon` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dokumen_path` varchar(255) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `investor_dividends`
--

CREATE TABLE `investor_dividends` (
  `id` int NOT NULL,
  `distribution_id` int DEFAULT NULL,
  `investor_id` int DEFAULT NULL,
  `investment_id` int DEFAULT NULL,
  `persentase_dividen` decimal(5,2) DEFAULT NULL,
  `jumlah_dividen` decimal(15,2) DEFAULT NULL,
  `status_pembayaran` enum('PENDING','PAID') DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int NOT NULL,
  `tanggal` date DEFAULT NULL,
  `nomor_jurnal` varchar(50) DEFAULT NULL,
  `deskripsi` text,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `journal_entry_details`
--

CREATE TABLE `journal_entry_details` (
  `id` int NOT NULL,
  `journal_entry_id` int DEFAULT NULL,
  `account_id` int DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT '0.00',
  `kredit` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `member_shu`
--

CREATE TABLE `member_shu` (
  `id` int NOT NULL,
  `distribution_id` int DEFAULT NULL,
  `member_id` int DEFAULT NULL,
  `shu_dari_transaksi` decimal(15,2) DEFAULT NULL,
  `shu_dari_partisipasi` decimal(15,2) DEFAULT NULL,
  `total_shu` decimal(15,2) DEFAULT NULL,
  `status_pembayaran` enum('PENDING','PAID') DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mfa_codes`
--

CREATE TABLE `mfa_codes` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `code` varchar(10) NOT NULL,
  `mfa_type` enum('email','sms','authenticator') NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mfa_sessions`
--

CREATE TABLE `mfa_sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `mfa_type` enum('email','sms','authenticator') NOT NULL,
  `secret_key` varchar(255) DEFAULT NULL,
  `backup_codes` json DEFAULT NULL,
  `is_enabled` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `anggota_id` int DEFAULT NULL,
  `tipe` enum('tunggakan','pinjaman_approval','shu','sistem','info') DEFAULT 'info',
  `judul` varchar(255) NOT NULL,
  `pesan` text NOT NULL,
  `data_json` text,
  `is_read` tinyint DEFAULT '0',
  `is_dismissed` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `nomor_order` varchar(50) DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_type` enum('ANGGOTA','UMUM','PENGURUS','PENGAWAS') DEFAULT NULL,
  `tanggal_order` date DEFAULT NULL,
  `total_harga` decimal(15,2) DEFAULT NULL,
  `total_biaya_operasional` decimal(15,2) DEFAULT NULL,
  `total_bayar` decimal(15,2) DEFAULT NULL,
  `metode_pengambilan` enum('DI_TOKO','LOKASI_PIHAK_KETIGA','KIRIM_ALAMAT') DEFAULT NULL,
  `lokasi_pengambilan_id` int DEFAULT NULL,
  `alamat_pengiriman` text,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `status_pembayaran` enum('PENDING','PAID','FAILED') DEFAULT NULL,
  `status_order` enum('PENDING','DIPROSES','SIAP_DIAMBIL','DIKIRIM','SELESAI','DIBATALKAN') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_details`
--

CREATE TABLE `order_details` (
  `id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `produk_id` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `harga_satuan` decimal(15,2) DEFAULT NULL,
  `diskon` decimal(15,2) DEFAULT '0.00',
  `subtotal` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `partners`
--

CREATE TABLE `partners` (
  `id` int NOT NULL,
  `nama_perusahaan` varchar(255) DEFAULT NULL,
  `jenis_mitra` varchar(100) DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `alamat` text,
  `telepon` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pinjaman`
--

CREATE TABLE `pinjaman` (
  `id` int NOT NULL,
  `anggota_id` int DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `tenor` int DEFAULT NULL,
  `bunga` decimal(5,2) DEFAULT NULL,
  `status` enum('DIAJUKAN','DISETUJUI','DITOLAK','LUNAS') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int NOT NULL,
  `nama_kategori` varchar(100) DEFAULT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `kode_produk` varchar(50) DEFAULT NULL,
  `nama_produk` varchar(255) DEFAULT NULL,
  `kategori_id` int DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `stok` int DEFAULT '0',
  `foto` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `profit_distributions`
--

CREATE TABLE `profit_distributions` (
  `id` int NOT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `tanggal_distribusi` date DEFAULT NULL,
  `total_keuntungan` decimal(15,2) DEFAULT NULL,
  `shu_anggota` decimal(15,2) DEFAULT NULL,
  `dividen_investor` decimal(15,2) DEFAULT NULL,
  `cadangan_koperasi` decimal(15,2) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','COMPLETED') DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int NOT NULL,
  `nomor_po` varchar(50) DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `tanggal_po` date DEFAULT NULL,
  `tanggal_pengiriman` date DEFAULT NULL,
  `total_nilai` decimal(15,2) DEFAULT NULL,
  `syarat_pembayaran` text,
  `status` enum('PENDING','APPROVED','COMPLETED','CANCELLED') DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_order_details`
--

CREATE TABLE `purchase_order_details` (
  `id` int NOT NULL,
  `po_id` int DEFAULT NULL,
  `produk_id` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `harga_satuan` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rat`
--

CREATE TABLE `rat` (
  `id` int NOT NULL,
  `tahun` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('DRAFT','DISAHKAN') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rat_agenda`
--

CREATE TABLE `rat_agenda` (
  `id` int NOT NULL,
  `tahun` year NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `status` enum('draft','active','completed','cancelled') DEFAULT 'draft',
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rat_participants`
--

CREATE TABLE `rat_participants` (
  `id` int NOT NULL,
  `rat_agenda_id` int NOT NULL,
  `anggota_id` int NOT NULL,
  `status` enum('registered','attended','absent') DEFAULT 'registered',
  `registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `attended_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'anggota'),
(2, 'pengurus'),
(3, 'pengawas'),
(4, 'admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `security_audit_logs`
--

CREATE TABLE `security_audit_logs` (
  `id` int NOT NULL,
  `vulnerability_type` enum('sql_injection','xss','csrf','session_hijacking','weak_password','file_upload','rate_limit','other') NOT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `endpoint` varchar(255) DEFAULT NULL,
  `user_agent` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `description` text,
  `payload` text,
  `detected_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `security_settings`
--

CREATE TABLE `security_settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `security_settings`
--

INSERT INTO `security_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'password_min_length', '8', 'Minimum password length', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(2, 'password_require_uppercase', '1', 'Require uppercase letters', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(3, 'password_require_lowercase', '1', 'Require lowercase letters', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(4, 'password_require_numbers', '1', 'Require numbers', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(5, 'password_require_symbols', '1', 'Require special symbols', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(6, 'login_max_attempts', '5', 'Maximum login attempts before lockout', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(7, 'login_lockout_duration', '15', 'Lockout duration in minutes', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(8, 'session_timeout', '60', 'Session timeout in minutes', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(9, 'csrf_token_expiry', '3600', 'CSRF token expiry in seconds', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(10, 'rate_limit_requests', '100', 'Rate limit requests per minute', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(11, 'rate_limit_window', '60', 'Rate limit window in seconds', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(12, 'mfa_required_for_admin', '1', 'Require MFA for admin/pengurus roles', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(13, 'audit_log_enabled', '1', 'Enable security audit logging', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(14, 'ip_whitelist_enabled', '0', 'Enable IP whitelisting', '2026-01-31 19:42:31', '2026-01-31 19:42:31'),
(15, 'maintenance_mode', '0', 'Enable maintenance mode', '2026-01-31 19:42:31', '2026-01-31 19:42:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `shu`
--

CREATE TABLE `shu` (
  `id` int NOT NULL,
  `anggota_id` int DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `simpanan`
--

CREATE TABLE `simpanan` (
  `id` int NOT NULL,
  `anggota_id` int DEFAULT NULL,
  `jenis` enum('POKOK','WAJIB','SUKARELA') DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int NOT NULL,
  `nama_perusahaan` varchar(255) DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `alamat` text,
  `telepon` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `rating` decimal(3,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier_invoices`
--

CREATE TABLE `supplier_invoices` (
  `id` int NOT NULL,
  `po_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `nomor_invoice` varchar(50) DEFAULT NULL,
  `tanggal_invoice` date DEFAULT NULL,
  `total_nilai` decimal(15,2) DEFAULT NULL,
  `status_pembayaran` enum('PENDING','PAID','OVERDUE') DEFAULT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `is_active`, `created_at`) VALUES
(1, 'anggota_demo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, '2026-01-31 19:42:56'),
(2, 'pengurus_demo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, '2026-01-31 19:42:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_notification_preferences`
--

CREATE TABLE `user_notification_preferences` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `email_tunggakan` tinyint DEFAULT '1',
  `email_pinjaman` tinyint DEFAULT '1',
  `email_shu` tinyint DEFAULT '1',
  `ui_tunggakan` tinyint DEFAULT '1',
  `ui_pinjaman` tinyint DEFAULT '1',
  `ui_shu` tinyint DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `votes`
--

CREATE TABLE `votes` (
  `id` int NOT NULL,
  `voting_topic_id` int NOT NULL,
  `anggota_id` int NOT NULL,
  `pilihan` json NOT NULL,
  `voted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `is_verified` tinyint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `voting_results_cache`
--

CREATE TABLE `voting_results_cache` (
  `voting_topic_id` int NOT NULL,
  `total_votes` int DEFAULT '0',
  `results` json DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `voting_sessions`
--

CREATE TABLE `voting_sessions` (
  `id` int NOT NULL,
  `voting_topic_id` int NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `voting_topics`
--

CREATE TABLE `voting_topics` (
  `id` int NOT NULL,
  `rat_agenda_id` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text,
  `tipe` enum('single_choice','multiple_choice','yes_no','ranking') DEFAULT 'single_choice',
  `options` json DEFAULT NULL,
  `required_quorum` int DEFAULT '0',
  `is_active` tinyint DEFAULT '1',
  `voting_start` datetime DEFAULT NULL,
  `voting_end` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indeks untuk tabel `agent_commissions`
--
ALTER TABLE `agent_commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `agent_sale_id` (`agent_sale_id`);

--
-- Indeks untuk tabel `agent_sales`
--
ALTER TABLE `agent_sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_transaksi` (`nomor_transaksi`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `agent_sales_details`
--
ALTER TABLE `agent_sales_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agent_sale_id` (`agent_sale_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nrp` (`nrp`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `angsuran`
--
ALTER TABLE `angsuran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pinjaman_id` (`pinjaman_id`);

--
-- Indeks untuk tabel `asset_depreciations`
--
ALTER TABLE `asset_depreciations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indeks untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `capital_changes`
--
ALTER TABLE `capital_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `investment_id` (`investment_id`);

--
-- Indeks untuk tabel `capital_investments`
--
ALTER TABLE `capital_investments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `investor_id` (`investor_id`);

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_akun` (`kode_akun`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indeks untuk tabel `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indeks untuk tabel `dokumen_hash`
--
ALTER TABLE `dokumen_hash`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indeks untuk tabel `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_recipient` (`recipient`);

--
-- Indeks untuk tabel `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_aset` (`kode_aset`);

--
-- Indeks untuk tabel `forum_categories`
--
ALTER TABLE `forum_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sort_order` (`sort_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indeks untuk tabel `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `edited_by` (`edited_by`),
  ADD KEY `idx_thread` (`thread_id`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_parent` (`parent_post_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `forum_subscriptions`
--
ALTER TABLE `forum_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subscription` (`thread_id`,`user_id`),
  ADD KEY `idx_thread` (`thread_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indeks untuk tabel `forum_threads`
--
ALTER TABLE `forum_threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_reply_by` (`last_reply_by`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_is_pinned` (`is_pinned`),
  ADD KEY `idx_is_sticky` (`is_sticky`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_last_reply` (`last_reply_at`);

--
-- Indeks untuk tabel `forum_user_stats`
--
ALTER TABLE `forum_user_stats`
  ADD PRIMARY KEY (`anggota_id`);

--
-- Indeks untuk tabel `general_ledger`
--
ALTER TABLE `general_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeks untuk tabel `investors`
--
ALTER TABLE `investors`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `investor_dividends`
--
ALTER TABLE `investor_dividends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `distribution_id` (`distribution_id`),
  ADD KEY `investor_id` (`investor_id`),
  ADD KEY `investment_id` (`investment_id`);

--
-- Indeks untuk tabel `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_jurnal` (`nomor_jurnal`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entry_id` (`journal_entry_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeks untuk tabel `member_shu`
--
ALTER TABLE `member_shu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `distribution_id` (`distribution_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indeks untuk tabel `mfa_codes`
--
ALTER TABLE `mfa_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indeks untuk tabel `mfa_sessions`
--
ALTER TABLE `mfa_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_tipe` (`tipe`),
  ADD KEY `anggota_id` (`anggota_id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_order` (`nomor_order`);

--
-- Indeks untuk tabel `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anggota_id` (`anggota_id`);

--
-- Indeks untuk tabel `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_produk` (`kode_produk`);

--
-- Indeks untuk tabel `profit_distributions`
--
ALTER TABLE `profit_distributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indeks untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_po` (`nomor_po`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indeks untuk tabel `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `rat`
--
ALTER TABLE `rat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rat_agenda`
--
ALTER TABLE `rat_agenda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_tahun` (`tahun`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tanggal_mulai` (`tanggal_mulai`);

--
-- Indeks untuk tabel `rat_participants`
--
ALTER TABLE `rat_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participant` (`rat_agenda_id`,`anggota_id`),
  ADD KEY `idx_rat_agenda` (`rat_agenda_id`),
  ADD KEY `idx_anggota` (`anggota_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `security_audit_logs`
--
ALTER TABLE `security_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`vulnerability_type`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_detected_at` (`detected_at`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `security_settings`
--
ALTER TABLE `security_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Indeks untuk tabel `shu`
--
ALTER TABLE `shu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anggota_id` (`anggota_id`);

--
-- Indeks untuk tabel `simpanan`
--
ALTER TABLE `simpanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anggota_id` (`anggota_id`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- Indeks untuk tabel `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`voting_topic_id`,`anggota_id`),
  ADD KEY `idx_voting_topic` (`voting_topic_id`),
  ADD KEY `idx_anggota` (`anggota_id`),
  ADD KEY `idx_voted_at` (`voted_at`);

--
-- Indeks untuk tabel `voting_results_cache`
--
ALTER TABLE `voting_results_cache`
  ADD PRIMARY KEY (`voting_topic_id`);

--
-- Indeks untuk tabel `voting_sessions`
--
ALTER TABLE `voting_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `voting_topic_id` (`voting_topic_id`),
  ADD KEY `idx_session_token` (`session_token`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indeks untuk tabel `voting_topics`
--
ALTER TABLE `voting_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rat_agenda` (`rat_agenda_id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_voting_start` (`voting_start`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `agent_commissions`
--
ALTER TABLE `agent_commissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `agent_sales`
--
ALTER TABLE `agent_sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `agent_sales_details`
--
ALTER TABLE `agent_sales_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `angsuran`
--
ALTER TABLE `angsuran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `asset_depreciations`
--
ALTER TABLE `asset_depreciations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `capital_changes`
--
ALTER TABLE `capital_changes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `capital_investments`
--
ALTER TABLE `capital_investments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dokumen_hash`
--
ALTER TABLE `dokumen_hash`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `forum_categories`
--
ALTER TABLE `forum_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `forum_subscriptions`
--
ALTER TABLE `forum_subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `forum_threads`
--
ALTER TABLE `forum_threads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `general_ledger`
--
ALTER TABLE `general_ledger`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `investors`
--
ALTER TABLE `investors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `investor_dividends`
--
ALTER TABLE `investor_dividends`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `member_shu`
--
ALTER TABLE `member_shu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mfa_codes`
--
ALTER TABLE `mfa_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mfa_sessions`
--
ALTER TABLE `mfa_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `profit_distributions`
--
ALTER TABLE `profit_distributions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rat`
--
ALTER TABLE `rat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rat_agenda`
--
ALTER TABLE `rat_agenda`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `rat_participants`
--
ALTER TABLE `rat_participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `security_audit_logs`
--
ALTER TABLE `security_audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `security_settings`
--
ALTER TABLE `security_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `shu`
--
ALTER TABLE `shu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `simpanan`
--
ALTER TABLE `simpanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `voting_sessions`
--
ALTER TABLE `voting_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `voting_topics`
--
ALTER TABLE `voting_topics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `agents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `agents_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`);

--
-- Ketidakleluasaan untuk tabel `agent_commissions`
--
ALTER TABLE `agent_commissions`
  ADD CONSTRAINT `agent_commissions_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `agent_commissions_ibfk_2` FOREIGN KEY (`agent_sale_id`) REFERENCES `agent_sales` (`id`);

--
-- Ketidakleluasaan untuk tabel `agent_sales`
--
ALTER TABLE `agent_sales`
  ADD CONSTRAINT `agent_sales_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `agent_sales_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `agent_sales_details`
--
ALTER TABLE `agent_sales_details`
  ADD CONSTRAINT `agent_sales_details_ibfk_1` FOREIGN KEY (`agent_sale_id`) REFERENCES `agent_sales` (`id`),
  ADD CONSTRAINT `agent_sales_details_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Ketidakleluasaan untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `angsuran`
--
ALTER TABLE `angsuran`
  ADD CONSTRAINT `angsuran_ibfk_1` FOREIGN KEY (`pinjaman_id`) REFERENCES `pinjaman` (`id`);

--
-- Ketidakleluasaan untuk tabel `asset_depreciations`
--
ALTER TABLE `asset_depreciations`
  ADD CONSTRAINT `asset_depreciations_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `fixed_assets` (`id`);

--
-- Ketidakleluasaan untuk tabel `capital_changes`
--
ALTER TABLE `capital_changes`
  ADD CONSTRAINT `capital_changes_ibfk_1` FOREIGN KEY (`investment_id`) REFERENCES `capital_investments` (`id`);

--
-- Ketidakleluasaan untuk tabel `capital_investments`
--
ALTER TABLE `capital_investments`
  ADD CONSTRAINT `capital_investments_ibfk_1` FOREIGN KEY (`investor_id`) REFERENCES `investors` (`id`);

--
-- Ketidakleluasaan untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Ketidakleluasaan untuk tabel `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD CONSTRAINT `chart_of_accounts_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`);

--
-- Ketidakleluasaan untuk tabel `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`);

--
-- Ketidakleluasaan untuk tabel `dokumen_hash`
--
ALTER TABLE `dokumen_hash`
  ADD CONSTRAINT `dokumen_hash_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `forum_threads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_ibfk_3` FOREIGN KEY (`parent_post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_ibfk_4` FOREIGN KEY (`edited_by`) REFERENCES `anggota` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `forum_subscriptions`
--
ALTER TABLE `forum_subscriptions`
  ADD CONSTRAINT `forum_subscriptions_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `forum_threads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_subscriptions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `forum_threads`
--
ALTER TABLE `forum_threads`
  ADD CONSTRAINT `forum_threads_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `forum_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_threads_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_threads_ibfk_3` FOREIGN KEY (`last_reply_by`) REFERENCES `anggota` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `forum_user_stats`
--
ALTER TABLE `forum_user_stats`
  ADD CONSTRAINT `forum_user_stats_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `general_ledger`
--
ALTER TABLE `general_ledger`
  ADD CONSTRAINT `general_ledger_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`);

--
-- Ketidakleluasaan untuk tabel `investor_dividends`
--
ALTER TABLE `investor_dividends`
  ADD CONSTRAINT `investor_dividends_ibfk_1` FOREIGN KEY (`distribution_id`) REFERENCES `profit_distributions` (`id`),
  ADD CONSTRAINT `investor_dividends_ibfk_2` FOREIGN KEY (`investor_id`) REFERENCES `investors` (`id`),
  ADD CONSTRAINT `investor_dividends_ibfk_3` FOREIGN KEY (`investment_id`) REFERENCES `capital_investments` (`id`);

--
-- Ketidakleluasaan untuk tabel `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  ADD CONSTRAINT `journal_entry_details_ibfk_1` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`),
  ADD CONSTRAINT `journal_entry_details_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`);

--
-- Ketidakleluasaan untuk tabel `member_shu`
--
ALTER TABLE `member_shu`
  ADD CONSTRAINT `member_shu_ibfk_1` FOREIGN KEY (`distribution_id`) REFERENCES `profit_distributions` (`id`),
  ADD CONSTRAINT `member_shu_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `anggota` (`id`);

--
-- Ketidakleluasaan untuk tabel `mfa_codes`
--
ALTER TABLE `mfa_codes`
  ADD CONSTRAINT `mfa_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mfa_sessions`
--
ALTER TABLE `mfa_sessions`
  ADD CONSTRAINT `mfa_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifikasi_ibfk_2` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Ketidakleluasaan untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD CONSTRAINT `pinjaman_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`);

--
-- Ketidakleluasaan untuk tabel `profit_distributions`
--
ALTER TABLE `profit_distributions`
  ADD CONSTRAINT `profit_distributions_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD CONSTRAINT `purchase_order_details_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `purchase_order_details_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Ketidakleluasaan untuk tabel `rat_agenda`
--
ALTER TABLE `rat_agenda`
  ADD CONSTRAINT `rat_agenda_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rat_participants`
--
ALTER TABLE `rat_participants`
  ADD CONSTRAINT `rat_participants_ibfk_1` FOREIGN KEY (`rat_agenda_id`) REFERENCES `rat_agenda` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rat_participants_ibfk_2` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `security_audit_logs`
--
ALTER TABLE `security_audit_logs`
  ADD CONSTRAINT `security_audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `shu`
--
ALTER TABLE `shu`
  ADD CONSTRAINT `shu_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`);

--
-- Ketidakleluasaan untuk tabel `simpanan`
--
ALTER TABLE `simpanan`
  ADD CONSTRAINT `simpanan_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`);

--
-- Ketidakleluasaan untuk tabel `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD CONSTRAINT `supplier_invoices_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `supplier_invoices_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Ketidakleluasaan untuk tabel `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  ADD CONSTRAINT `user_notification_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`voting_topic_id`) REFERENCES `voting_topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `voting_results_cache`
--
ALTER TABLE `voting_results_cache`
  ADD CONSTRAINT `voting_results_cache_ibfk_1` FOREIGN KEY (`voting_topic_id`) REFERENCES `voting_topics` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `voting_sessions`
--
ALTER TABLE `voting_sessions`
  ADD CONSTRAINT `voting_sessions_ibfk_1` FOREIGN KEY (`voting_topic_id`) REFERENCES `voting_topics` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `voting_topics`
--
ALTER TABLE `voting_topics`
  ADD CONSTRAINT `voting_topics_ibfk_1` FOREIGN KEY (`rat_agenda_id`) REFERENCES `rat_agenda` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
