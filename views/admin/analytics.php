<?php
$title = "Analitik Data - SIPMB v1.5";
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2>Analitik Data Pendaftar (<?php echo htmlspecialchars($currentYear['year_name']); ?>)</h2>
        <hr>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="year" class="form-control" id="yearFilter">
                    <option value="">Pilih Tahun Ajaran</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?php echo $year['id']; ?>" <?php echo (($_GET['year'] ?? $currentYear['id']) == $year['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year['year_name']); ?> <?php echo $year['is_active'] ? '(Aktif)' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Distribusi Jenis Kelamin</h4>
            </div>
            <div class="card-body">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Minat Program Studi</h4>
            </div>
            <div class="card-body">
                <canvas id="prodiChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Asal Daerah Pendaftar</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="provinceChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="regencyChart"></canvas>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-muted">Klik pada provinsi di grafik untuk melihat distribusi kabupaten/kota</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Ringkasan Statistik</h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h3><?php echo $totalStudents; ?></h3>
                                <p class="card-text">Total Pendaftar</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h3><?php echo $verifiedStudents; ?></h3>
                                <p class="card-text">Terverifikasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <h3><?php echo $pendingStudents; ?></h3>
                                <p class="card-text">Menunggu</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h3><?php echo $rejectedStudents; ?></h3>
                                <p class="card-text">Ditolak</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Province Chart
    const provinceCtx = document.getElementById('provinceChart').getContext('2d');
    const provinceChart = new Chart(provinceCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($provinceLabels); ?>,
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: <?php echo json_encode($provinceData); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const provinceId = <?php echo json_encode(array_keys($provinceData)); ?>[index];
                    loadRegencyData(provinceId);
                }
            }
        }
    });
    
    // Regency Chart
    const regencyCtx = document.getElementById('regencyChart').getContext('2d');
    const regencyChart = new Chart(regencyCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($regencyLabels); ?>,
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: <?php echo json_encode($regencyData); ?>,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
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
    
    // Function to load regency data for a specific province
    function loadRegencyData(provinceId) {
        fetch(`/api/analytics/regencies?province_id=${provinceId}`)
            .then(response => response.json())
            .then(data => {
                regencyChart.data.labels = data.labels;
                regencyChart.data.datasets[0].data = data.data;
                regencyChart.update();
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Year filter change
    document.getElementById('yearFilter').addEventListener('change', function() {
        const selectedYear = this.value;
        if (selectedYear) {
            window.location.href = `?year=${selectedYear}`;
        } else {
            window.location.href = '?';
        }
    });
});
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>