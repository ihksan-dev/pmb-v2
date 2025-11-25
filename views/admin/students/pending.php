<?php
$title = "Pendaftar Belum Diverifikasi - SIPMB v1.5";
include_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Pendaftar Belum Diverifikasi (<?php echo htmlspecialchars($currentYear['year_name']); ?>)</h2>
        <hr>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between">
            <a href="/admin/students/active" class="btn btn-secondary">Lihat Semua Pendaftar Aktif</a>
            <a href="/admin/students/all" class="btn btn-outline-secondary">Lihat Semua Pendaftar</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <div class="text-center">
                        <p>Belum ada pendaftar yang menunggu verifikasi.</p>
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
                                        <td><?php echo date('d-m-Y H:i:s', strtotime($student['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/students/<?php echo $student['id']; ?>/detail" class="btn btn-sm btn-info">Detail</a>
                                                <a href="/admin/students/<?php echo $student['id']; ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal" data-id="<?php echo $student['id']; ?>">Verifikasi</button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="<?php echo $student['id']; ?>">Tolak</button>
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

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyModalLabel">Verifikasi Pendaftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memverifikasi pendaftar ini?</p>
                <form id="verifyForm" method="POST" action="">
                    <input type="hidden" name="action" value="verify">
                    <input type="hidden" name="student_id" id="verifyStudentId" value="">
                    <div class="mb-3">
                        <label for="verify_note" class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" id="verify_note" name="note" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Verifikasi</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Pendaftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menolak pendaftar ini?</p>
                <form id="rejectForm" method="POST" action="">
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="student_id" id="rejectStudentId" value="">
                    <div class="mb-3">
                        <label for="reject_note" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" id="reject_note" name="note" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle verify modal
    const verifyModal = document.getElementById('verifyModal');
    verifyModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const studentId = button.getAttribute('data-id');
        document.getElementById('verifyStudentId').value = studentId;
        document.getElementById('verifyForm').action = '/admin/students/' + studentId + '/verify';
    });
    
    // Handle reject modal
    const rejectModal = document.getElementById('rejectModal');
    rejectModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const studentId = button.getAttribute('data-id');
        document.getElementById('rejectStudentId').value = studentId;
        document.getElementById('rejectForm').action = '/admin/students/' + studentId + '/reject';
    });
});
</script>

<?php
include_once __DIR__ . '/../../layouts/footer.php';
?>