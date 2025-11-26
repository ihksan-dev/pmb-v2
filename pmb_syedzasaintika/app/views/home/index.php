<?php $this->view('templates/header'); ?>

<div class="jumbotron bg-primary text-white p-5 rounded">
    <div class="container">
        <h1 class="display-4">Selamat Datang di PMB <?php echo APP_NAME; ?></h1>
        <p class="lead">Sistem Pendaftaran Mahasiswa Baru Universitas Syedza Saintika</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a class="btn btn-light btn-lg" href="<?php echo BASE_URL; ?>/auth/register" role="button">Daftar Sekarang</a>
            <a class="btn btn-outline-light btn-lg" href="<?php echo BASE_URL; ?>/auth/login" role="button">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Proses Pendaftaran Mudah</h5>
                <p class="card-text">Ikuti langkah-langkah pendaftaran yang mudah dan cepat hanya dalam beberapa menit.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Verifikasi Cepat</h5>
                <p class="card-text">Verifikasi pendaftaran dilakukan secara cepat dan efisien oleh admin kami.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Cetak Formulir</h5>
                <p class="card-text">Cetak formulir pendaftaran setelah diverifikasi untuk proses daftar ulang.</p>
            </div>
        </div>
    </div>
</div>

<?php $this->view('templates/footer'); ?>