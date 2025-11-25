<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran - <?php echo htmlspecialchars($student['full_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .verified {
            background-color: #d4edda;
            color: #155724;
        }
        .pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>BUKTI PENDAFTARAN MAHASISWA BARU</h2>
            <p><strong><?php echo htmlspecialchars($student['year_name']); ?></strong></p>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <span class="status-badge <?php echo $student['status']; ?>">
                    STATUS: <?php echo strtoupper($student['status'] === 'pending' ? 'MENUNGGU VERIFIKASI' : ($student['status'] === 'verified' ? 'TERVERIFIKASI' : 'DITOLAK')); ?>
                </span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h5>Informasi Pribadi</h5>
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Nama Lengkap</strong></td>
                        <td width="60%">: <?php echo htmlspecialchars($student['full_name']); ?></td>
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
                        <td width="40%"><strong>Program Studi</strong></td>
                        <td width="60%">: <?php echo htmlspecialchars($student['nama_prodi']); ?></td>
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
                        <td><strong>Tanggal Daftar</strong></td>
                        <td>: <?php echo date('d-m-Y H:i:s', strtotime($student['created_at'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-12">
                <p>Catatan:</p>
                <ul>
                    <li>Bukti pendaftaran ini sah apabila status pendaftaran telah terverifikasi</li>
                    <li>Simpan bukti pendaftaran ini sebagai arsip</li>
                    <li>Hubungi admin apabila terdapat kesalahan data</li>
                </ul>
            </div>
        </div>
        
        <div class="row mt-5 no-print">
            <div class="col-md-12 text-center">
                <button class="btn btn-primary" onclick="window.print()">Cetak Bukti</button>
                <a href="/applicant/dashboard" class="btn btn-secondary">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>