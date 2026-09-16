<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['category_id', 'brand_id', 'series_id', 'name', 'slug', 'description', 'specifications', 'base_price', 'thumbnail', 'thumbnail_thumb', 'status', 'is_featured', 'featured_tagline', 'search_text'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'specifications' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        // Keep search_text (name + series + category, lowercased and
        // accent-stripped) in sync on every save so search matches
        // Vietnamese input typed without diacritics. This does NOT fire
        // during `db:seed` — DatabaseSeeder uses WithoutModelEvents, so
        // ProductSeeder sets search_text itself via buildSearchText().
        static::saving(function (self $product): void {
            $seriesName = $product->series_id
                ? ProductSeries::whereKey($product->series_id)->value('name')
                : null;

            $categoryName = $product->category_id
                ? Category::whereKey($product->category_id)->value('name')
                : null;

            $product->search_text = self::buildSearchText($product->name, $seriesName, $categoryName);
        });
    }

    public static function buildSearchText(string $name, ?string $seriesName, ?string $categoryName): string
    {
        return Str::lower(Str::ascii(
            collect([$name, $seriesName, $categoryName])->filter()->implode(' ')
        ));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(ProductSeries::class, 'series_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Match every space-separated word of $term against search_text (AND
     * between words), so typing more narrows the result set. $term is
     * normalized the same way search_text is, so diacritics don't matter.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $words = array_filter(explode(' ', Str::lower(Str::ascii(trim($term)))));

        foreach ($words as $word) {
            $query->where('search_text', 'like', '%'.$word.'%');
        }

        return $query;
    }

    /**
     * Apply the series/category/price filters shared by the product listing
     * and search results pages.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['series'])) {
            $query->whereHas('series', fn (Builder $q) => $q->where('slug', $filters['series']));
        }

        if (! empty($filters['category'])) {
            $query->whereHas('category', fn (Builder $q) => $q->where('slug', $filters['category']));
        }

        if (! empty($filters['min_price'])) {
            $query->where('base_price', '>=', (float) $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('base_price', '<=', (float) $filters['max_price']);
        }

        return $query;
    }

    public function scopeSorted(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            default => $query->latest(),
        };
    }

    public function getFinalPriceAttribute(): string
    {
        $cheapestVariant = $this->variants->sortBy('price')->first();

        return $cheapestVariant?->price ?? $this->base_price;
    }
}
