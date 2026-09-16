<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ShopLayout extends Component
{
    public function __construct(
        public ?string $title = null,
        public bool $hideBottomNav = false,
        public ?string $description = null,
        public ?string $ogImage = null,
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.shop');
    }
}
