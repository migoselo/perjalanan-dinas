<?php

namespace App\Services;

/**
 * Helper class untuk Google Drive operations
 * Mempermudah penggunaan GoogleDriveService di controller atau model
 */
class GoogleDriveHelper
{
    /**
     * Get setup instructions untuk Google Drive integration
     */
    public static function getSetupInstructions()
    {
        return [
            'step_1' => 'Kunjungi https://console.cloud.google.com/',
            'step_2' => 'Buat project baru atau gunakan yang sudah ada',
            'step_3' => 'Enable Google Drive API di Marketplace',
            'step_4' => 'Buat Service Account di APIs & Services → Credentials',
            'step_5' => 'Download JSON credentials key',
            'step_6' => 'Buat folder di Google Drive untuk root (contoh: SPT_Progress)',
            'step_7' => 'Share folder dengan service account email',
            'step_8' => 'Setup .env dengan GOOGLE_DRIVE_ROOT_FOLDER_ID dan credentials',
        ];
    }

    /**
     * Validate Google Drive credentials
     */
    public static function validateCredentials()
    {
        $rootFolderId = config('services.google.drive.root_folder_id');
        $credentialsPath = config('services.google.drive.credentials_path');
        $credentialsJson = config('services.google.drive.credentials_json');

        $errors = [];

        if (!$rootFolderId) {
            $errors[] = 'GOOGLE_DRIVE_ROOT_FOLDER_ID tidak diset di .env';
        }

        if (!$credentialsPath && !$credentialsJson) {
            $errors[] = 'GOOGLE_DRIVE_CREDENTIALS_PATH atau GOOGLE_DRIVE_CREDENTIALS_JSON harus diset';
        }

        if ($credentialsPath && !file_exists($credentialsPath)) {
            $errors[] = "File credentials tidak ditemukan: {$credentialsPath}";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get list of supported file types
     */
    public static function getSupportedFileTypes()
    {
        return [
            'laporan' => 'Laporan (PDF, DOC, DOCX, XLS, XLSX)',
            'penanggung_jawab' => 'Penanggung Jawab (PDF, DOC, DOCX, JPG, PNG)',
            'pembayaran' => 'Pembayaran (PDF, XLS, XLSX, JPG, PNG)',
        ];
    }

    /**
     * Get max file size in bytes (50 MB)
     */
    public static function getMaxFileSize()
    {
        return 50 * 1024 * 1024; // 50 MB
    }

    /**
     * Format file size untuk display
     */
    public static function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Generate folder name untuk SPT
     */
    public static function generateFolderName($nomorSPT)
    {
        // Replace special characters
        $folderName = "SPT_" . str_replace(['/', '\\', ' ', ':', '*', '?', '"', '<', '>', '|'], '_', $nomorSPT);
        // Remove consecutive underscores
        $folderName = preg_replace('/_+/', '_', $folderName);
        // Remove trailing underscore
        $folderName = rtrim($folderName, '_');
        
        return $folderName;
    }

    /**
     * Generate file name dengan prefix file type
     */
    public static function generateFileName($fileType, $originalName)
    {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        
        $typePrefix = [
            'laporan' => 'LAP',
            'penanggung_jawab' => 'PJ',
            'pembayaran' => 'PMB',
        ];

        $prefix = $typePrefix[$fileType] ?? $fileType;
        
        return $prefix . '_' . str_slug($name) . '.' . $ext;
    }

    /**
     * Get Google Drive web link untuk file
     */
    public static function getGoogleDriveLink($fileId)
    {
        return "https://drive.google.com/file/d/{$fileId}/view";
    }

    /**
     * Get Google Drive download link untuk file
     */
    public static function getGoogleDriveDownloadLink($fileId)
    {
        return "https://drive.google.com/uc?export=download&id={$fileId}";
    }

    /**
     * Get folder structure description
     */
    public static function getFolderStructure()
    {
        return <<<'EOT'
        Google Drive Folder Structure:
        ├── Root Folder (SPT_Progress)
        │   ├── Folder: SPT_001_2026
        │   │   ├── LAP_report.pdf
        │   │   ├── PJ_manager_document.pdf
        │   │   └── PMB_payment_proof.pdf
        │   ├── Folder: SPT_002_2026
        │   │   ├── LAP_report.docx
        │   │   ├── PJ_manager_document.pdf
        │   │   └── PMB_payment_proof.xlsx
        │   └── ...
        EOT;
    }
}
