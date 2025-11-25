<?php
$title = "Beranda - SIPMB v1.5";
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="jumbotron bg-primary text-white p-5 rounded">
    <div class="container">
        <h1 class="display-4">Selamat Datang di SIPMB v1.5</h1>
        <p class="lead">Sistem Informasi Pendaftaran Mahasiswa Baru yang aman, efisien, dan mudah digunakan.</p>
        <a class="btn btn-light btn-lg" href="/register" role="button">Daftar Sekarang</a>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Calon Mahasiswa</h5>
                <p class="card-text">Daftar dan lacak status pendaftaran Anda dengan mudah melalui portal kami.</p>
                <a href="/register" class="btn btn-primary">Daftar Sekarang</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users-cog fa-3x text-success mb-3"></i>
                <h5 class="card-title">Administrator</h5>
                <p class="card-text">Kelola pendaftaran dan analisis data calon mahasiswa secara real-time.</p>
                <a href="/admin/login" class="btn btn-success">Login Admin</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                <h5 class="card-title">Analitik</h5>
                <p class="card-text">Visualisasi data pendaftaran untuk pengambilan keputusan yang lebih baik.</p>
                <a href="/admin/login" class="btn btn-info">Lihat Analitik</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-12">
        <h3 class="mb-4">Tentang Sistem Ini</h3>
        <p>Sistem Informasi Pendaftaran Mahasiswa Baru (SIPMB) v1.5 adalah platform yang dirancang untuk menyederhanakan proses pendaftaran mahasiswa baru. Sistem ini menawarkan berbagai fitur seperti:</p>
        <ul>
            <li>Formulir pendaftaran yang mudah digunakan</li>
            <li>Dashboard untuk pelacakan status pendaftaran</li>
            <li>Fitur manajemen untuk administrator</li>
            <li>Visualisasi data untuk analisis</li>
            <li>Keamanan data yang terjamin</li>
        </ul>
    </div>
</div>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>