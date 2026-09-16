@props(['action', 'categories', 'allSeries'])

@php
    /**
     * Build a query string identical to the current request, except with
     * $value removed from the array-valued filter $key (or the whole key
     * removed for a scalar filter). Empty arrays are dropped entirely so
     * they don't linger as `series[]=` in the URL.
     */
    $withoutValue = function (string $key, ?string $value = null) use ($action) {
        $params = request()->except(['page']);

        if ($value === null) {
            unset($params[$key]);
        } else {
            $params[$key] = array_values(array_diff((array) ($params[$key] ?? []), [$value]));
            if (empty($params[$key])) {
                unset($params[$key]);
            }
        }

        return $action.'?'.http_build_query($params);
    };

    $chips = [];

    if ($slug = request('category')) {
        $category = $categories->firstWhere('slug', $slug);
        if ($category) {
            $chips[] = ['label' => $category->name, 'href' => $withoutValue('category')];
        }
    }

    foreach ((array) request('series', []) as $slug) {
        $series = $allSeries->firstWhere('slug', $slug);
        if ($series) {
            $chips[] = ['label' => $series->name, 'href' => $withoutValue('series', $slug)];
        }
    }

    foreach ((array) request('storage', []) as $storage) {
        $chips[] = ['label' => $storage, 'href' => $withoutValue('storage', $storage)];
    }

    foreach ((array) request('color', []) as $color) {
        $chips[] = ['label' => $color, 'href' => $withoutValue('color', $color)];
    }

    if (request()->filled('min_price') || request()->filled('max_price')) {
        $min = request('min_price');
        $max = request('max_price');
        $label = match (true) {
            $min && $max => number_format($min / 1000000, 0).' - '.number_format($max / 1000000, 0).' triệu',
            (bool) $max => 'Dưới '.number_format($max / 1000000, 0).' triệu',
            default => 'Trên '.number_format($min / 1000000, 0).' triệu',
        };
        $chips[] = ['label' => $label, 'href' => (function () use ($action) {
            $params = request()->except(['min_price', 'max_price', 'page']);

            return $action.'?'.http_build_query($params);
        })()];
    }

    if (request()->boolean('in_stock')) {
        $chips[] = ['label' => 'Còn hàng', 'href' => $withoutValue('in_stock')];
    }
@endphp

@if (! empty($chips))
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach ($chips as $chip)
            <a href="{{ $chip['href'] }}" class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 rounded-full border border-brand bg-brand/5 text-brand text-xs font-semibold">
                {{ $chip['label'] }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </a>
        @endforeach

        <a href="{{ $action }}{{ request()->has('q') ? '?q='.urlencode(request('q')) : '' }}" class="text-xs font-semibold text-ink-soft hover:text-brand transition ml-1">Xoá tất cả</a>
    </div>
@endif
