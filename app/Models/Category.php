<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[Fillable(['parent_id', 'name', 'slug'])]
class Category extends Model
{
    public const NAV_CACHE_KEY = 'categories.nav-list';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Top-level categories shown in nav/filters, cached because they rarely
     * change. Only categories with at least one active product — otherwise
     * picking one is a dead end straight to an empty result page. Busted
     * from Admin\ProductController on every product create/update/delete/
     * toggle-status, since those are what change a category's emptiness.
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
            fn () => self::whereNull('parent_id')
                ->whereHas('products', fn ($q) => $q->where('status', 'active'))
                ->orderBy('name')->get()
                ->map(fn (self $category) => $category->getAttributes())
                ->all()
        );

        return self::hydrate($rows);
    }
}
