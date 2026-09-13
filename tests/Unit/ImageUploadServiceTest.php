<?php

namespace Tests\Unit;

use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadServiceTest extends TestCase
{
    public function test_it_downscales_an_oversized_image_and_reencodes_it_as_webp(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.png', 2000, 1500);

        $image = (new ImageUploadService)->store($file, 'products');

        $path = 'products/'.basename(parse_url($image->url, PHP_URL_PATH));
        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        [$width, $height] = getimagesize(Storage::disk('public')->path($path));
        $this->assertLessThanOrEqual(1200, $width);
        $this->assertLessThanOrEqual(1200, $height);
        $this->assertEqualsWithDelta(4 / 3, $width / $height, 0.01);
    }

    public function test_it_leaves_an_already_small_full_image_at_its_original_size(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg', 400, 300);

        $image = (new ImageUploadService)->store($file, 'products');

        $path = 'products/'.basename(parse_url($image->url, PHP_URL_PATH));
        [$width, $height] = getimagesize(Storage::disk('public')->path($path));

        $this->assertSame(400, $width);
        $this->assertSame(300, $height);
    }

    public function test_it_also_generates_a_smaller_thumb_variant(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg', 2000, 1500);

        $image = (new ImageUploadService)->store($file, 'products');

        $this->assertNotSame($image->url, $image->thumbUrl);

        $thumbPath = 'products/'.basename(parse_url($image->thumbUrl, PHP_URL_PATH));
        $this->assertStringEndsWith('-thumb.webp', $thumbPath);
        Storage::disk('public')->assertExists($thumbPath);

        [$thumbWidth, $thumbHeight] = getimagesize(Storage::disk('public')->path($thumbPath));
        $this->assertLessThanOrEqual(600, $thumbWidth);
        $this->assertLessThanOrEqual(600, $thumbHeight);
        $this->assertEqualsWithDelta(4 / 3, $thumbWidth / $thumbHeight, 0.01);
    }
}
