<?php
$title = "Pendaftar Aktif - SIPMB v1.5";
include_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Pendaftar Aktif (<?php echo htmlspecialchars($currentYear['year_name']); ?>)</h2>
        <hr>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between">
            <a href="/admin/students/pending" class="btn btn-secondary">Lihat Pendaftar Belum Diverifikasi</a>
            <a href="/admin/students/all" class="btn btn-outline-secondary">Lihat Semua Pendaftar</a>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <a href="/admin/students/active/export" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export ke Excel
        </a>
        <a href="/admin/students/active/print" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Daftar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <div class="text-center">
                        <p>Belum ada pendaftar aktif untuk tahun ajaran ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Prodi</th>
                                    <th>Asal Daerah</th>
                                    <th>Status</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($student['nama_prodi']); ?></td>
                                        <td><?php echo htmlspecialchars($student['regency_name'] . ', ' . $student['province_name']); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?php 
                                                    switch($student['status']) {
                                                        case 'pending': echo 'bg-warning text-dark'; break;
                                                        case 'verified': echo 'bg-success'; break;
                                                        case 'rejected': echo 'bg-danger'; break;
                                                        default: echo 'bg-secondary';
                                                    }
                                                ?>
                                            ">
                                                <?php echo $student['status'] === 'pending' ? 'Menunggu' : ($student['status'] === 'verified' ? 'Terverifikasi' : 'Ditolak'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d-m-Y H:i:s', strtotime($student['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/students/<?php echo $student['id']; ?>/detail" class="btn btn-sm btn-info">Detail</a>
                                                <a href="/admin/students/<?php echo $student['id']; ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../../layouts/footer.php';
?>