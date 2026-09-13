<?php

namespace Tests\Unit;

use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadServiceTest extends TestCase
{
    public function test_it_downscales_an_oversized_image_and_reencodes_it_as_jpeg(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.png', 2000, 1500);

        $url = (new ImageUploadService)->store($file, 'products');

        $path = 'products/'.basename(parse_url($url, PHP_URL_PATH));
        $this->assertStringEndsWith('.jpg', $path);
        Storage::disk('public')->assertExists($path);

        [$width, $height] = getimagesize(Storage::disk('public')->path($path));
        $this->assertLessThanOrEqual(1200, $width);
        $this->assertLessThanOrEqual(1200, $height);
        $this->assertEqualsWithDelta(4 / 3, $width / $height, 0.01);
    }

    public function test_it_leaves_an_already_small_image_at_its_original_size(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg', 400, 300);

        $url = (new ImageUploadService)->store($file, 'products');

        $path = 'products/'.basename(parse_url($url, PHP_URL_PATH));
        [$width, $height] = getimagesize(Storage::disk('public')->path($path));

        $this->assertSame(400, $width);
        $this->assertSame(300, $height);
    }
}
