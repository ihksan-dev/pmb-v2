<?php
$title = "Status Pendaftaran - SIPMB v1.5";
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Status Pendaftaran</h2>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Detail Pendaftaran</h4>
                <?php if ($student['status'] === 'pending'): ?>
                    <a href="/applicant/edit" class="btn btn-warning">Edit Data</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informasi Pribadi</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nama Lengkap</strong></td>
                                <td>: <?php echo htmlspecialchars($student['full_name']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>: <?php echo htmlspecialchars($student['email']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: <?php echo htmlspecialchars($student['phone']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Kelamin</strong></td>
                                <td>: <?php echo $student['gender'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Lahir</strong></td>
                                <td>: <?php echo date('d-m-Y', strtotime($student['birth_date'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: <?php echo htmlspecialchars($student['address']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Informasi Pendaftaran</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Program Studi</strong></td>
                                <td>: <?php echo htmlspecialchars($student['nama_prodi']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tahun Ajaran</strong></td>
                                <td>: <?php echo htmlspecialchars($student['year_name']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Provinsi</strong></td>
                                <td>: <?php echo htmlspecialchars($student['province_name']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Kabupaten/Kota</strong></td>
                                <td>: <?php echo htmlspecialchars($student['regency_name']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php 
                                    $status_class = '';
                                    switch($student['status']) {
                                        case 'pending': $status_class = 'badge bg-warning text-dark'; break;
                                        case 'verified': $status_class = 'badge bg-success'; break;
                                        case 'rejected': $status_class = 'badge bg-danger'; break;
                                        default: $status_class = 'badge bg-secondary';
                                    }
                                    ?>
                                    <span class="<?php echo $status_class; ?>">
                                        <?php echo $student['status'] === 'pending' ? 'Menunggu Verifikasi' : ($student['status'] === 'verified' ? 'Terverifikasi' : 'Ditolak'); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Daftar</strong></td>
                                <td>: <?php echo date('d-m-Y H:i:s', strtotime($student['created_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>