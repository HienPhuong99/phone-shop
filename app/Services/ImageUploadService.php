<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    private const FULL_MAX_DIMENSION = 1200;

    private const THUMB_MAX_DIMENSION = 600;

    private const WEBP_QUALITY = 80;

    /** @var list<string> */
    private const OPTIMIZABLE_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * Store an uploaded image as two WebP variants: a full size (capped at
     * FULL_MAX_DIMENSION, for detail/zoom views) and a thumb (capped at
     * THUMB_MAX_DIMENSION, for grid cards and gallery strips), so listing
     * pages never ship a full-resolution photo for a small preview.
     * Formats GD cannot decode (e.g. SVG) are stored as-is, with both
     * variants pointing at the same file.
     */
    public function store(UploadedFile $file, string $directory): ImageVariant
    {
        if (! in_array($file->getMimeType(), self::OPTIMIZABLE_MIMES, true)) {
            $url = Storage::disk('public')->url($file->store($directory, 'public'));

            return new ImageVariant($url, $url);
        }

        $source = $this->readSource($file);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $basename = Str::random(40);
        $url = $this->renderVariant($source, $sourceWidth, $sourceHeight, self::FULL_MAX_DIMENSION, $directory, $basename);
        $thumbUrl = $this->renderVariant($source, $sourceWidth, $sourceHeight, self::THUMB_MAX_DIMENSION, $directory, $basename.'-thumb');

        imagedestroy($source);

        return new ImageVariant($url, $thumbUrl);
    }

    private function renderVariant(GdImage $source, int $sourceWidth, int $sourceHeight, int $maxDimension, string $directory, string $filename): string
    {
        [$width, $height] = $this->fitDimensions($sourceWidth, $sourceHeight, $maxDimension);

        $canvas = imagecreatetruecolor($width, $height);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        $path = $directory.'/'.$filename.'.webp';

        ob_start();
        imagewebp($canvas, null, self::WEBP_QUALITY);
        Storage::disk('public')->put($path, ob_get_clean());
        imagedestroy($canvas);

        return Storage::disk('public')->url($path);
    }

    private function readSource(UploadedFile $file): GdImage
    {
        return match ($file->getMimeType()) {
            'image/png' => imagecreatefrompng($file->getRealPath()),
            'image/webp' => imagecreatefromwebp($file->getRealPath()),
            'image/gif' => imagecreatefromgif($file->getRealPath()),
            default => imagecreatefromjpeg($file->getRealPath()),
        };
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function fitDimensions(int $width, int $height, int $maxDimension): array
    {
        if ($width <= $maxDimension && $height <= $maxDimension) {
            return [$width, $height];
        }

        $ratio = min($maxDimension / $width, $maxDimension / $height);

        return [(int) round($width * $ratio), (int) round($height * $ratio)];
    }
}
