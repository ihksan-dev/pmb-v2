<?php
$title = "Edit Data Pendaftaran - SIPMB v1.5";
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Edit Data Pendaftaran</h2>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="/applicant/edit" method="POST" id="editForm">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($studentData['full_name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($studentData['phone']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" <?php echo $studentData['gender'] === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?php echo $studentData['gender'] === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="birth_date" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo $studentData['birth_date']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="province_id" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <select class="form-control" id="province_id" name="province_id" required>
                                    <option value="">Pilih Provinsi</option>
                                    <?php foreach ($provinces as $province): ?>
                                        <option value="<?php echo $province['id']; ?>" <?php echo $studentData['province_id'] == $province['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($province['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="regency_id" class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                                <select class="form-control" id="regency_id" name="regency_id" required>
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    <?php foreach ($regencies as $regency): ?>
                                        <option value="<?php echo $regency['id']; ?>" <?php echo $studentData['regency_id'] == $regency['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($regency['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($studentData['address']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="prodi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select class="form-control" id="prodi_id" name="prodi_id" required>
                            <option value="">Pilih Program Studi</option>
                            <?php foreach ($prodis as $prodi): ?>
                                <option value="<?php echo $prodi['id']; ?>" <?php echo $studentData['prodi_id'] == $prodi['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($prodi['nama_prodi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select class="form-control" id="academic_year_id" name="academic_year_id" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            <?php foreach ($academic_years as $year): ?>
                                <option value="<?php echo $year['id']; ?>" <?php echo $studentData['academic_year_id'] == $year['id'] ? 'selected' : ''; ?> <?php echo $year['is_active'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($year['year_name']); ?> <?php echo $year['is_active'] ? '(Aktif)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg">Perbarui Data</button>
                    </div>
                </form>
                
                <div class="mt-3">
                    <a href="/applicant/status" class="btn btn-secondary">Kembali ke Status</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province_id');
    const regencySelect = document.getElementById('regency_id');
    
    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        
        if(provinceId) {
            fetch(`/api/regencies?province_id=${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                    data.forEach(regency => {
                        const option = document.createElement('option');
                        option.value = regency.id;
                        option.textContent = regency.name;
                        regencySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
        }
    });
});
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>