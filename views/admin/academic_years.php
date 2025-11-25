<?php
$title = "Manajemen Tahun Ajaran - SIPMB v1.5";
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Manajemen Tahun Ajaran</h2>
        <hr>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addYearModal">
            <i class="fas fa-plus"></i> Tambah Tahun Ajaran
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php if (empty($academicYears)): ?>
                    <div class="text-center">
                        <p>Belum ada tahun ajaran yang terdaftar.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Tahun Ajaran</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($academicYears as $index => $year): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($year['year_name']); ?></td>
                                        <td>
                                            <?php if ($year['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d-m-Y H:i:s', strtotime($year['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if (!$year['is_active']): ?>
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal" data-id="<?php echo $year['id']; ?>" data-name="<?php echo htmlspecialchars($year['year_name']); ?>">Aktifkan</button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editYearModal" data-id="<?php echo $year['id']; ?>" data-name="<?php echo htmlspecialchars($year['year_name']); ?>">Edit</button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $year['id']; ?>" data-name="<?php echo htmlspecialchars($year['year_name']); ?>">Hapus</button>
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

<!-- Add Year Modal -->
<div class="modal fade" id="addYearModal" tabindex="-1" aria-labelledby="addYearModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addYearModalLabel">Tambah Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addYearForm" method="POST" action="/admin/academic-years">
                    <input type="hidden" name="action" value="create">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div class="mb-3">
                        <label for="year_name" class="form-label">Nama Tahun Ajaran</label>
                        <input type="text" class="form-control" id="year_name" name="year_name" placeholder="Contoh: 2024/2025" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Year Modal -->
<div class="modal fade" id="editYearModal" tabindex="-1" aria-labelledby="editYearModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editYearModalLabel">Edit Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editYearForm" method="POST" action="">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="year_id" id="editYearId" value="">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div class="mb-3">
                        <label for="edit_year_name" class="form-label">Nama Tahun Ajaran</label>
                        <input type="text" class="form-control" id="edit_year_name" name="year_name" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Activate Modal -->
<div class="modal fade" id="activateModal" tabindex="-1" aria-labelledby="activateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activateModalLabel">Aktifkan Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengaktifkan tahun ajaran <strong id="activateYearName"></strong>? Tindakan ini akan menonaktifkan tahun ajaran lainnya.</p>
                <form id="activateForm" method="POST" action="">
                    <input type="hidden" name="action" value="activate">
                    <input type="hidden" name="year_id" id="activateYearId" value="">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <button type="submit" class="btn btn-success">Aktifkan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hapus Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tahun ajaran <strong id="deleteYearName"></strong>? Data yang dihapus tidak dapat dikembalikan.</p>
                <form id="deleteForm" method="POST" action="">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="year_id" id="deleteYearId" value="">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <button type="submit" class="btn btn-danger">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit modal
    const editModal = document.getElementById('editYearModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const yearId = button.getAttribute('data-id');
        const yearName = button.getAttribute('data-name');
        document.getElementById('editYearId').value = yearId;
        document.getElementById('edit_year_name').value = yearName;
        document.getElementById('editYearForm').action = '/admin/academic-years/' + yearId;
    });
    
    // Handle activate modal
    const activateModal = document.getElementById('activateModal');
    activateModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const yearId = button.getAttribute('data-id');
        const yearName = button.getAttribute('data-name');
        document.getElementById('activateYearId').value = yearId;
        document.getElementById('activateYearName').textContent = yearName;
        document.getElementById('activateForm').action = '/admin/academic-years/' + yearId + '/activate';
    });
    
    // Handle delete modal
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const yearId = button.getAttribute('data-id');
        const yearName = button.getAttribute('data-name');
        document.getElementById('deleteYearId').value = yearId;
        document.getElementById('deleteYearName').textContent = yearName;
        document.getElementById('deleteForm').action = '/admin/academic-years/' + yearId;
    });
});
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>