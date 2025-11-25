<?php
$title = "Semua Pendaftar - SIPMB v1.5";
include_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Semua Pendaftar</h2>
        <hr>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between">
            <a href="/admin/students/pending" class="btn btn-secondary">Lihat Pendaftar Belum Diverifikasi</a>
            <a href="/admin/students/active" class="btn btn-outline-secondary">Lihat Pendaftar Aktif</a>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Cari nama, email, atau telepon" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="pending" <?php echo (($_GET['status'] ?? '') === 'pending') ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                    <option value="verified" <?php echo (($_GET['status'] ?? '') === 'verified') ? 'selected' : ''; ?>>Terverifikasi</option>
                    <option value="rejected" <?php echo (($_GET['status'] ?? '') === 'rejected') ? 'selected' : ''; ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="year" class="form-control">
                    <option value="">Semua Tahun Ajaran</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?php echo $year['id']; ?>" <?php echo (($_GET['year'] ?? '') == $year['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year['year_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="/admin/students/all" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <div class="text-center">
                        <p>Tidak ada pendaftar yang ditemukan.</p>
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
                                    <th>Tahun Ajaran</th>
                                    <th>Status</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td><?php echo ($currentPage - 1) * $limit + $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($student['nama_prodi']); ?></td>
                                        <td><?php echo htmlspecialchars($student['year_name']); ?></td>
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
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $student['id']; ?>" data-name="<?php echo htmlspecialchars($student['full_name']); ?>">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&year=<?php echo urlencode($_GET['year'] ?? ''); ?>">Sebelumnya</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&year=<?php echo urlencode($_GET['year'] ?? ''); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&year=<?php echo urlencode($_GET['year'] ?? ''); ?>">Berikutnya</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hapus Pendaftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pendaftar <strong id="deleteStudentName"></strong>? Data yang dihapus tidak dapat dikembalikan.</p>
                <form id="deleteForm" method="POST" action="">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="student_id" id="deleteStudentId" value="">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete modal
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const studentId = button.getAttribute('data-id');
        const studentName = button.getAttribute('data-name');
        document.getElementById('deleteStudentId').value = studentId;
        document.getElementById('deleteStudentName').textContent = studentName;
        document.getElementById('deleteForm').action = '/admin/students/' + studentId + '/delete';
    });
});
</script>

<?php
include_once __DIR__ . '/../../layouts/footer.php';
?>