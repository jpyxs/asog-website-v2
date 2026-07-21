<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * ImageUpload — reusable library for handling image uploads.
 *
 * Usage:
 *   $uploader = new \App\Libraries\ImageUpload();
 *   $path = $uploader->upload($file, 'posts');
 *   // returns relative path like "uploads/posts/company-logo-a1b2c3d4.webp" or null on failure
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

        $newName = self::readableFileName($file, 'image', $this->extensionForMime($mimeType));

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

        log_message('info', '[ImageUpload] Success: ' . $relativePath . ' (' . filesize($finalPath) . ' bytes)');

        $this->generateVariants($relativePath, $subfolder);

        // Return path relative to the public accessor
        return $relativePath;
    }

    /**
    * Config: which sidecar widths to generate per subfolder, matching the
     * naming convention responsiveUploadImg() already expects.
     */
    protected array $variantWidths = [
        'posts'      => ['100', '180', '400'],   // crop 3:2 (matches responsiveUploadImg 'posts' case)
        'incubatees' => ['160'],                 // contain, square-ish (matches 'incubatees' case)
        'team'       => ['300', '500'],   // contain, square-ish (matches 'team' case); team-org (44/88) & team-landing (88/160) display sizes
    ];

    /** Standardized "full" display width per subfolder — resized, but saved with no width suffix. */
    protected array $fullSizeWidth = [
        'posts'      => 900,
        'incubatees' => 400,
        'team'       => 900,
    ];
    
    /** Safety cap: skip variant generation if decoded bitmap would exceed this many bytes. */
    protected int $maxDecodeMemoryBytes = 80 * 1024 * 1024; // 80MB

    /**
     * Generate responsive sidecar variants for an uploaded image.
     * Call this after upload() succeeds. Never throws — logs and returns
     * silently on any failure, since the original upload already succeeded.
     *
     * @param string $relativePath  e.g. "uploads/posts/company-logo-a1b2c3d4.webp"
     * @param string $subfolder     'posts' | 'incubatees'
     */
    public function generateVariants(string $relativePath, string $subfolder): void
    {
        if (! isset($this->variantWidths[$subfolder])) {
            return; // no variants configured for this subfolder
        }

        $fullPath = FCPATH . $relativePath;
        if (! is_file($fullPath)) {
            log_message('error', '[ImageUpload] Variant source not found: ' . $fullPath);
            return;
        }

        try {
            $this->doGenerateVariants($fullPath, $relativePath, $subfolder);
        } catch (\Throwable $e) {
            // Never let variant failure affect the already-successful upload.
            log_message('error', '[ImageUpload] Variant generation failed for '
                . $relativePath . ': ' . $e->getMessage());
        }
    }

    private function doGenerateVariants(string $fullPath, string $relativePath, string $subfolder): void
    {
        if (! extension_loaded('gd')) {
            log_message('warning', '[ImageUpload] GD extension not available — skipping variant generation.');
            return;
        }

        $info = @getimagesize($fullPath);
        if ($info === false) {
            log_message('error', '[ImageUpload] Could not read image dimensions: ' . $fullPath);
            return;
        }

        [$srcW, $srcH, $imageType] = $info;

        // Memory safety check before decoding — GD needs roughly width*height*4 bytes for the bitmap.
        $estimatedBytes = $srcW * $srcH * 4;
        if ($estimatedBytes > $this->maxDecodeMemoryBytes) {
            log_message('warning', "[ImageUpload] Image too large to safely process ({$srcW}x{$srcH}) — skipping variants for {$relativePath}.");
            return;
        }

        $srcImage = $this->decodeImage($fullPath, $imageType);
        if ($srcImage === null) {
            log_message('error', '[ImageUpload] Unsupported or corrupt image, cannot decode: ' . $fullPath);
            return;
        }

        $outputFormat = $this->bestOutputFormat(); // 'webp' | 'png' | 'jpg'

        $stem = pathinfo($relativePath, PATHINFO_FILENAME);
        $dir  = rtrim(dirname($relativePath), '/\\');

        foreach ($this->variantWidths[$subfolder] as $targetWidth) {
            $targetWidth = (int) $targetWidth;

            $variant = in_array($subfolder, ['incubatees', 'team'], true)
                ? $this->resizeContainSquare($srcImage, $srcW, $srcH, $targetWidth)
                : $this->resizeCrop3x2($srcImage, $srcW, $srcH, $targetWidth);

            if ($variant === null) {
                continue;
            }

            $variantRelPath = $dir . '/' . $stem . '-' . $targetWidth . 'w.' . $outputFormat;
            $variantFullPath = FCPATH . $variantRelPath;

            $this->saveImage($variant, $variantFullPath, $outputFormat);
            imagedestroy($variant);

            log_message('info', "[ImageUpload] Generated variant ({$outputFormat}): {$variantRelPath}");
        }

        // Standardized "full" size — resized for consistency, but named with no width suffix (matches static-asset convention).
        if (isset($this->fullSizeWidth[$subfolder])) {
            $fullWidth = $this->fullSizeWidth[$subfolder];

            $fullVariantRelPath = $dir . '/' . $stem . '.' . $outputFormat;

            // Never overwrite the original upload — only generate if this would be a distinct file.
            if ($fullVariantRelPath !== $relativePath) {
                $fullVariant = in_array($subfolder, ['incubatees', 'team'], true)
                    ? $this->resizeContainSquare($srcImage, $srcW, $srcH, $fullWidth)
                    : $this->resizeCrop3x2($srcImage, $srcW, $srcH, $fullWidth);

                if ($fullVariant !== null) {
                    $fullVariantFullPath = FCPATH . $fullVariantRelPath;
                    $this->saveImage($fullVariant, $fullVariantFullPath, $outputFormat);
                    imagedestroy($fullVariant);

                    log_message('info', "[ImageUpload] Generated full-size variant ({$outputFormat}, {$fullWidth}w): {$fullVariantRelPath}");
                }
            } else {
                log_message('info', "[ImageUpload] Skipped full-size variant — would overwrite original at {$relativePath}.");
            }
        }

        imagedestroy($srcImage);
    }

    /**
     * Decode a source image based on its detected type. Cross-format:
     * handles JPEG, PNG, GIF, and WebP inputs.
     */
    private function decodeImage(string $path, int $imageType): ?\GdImage
    {
        $image = match ($imageType) {
            IMAGETYPE_JPEG => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : null,
            IMAGETYPE_PNG  => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : null,
            IMAGETYPE_GIF  => function_exists('imagecreatefromgif') ? @imagecreatefromgif($path) : null,
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default        => null,
        };

        if (! $image instanceof \GdImage) {
            return null;
        }

        // Preserve transparency for PNG/GIF sources when later resizing.
        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * Resize + center-crop to a 3:2 aspect ratio at the target width (posts).
     */
    private function resizeCrop3x2(\GdImage $src, int $srcW, int $srcH, int $targetWidth): ?\GdImage
    {
        $targetHeight = (int) round($targetWidth * (2 / 3));

        $srcRatio = $srcW / $srcH;
        $targetRatio = $targetWidth / $targetHeight;

        if ($srcRatio > $targetRatio) {
            // source is wider than target ratio — crop left/right
            $cropH = $srcH;
            $cropW = (int) round($srcH * $targetRatio);
        } else {
            // source is taller than target ratio — crop top/bottom
            $cropW = $srcW;
            $cropH = (int) round($srcW / $targetRatio);
        }

        $cropX = (int) (($srcW - $cropW) / 2);
        $cropY = (int) (($srcH - $cropH) / 2);

        $dst = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($dst === false) {
            return null;
        }

        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        $ok = imagecopyresampled(
            $dst, $src,
            0, 0, $cropX, $cropY,
            $targetWidth, $targetHeight, $cropW, $cropH
        );

        return $ok ? $dst : null;
    }

    /**
     * Resize to fit within a target width×width square, preserving aspect
     * ratio, transparent-padded (contain, not cropped) — for incubatees logos.
     */
    private function resizeContainSquare(\GdImage $src, int $srcW, int $srcH, int $targetSize): ?\GdImage
    {
        $scale = min($targetSize / $srcW, $targetSize / $srcH);
        $newW = max(1, (int) round($srcW * $scale));
        $newH = max(1, (int) round($srcH * $scale));

        $dst = imagecreatetruecolor($targetSize, $targetSize);
        if ($dst === false) {
            return null;
        }

        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        $offsetX = (int) (($targetSize - $newW) / 2);
        $offsetY = (int) (($targetSize - $newH) / 2);

        $ok = imagecopyresampled(
            $dst, $src,
            $offsetX, $offsetY, 0, 0,
            $newW, $newH, $srcW, $srcH
        );

        return $ok ? $dst : null;
    }

    /**
     * Save a GD image resource to disk. Tries WebP first, falls back to
     * PNG (preserves transparency), then JPEG (universal but flattens alpha),
     * depending on what this server's GD build actually supports.
     */
    private function saveImage(\GdImage $image, string $fullPath, string $format): void
    {
        $dir = dirname($fullPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        switch ($format) {
            case 'webp':
                imagewebp($image, $fullPath, 82);
                return;

            case 'png':
                imagesavealpha($image, true);
                imagepng($image, $fullPath, 6); // compression 0-9, 6 is a reasonable default
                return;

            case 'jpg':
            default:
                $width = imagesx($image);
                $height = imagesy($image);
                $flattened = imagecreatetruecolor($width, $height);
                $white = imagecolorallocate($flattened, 255, 255, 255);
                imagefill($flattened, 0, 0, $white);
                imagecopy($flattened, $image, 0, 0, 0, 0, $width, $height);
                imagejpeg($flattened, $fullPath, 82);
                imagedestroy($flattened);
                return;
        }
    }

    /**
     * Determine the best available output format this server's GD build supports.
     */
    private function bestOutputFormat(): string
    {
        if (function_exists('imagewebp')) {
            return 'webp';
        }
        if (function_exists('imagepng')) {
            return 'png';
        }
        return 'jpg'; // imagejpeg() is present on essentially every GD build
    }
    
    /**
     * Delete an uploaded image and any responsive variants generated for it.
     */
    public function delete(?string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        $fullPath = FCPATH . $relativePath;
        $deletedOriginal = false;

        if (is_file($fullPath)) {
            $deletedOriginal = unlink($fullPath);
        }

        $this->deleteVariants($relativePath);

        return $deletedOriginal;
    }

    /**
     * Delete all responsive sidecar variants for a given uploaded image,
     * regardless of which output format they were generated in.
     */
    private function deleteVariants(string $relativePath): void
    {
        $parts = explode('/', trim($relativePath, '/'));
        $subfolder = $parts[1] ?? null;

        if ($subfolder === null || ! isset($this->variantWidths[$subfolder])) {
            return;
        }

        $stem = pathinfo($relativePath, PATHINFO_FILENAME);
        $dir  = rtrim(dirname($relativePath), '/\\');

        foreach ($this->variantWidths[$subfolder] as $targetWidth) {
            foreach (['webp', 'png', 'jpg'] as $ext) {
                $variantRelPath = $dir . '/' . $stem . '-' . $targetWidth . 'w.' . $ext;
                if (is_file(FCPATH . $variantRelPath)) {
                    unlink(FCPATH . $variantRelPath);
                    log_message('info', "[ImageUpload] Deleted variant: {$variantRelPath}");
                }
            }
        }

        // Also delete the full-size (no width suffix) variant, if one was generated.
        foreach (['webp', 'png', 'jpg'] as $ext) {
            $fullVariantRelPath = $dir . '/' . $stem . '.' . $ext;
            if ($fullVariantRelPath !== $relativePath && is_file(FCPATH . $fullVariantRelPath)) {
                unlink(FCPATH . $fullVariantRelPath);
                log_message('info', "[ImageUpload] Deleted full-size variant: {$fullVariantRelPath}");
            }
        }
    }

    /**
     * Return the last error message.
     */
    public function getError(): string
    {
        return $this->error;
    }

    public static function readableFileName(UploadedFile $file, string $fallbackBase = 'upload', ?string $extension = null): string
    {
        $clientName = trim((string) $file->getClientName());
        $base = pathinfo($clientName, PATHINFO_FILENAME);
        $base = self::slugFileBase($base !== '' ? $base : $fallbackBase);

        if ($base === '') {
            $base = self::slugFileBase($fallbackBase) ?: 'upload';
        }

        $base = substr($base, 0, 64);
        $base = trim($base, '-');
        $suffix = bin2hex(random_bytes(4));
        $ext = strtolower(trim((string) ($extension ?: $file->getClientExtension()), '.'));

        if ($ext === '') {
            $ext = strtolower(pathinfo($clientName, PATHINFO_EXTENSION));
        }

        return $base . '-' . $suffix . ($ext !== '' ? '.' . $ext : '');
    }

    private static function slugFileBase(string $value): string
    {
        $value = trim($value);
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if (is_string($converted) && $converted !== '') {
            $value = $converted;
        }

        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = preg_replace('/-+/', '-', $value) ?? '';

        return trim($value, '-');
    }

    private function extensionForMime(string $mimeType): ?string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => null,
        };
    }

}