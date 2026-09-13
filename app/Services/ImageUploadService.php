<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    private const MAX_DIMENSION = 1200;

    private const JPEG_QUALITY = 82;

    /** @var list<string> */
    private const OPTIMIZABLE_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * Store an uploaded image, downscaling it to a max dimension and
     * re-encoding it as JPEG to keep product photos light on the storefront.
     * Formats GD cannot decode (e.g. SVG) are stored as-is.
     */
    public function store(UploadedFile $file, string $directory): string
    {
        if (! in_array($file->getMimeType(), self::OPTIMIZABLE_MIMES, true)) {
            return Storage::disk('public')->url($file->store($directory, 'public'));
        }

        $source = $this->readSource($file);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        [$width, $height] = $this->fitDimensions($sourceWidth, $sourceHeight, self::MAX_DIMENSION);

        $canvas = imagecreatetruecolor($width, $height);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
        imagedestroy($source);

        $path = $directory.'/'.Str::random(40).'.jpg';

        ob_start();
        imagejpeg($canvas, null, self::JPEG_QUALITY);
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
