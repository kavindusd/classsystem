<?php

/**
 * Handle file uploads (payment slips, favicon etc.)
 */
class UploadHelper {

    public static function uploadSlip(array $file, int $studentId, int $courseId): string|false {
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxSize      = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) return false;
        if ($file['size'] > $maxSize) return false;

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = "slip_{$studentId}_{$courseId}_" . date('Ym') . ".{$ext}";
        $dest     = ROOT . '/public/uploads/slips/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return $filename;
        }
        return false;
    }
}
