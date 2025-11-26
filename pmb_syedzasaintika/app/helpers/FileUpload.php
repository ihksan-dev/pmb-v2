<?php
class FileUpload 
{
    private $upload_dir = '../public/uploads/';
    private $allowed_types = [
        'foto' => ['jpg', 'jpeg', 'png'],
        'dokumen' => ['pdf', 'jpg', 'jpeg', 'png']
    ];
    private $max_sizes = [
        'foto' => 2 * 1024 * 1024, // 2MB
        'dokumen' => 5 * 1024 * 1024 // 5MB
    ];

    public function uploadFile($file, $type)
    {
        if (!isset($file['name']) || $file['name'] === '') {
            return ['success' => false, 'message' => 'File tidak ditemukan'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Error upload file: ' . $file['error']];
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_types = $this->allowed_types[$type] ?? [];

        if (!in_array($file_extension, $allowed_types)) {
            return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
        }

        if ($file['size'] > $this->max_sizes[$type]) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar'];
        }

        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $file_extension;
        $upload_path = $this->upload_dir . $type . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return ['success' => true, 'filename' => $filename, 'message' => 'File berhasil diupload'];
        } else {
            return ['success' => false, 'message' => 'Gagal mengupload file'];
        }
    }

    public function deleteFile($filename, $type)
    {
        $file_path = $this->upload_dir . $type . '/' . $filename;
        
        if (file_exists($file_path)) {
            return unlink($file_path);
        }
        
        return false;
    }

    public function validateFile($file, $type)
    {
        if (!isset($file['name']) || $file['name'] === '') {
            return ['valid' => false, 'message' => 'File tidak ditemukan'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'message' => 'Error upload file: ' . $file['error']];
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_types = $this->allowed_types[$type] ?? [];

        if (!in_array($file_extension, $allowed_types)) {
            return ['valid' => false, 'message' => 'Tipe file tidak diizinkan'];
        }

        if ($file['size'] > $this->max_sizes[$type]) {
            return ['valid' => false, 'message' => 'Ukuran file terlalu besar'];
        }

        return ['valid' => true, 'message' => 'File valid'];
    }
}