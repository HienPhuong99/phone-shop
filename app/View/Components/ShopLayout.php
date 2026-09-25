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
        public ?string $canonical = null,
        public string $ogType = 'website',
        public bool $noindex = false,
    ) {}

    /**
     * The one URL this page wants search engines to keep. Defaults to the
     * current path with the query string dropped, so the dozens of filter
     * and sort combinations on /san-pham all point back at one address
     * instead of competing with each other. `page` is the exception: page
     * 2 is its own list of products, not a duplicate of page 1.
     */
    public function canonicalUrl(): string
    {
        $base = $this->canonical ?? url()->current();
        $page = (int) request()->query('page');

        if ($page > 1) {
            $base .= (str_contains($base, '?') ? '&' : '?').'page='.$page;
        }

        return $base;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.shop');
    }
}
