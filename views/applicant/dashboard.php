<?php
$title = "Dashboard Calon Mahasiswa - SIPMB v1.5";
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['student_name']); ?></h2>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body text-center">
                <i class="fas fa-user-graduate fa-3x mb-3"></i>
                <h5 class="card-title">Profil Saya</h5>
                <p class="card-text">Lihat dan edit informasi pribadi</p>
                <a href="/applicant/status" class="btn btn-light">Lihat Profil</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body text-center">
                <i class="fas fa-file-alt fa-3x mb-3"></i>
                <h5 class="card-title">Status Pendaftaran</h5>
                <p class="card-text">
                    <?php 
                    $status_class = '';
                    switch($student['status']) {
                        case 'pending': $status_class = 'bg-warning text-dark'; break;
                        case 'verified': $status_class = 'bg-success'; break;
                        case 'rejected': $status_class = 'bg-danger'; break;
                        default: $status_class = 'bg-secondary';
                    }
                    ?>
                    <span class="badge <?php echo $status_class; ?> fs-6">
                        <?php echo $student['status'] === 'pending' ? 'Menunggu Verifikasi' : ($student['status'] === 'verified' ? 'Terverifikasi' : 'Ditolak'); ?>
                    </span>
                </p>
                <a href="/applicant/status" class="btn btn-light">Lihat Status</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body text-center">
                <i class="fas fa-print fa-3x mb-3"></i>
                <h5 class="card-title">Cetak Bukti</h5>
                <p class="card-text">Cetak bukti pendaftaran</p>
                <a href="/applicant/print" class="btn btn-light">Cetak</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-secondary mb-3">
            <div class="card-body text-center">
                <i class="fas fa-cog fa-3x mb-3"></i>
                <h5 class="card-title">Pengaturan</h5>
                <p class="card-text">Ubah kata sandi dan akun</p>
                <a href="/applicant/account" class="btn btn-light">Pengaturan</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Ringkasan Pendaftaran</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                        <p><strong>Telepon:</strong> <?php echo htmlspecialchars($student['phone']); ?></p>
                        <p><strong>Jenis Kelamin:</strong> <?php echo $student['gender'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Program Studi:</strong> <?php echo htmlspecialchars($student['nama_prodi']); ?></p>
                        <p><strong>Tahun Ajaran:</strong> <?php echo htmlspecialchars($student['year_name']); ?></p>
                        <p><strong>Provinsi:</strong> <?php echo htmlspecialchars($student['province_name']); ?></p>
                        <p><strong>Kabupaten/Kota:</strong> <?php echo htmlspecialchars($student['regency_name']); ?></p>
                    </div>
                </div>
                
                <?php if ($student['status'] === 'pending'): ?>
                    <div class="mt-3">
                        <a href="/applicant/edit" class="btn btn-warning">Edit Data Pendaftaran</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>