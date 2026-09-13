<?php

namespace App\Services;

final readonly class ImageVariant
{
    public function __construct(
        public string $url,
        public string $thumbUrl,
    ) {}
}
