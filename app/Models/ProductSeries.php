<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[Fillable(['name', 'slug', 'description', 'sort_order'])]
class ProductSeries extends Model
{
    public const NAV_CACHE_KEY = 'product-series.nav-list';

    protected $table = 'product_series';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'series_id');
    }

    /**
     * All series shown in nav/filters, cached because they rarely change.
     *
     * @return Collection<int, self>
     */
    public static function navList(): Collection
    {
        // Cache raw attribute arrays and re-hydrate: the cache config disables
        // unserializing arbitrary objects, so a cached Eloquent Collection
        // would come back as __PHP_Incomplete_Class.
        $rows = Cache::remember(
            self::NAV_CACHE_KEY,
            now()->addHour(),
            fn () => self::orderBy('sort_order')->get()
                ->map(fn (self $series) => $series->getAttributes())
                ->all()
        );

        return self::hydrate($rows);
    }
}
