<?php
// File: proses_tambah_event.php

// PENTING: Start session untuk mengakses data user yang login
session_start();

// Pastikan file koneksi terhubung
include_once '../koneksi.php';

// Cek apakah user sudah login dan memiliki ID
// NOTE: login.php menyimpan ID user di key 'user_id' — gunakan kunci yang konsisten
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Ganti ke halaman login yang sesuai
    exit();
}

// Ambil ID user yang sedang login
$id_user = $_SESSION['user_id'];

// Cek apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Ambil dan bersihkan data input dari formulir
    $nama_event        = trim($_POST['nama_event']);
    $tanggal_mulai     = $_POST['tanggal_mulai'];
    $tanggal_selesai   = empty($_POST['tanggal_selesai']) ? NULL : $_POST['tanggal_selesai'];
    $lokasi            = trim($_POST['lokasi']);
    $deskripsi         = trim($_POST['deskripsi']);
    $total_anggaran    = empty($_POST['total_anggaran']) ? NULL : (float)$_POST['total_anggaran'];

    // 3. Validasi Data yang Wajib Diisi
    if (empty($nama_event) || empty($tanggal_mulai) || empty($lokasi) || empty($deskripsi)) {
        header("Location: tambah-event.php?status=error&msg=" . urlencode("Semua kolom bertanda bintang (*) wajib diisi."));
        exit();
    }

    // 4. Siapkan Prepared Statement (QUERRY INSERT) - DITAMBAH created_by
    $sql = "INSERT INTO events (nama_event, tanggal_mulai, tanggal_selesai, lokasi, deskripsi, total_anggaran, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?)"; // Total 7 placeholder (?)

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        error_log("[proses-tambahevent] prepare failed: " . mysqli_error($conn));
        $_SESSION['flash_error'] = "Terjadi kesalahan server saat menyiapkan permintaan. Silakan coba lagi atau laporkan ke admin.";
        header("Location: tambah-event.php?status=error");
        exit();
    }

    // 5. Bind Parameter - DITAMBAH created_by (i = integer)
    // Tipe data: s, s, s, s, s, d, i
    $bind_types = "sssssdi";

    mysqli_stmt_bind_param(
        $stmt,
        $bind_types,
        $nama_event,
        $tanggal_mulai,
        $tanggal_selesai,
        $lokasi,
        $deskripsi,
        $total_anggaran,
        $id_user        // ID User ditambahkan di sini
    );

    // 6. Eksekusi Statement
    // Baris 51 terletak di sekitar sini, di mana eksekusi dilakukan
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash_success'] = "Event berhasil ditambahkan.";
        header("Location: tambah-event.php?status=tambah_sukses");
        exit();
    } else {
        // Log error ke error_log untuk debugging lebih lanjut, jangan pamerkan SQL error ke user
        $err = mysqli_error($conn);
        $stmt_err = mysqli_stmt_error($stmt);
        error_log("[proses-tambahevent] execute failed: " . $err . " | stmt: " . $stmt_err);
        $_SESSION['flash_error'] = "Gagal menambahkan event karena kesalahan server. Silakan coba lagi atau hubungi admin.";
        header("Location: tambah-event.php?status=error");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    header("Location: tambah-event.php");
    exit();
}

mysqli_close($conn);
