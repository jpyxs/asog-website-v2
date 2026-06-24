<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * ImageUpload — reusable library for handling image uploads.
 *
 * Usage:
 *   $uploader = new \App\Libraries\ImageUpload();
 *   $path = $uploader->upload($file, 'posts');
 *   // returns relative path like "uploads/posts/abc123.webp" or null on failure
 */
class ImageUpload
{
    /** Base upload directory inside `public/uploads/`. */
    protected string $basePath;

    /** Maximum file size in bytes. */
    protected int $maxSize = 104857600; // 100 MB (100 * 1024 * 1024)

    /** Allowed MIME types. */
    protected array $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /** Last error message. */
    protected string $error = '';

    /** Whether to convert uploaded images to WebP. */
    protected bool $convertToWebp = true;

    /** WebP output quality (0-100). */
    protected int $webpQuality = 82;

    public function __construct()
    {
        $this->basePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
    }

    /**
     * Upload an image file to the given subfolder.
     *
     * @param  UploadedFile|null $file          The uploaded file instance.
     * @param  string            $subfolder     e.g. "posts", "team"
     * @param  int|null          $maxSizeBytes  Optional per-upload size cap in bytes.
     * @return string|null                      Relative path from public/ or null on failure.
     */
    public function upload(?UploadedFile $file, string $subfolder = 'posts', ?int $maxSizeBytes = null): ?string
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            $this->error = 'No valid file was uploaded.';
            log_message('error', '[ImageUpload] ' . $this->error);
            return null;
        }

        // Resolve MIME once before move() because temp upload path is removed after moving.
        $mimeType = $file->getMimeType() ?: $file->getClientMimeType();

        // Validate MIME
        if (! in_array($mimeType, $this->allowedTypes, true)) {
            $this->error = 'Invalid file type (' . $mimeType . '). Allowed: JPG, PNG, GIF, WEBP.';
            log_message('error', '[ImageUpload] ' . $this->error);
            return null;
        }

        // Validate size (compare raw bytes to avoid number_format string bug)
        $fileSizeBytes = $file->getSize();
        $sizeLimitBytes = $maxSizeBytes ?? $this->maxSize;
        if ($fileSizeBytes > $sizeLimitBytes) {
            $maxMB = round($sizeLimitBytes / 1048576, 1);
            $fileMB = round($fileSizeBytes / 1048576, 1);
            $this->error = "File ({$fileMB} MB) exceeds the maximum size of {$maxMB} MB.";
            log_message('error', '[ImageUpload] ' . $this->error);
            return null;
        }

        $destination = $this->basePath . $subfolder;

        // Ensure destination directory exists and is writable
        if (! is_dir($destination)) {
            if (! mkdir($destination, 0755, true)) {
                $this->error = 'Could not create upload directory: ' . $destination;
                log_message('error', '[ImageUpload] ' . $this->error);
                return null;
            }
        }

        if (! is_writable($destination)) {
            $this->error = 'Upload directory is not writable: ' . $destination;
            log_message('error', '[ImageUpload] ' . $this->error);
            return null;
        }

        // Generate unique filename
        $newName = $file->getRandomName();

        try {
            $file->move($destination, $newName);
        } catch (\Throwable $e) {
            $this->error = 'Failed to move uploaded file: ' . $e->getMessage();
            log_message('error', '[ImageUpload] ' . $this->error);
            return null;
        }

        // Verify the file was actually written
        $finalPath = $destination . DIRECTORY_SEPARATOR . $newName;
        if (! is_file($finalPath)) {
            $this->error = 'File was moved but not found at destination.';
            log_message('error', '[ImageUpload] ' . $this->error . ' Expected: ' . $finalPath);
            return null;
        }

        $relativePath = 'uploads/' . $subfolder . '/' . $newName;

        // Convert to WebP when possible; keep original as fallback.
        if ($this->convertToWebp && $this->shouldConvertToWebp($mimeType)) {
            $webpName = pathinfo($newName, PATHINFO_FILENAME) . '.webp';
            $webpPath = $destination . DIRECTORY_SEPARATOR . $webpName;

            if ($this->convertImageToWebp($finalPath, $webpPath, $mimeType)) {
                if ($webpPath !== $finalPath && is_file($finalPath)) {
                    @unlink($finalPath);
                }

                $finalPath = $webpPath;
                $relativePath = 'uploads/' . $subfolder . '/' . $webpName;
            }
        }

        log_message('info', '[ImageUpload] Success: ' . $relativePath . ' (' . filesize($finalPath) . ' bytes)');

        // Return path relative to the public accessor
        return $relativePath;
    }

    /**
     * Delete an uploaded image.
     */
    public function delete(?string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        $fullPath = FCPATH . $relativePath;

        if (is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * Return the last error message.
     */
    public function getError(): string
    {
        return $this->error;
    }

    protected function shouldConvertToWebp(string $mimeType): bool
    {
        return in_array($mimeType, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ], true);
    }

    protected function convertImageToWebp(string $sourcePath, string $targetPath, string $mimeType): bool
    {
        if (! function_exists('imagewebp')) {
            log_message('warning', '[ImageUpload] WebP conversion skipped: imagewebp() is unavailable.');
            return false;
        }

        $loader = match ($mimeType) {
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png'  => 'imagecreatefrompng',
            'image/gif'  => 'imagecreatefromgif',
            'image/webp' => 'imagecreatefromwebp',
            default      => null,
        };

        if ($loader === null || ! function_exists($loader)) {
            log_message('warning', '[ImageUpload] WebP conversion skipped: loader unavailable for ' . $mimeType . '.');
            return false;
        }

        $image = @$loader($sourcePath);
        if (! $image) {
            log_message('warning', '[ImageUpload] WebP conversion skipped: unable to read source image.');
            return false;
        }

        // Preserve transparency for indexed/alpha images where possible.
        if (function_exists('imagepalettetotruecolor')) {
            @imagepalettetotruecolor($image);
        }
        @imagealphablending($image, true);
        @imagesavealpha($image, true);

        $written = @imagewebp($image, $targetPath, $this->webpQuality);
        imagedestroy($image);

        if (! $written || ! is_file($targetPath)) {
            log_message('warning', '[ImageUpload] WebP conversion skipped: failed to write target image.');
            return false;
        }

        return true;
    }
}
