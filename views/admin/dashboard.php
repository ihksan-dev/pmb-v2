<?php
$title = "Dashboard Admin - SIPMB v1.5";
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Dashboard Administrator</h2>
        <hr>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h5 class="card-title">Total Pendaftar</h5>
                <h3><?php echo $totalStudents; ?></h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-3x mb-3"></i>
                <h5 class="card-title">Menunggu Verifikasi</h5>
                <h3><?php echo $pendingStudents; ?></h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h5 class="card-title">Terverifikasi</h5>
                <h3><?php echo $verifiedStudents; ?></h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-3x mb-3"></i>
                <h5 class="card-title">Ditolak</h5>
                <h3><?php echo $rejectedStudents; ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Grafik Minat Program Studi (<?php echo htmlspecialchars($currentYear['year_name']); ?>)</h4>
            </div>
            <div class="card-body">
                <canvas id="prodiChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Distribusi Jenis Kelamin</h4>
            </div>
            <div class="card-body">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Daftar Pendaftar Terbaru</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Prodi</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStudents as $index => $student): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['nama_prodi']); ?></td>
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
                                        <a href="/admin/students/<?php echo $student['id']; ?>/detail" class="btn btn-sm btn-info">Detail</a>
                                        <a href="/admin/students/<?php echo $student['id']; ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="/admin/students/pending" class="btn btn-primary">Lihat Semua Pendaftar Belum Diverifikasi</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prodi Chart
    const prodiCtx = document.getElementById('prodiChart').getContext('2d');
    const prodiChart = new Chart(prodiCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($prodiLabels); ?>,
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: <?php echo json_encode($prodiData); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gender Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderChart = new Chart(genderCtx, {
        type: 'pie',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{
                data: [<?php echo $maleCount; ?>, <?php echo $femaleCount; ?>],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>