<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

#[Fillable(['category_id', 'brand_id', 'series_id', 'name', 'slug', 'description', 'specifications', 'spec_highlights', 'base_price', 'compare_at_price', 'thumbnail', 'thumbnail_thumb', 'status', 'is_featured', 'featured_tagline', 'search_text'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * Total stock at or below this is "sắp hết hàng" rather than "còn hàng".
     */
    private const LOW_STOCK_THRESHOLD = 5;

    /**
     * Mirrors the products table's own default(false) for is_featured
     * (migration 2026_09_16_075458). Without this, a freshly created()
     * model has no is_featured attribute at all until it's reloaded from
     * the database — the column default is applied by the database, not
     * reflected back onto the in-memory model — so $product->is_featured
     * reads as null instead of false right after create().
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_featured' => false,
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'specifications' => 'array',
            'spec_highlights' => 'array',
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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Adds reviews_count and reviews_avg_rating (Laravel's withCount/
     * withAvg naming) via SQL aggregates — cheap on listing pages, unlike
     * eager-loading every review row just to average it in PHP.
     */
    public function scopeWithRatingStats(Builder $query): Builder
    {
        return $query->withCount('reviews')->withAvg('reviews', 'rating');
    }

    /**
     * Order items sold across this product's variants, for the "Bán chạy"
     * sort. Excludes cancelled orders — those were never real demand.
     */
    public function orderItems(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, ProductVariant::class, 'product_id', 'variant_id')
            ->whereHas('order', fn (Builder $q) => $q->where('status', '!=', Order::STATUS_CANCELLED));
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
     * Apply the series/category/price/storage/color/stock filters shared by
     * the product listing and search results pages. series/storage/color
     * each accept either a single value or an array of values (checkboxes
     * post arrays, a quick-filter link posts one string).
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['series'])) {
            $query->whereHas('series', fn (Builder $q) => $q->whereIn('slug', Arr::wrap($filters['series'])));
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

        if (! empty($filters['storage'])) {
            $query->whereHas('variants', fn (Builder $q) => $q->whereIn('storage', Arr::wrap($filters['storage'])));
        }

        if (! empty($filters['color'])) {
            $query->whereHas('variants', fn (Builder $q) => $q->whereIn('color', Arr::wrap($filters['color'])));
        }

        if (! empty($filters['in_stock'])) {
            $query->whereHas('variants', fn (Builder $q) => $q->where('stock_quantity', '>', 0));
        }

        return $query;
    }

    public function scopeSorted(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            // "* 1.0" forces floating-point division — on SQLite, dividing
            // two integer columns truncates to 0 for every product whose
            // discount is under 100%, which silently broke this sort.
            'discount' => $query->orderByRaw(
                'CASE WHEN compare_at_price IS NOT NULL AND compare_at_price > base_price
                    THEN (compare_at_price - base_price) * 1.0 / compare_at_price
                    ELSE 0 END DESC'
            ),
            'best_selling' => $query->withSum('orderItems as sold_count', 'quantity')->orderByDesc('sold_count'),
            default => $query->latest(),
        };
    }

    public function getFinalPriceAttribute(): string
    {
        $cheapestVariant = $this->variants->sortBy('price')->first();

        return $cheapestVariant?->price ?? $this->base_price;
    }

    /**
     * Percent off compare_at_price, or null when there's nothing to show
     * (no compare price set, or it's not actually higher than base_price).
     */
    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->compare_at_price === null || (float) $this->compare_at_price <= (float) $this->base_price) {
            return null;
        }

        return (int) round((1 - ((float) $this->base_price / (float) $this->compare_at_price)) * 100);
    }

    /**
     * Relies on `variants` being eager-loaded — same convention as
     * getFinalPriceAttribute() above, no extra query.
     */
    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants->sum('stock_quantity');
    }

    public function getStockStatusAttribute(): string
    {
        return match (true) {
            $this->total_stock <= 0 => 'out_of_stock',
            $this->total_stock <= self::LOW_STOCK_THRESHOLD => 'low_stock',
            default => 'in_stock',
        };
    }

    public function getStockLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'Hết hàng',
            'low_stock' => 'Sắp hết hàng',
            default => "Còn {$this->total_stock} máy",
        };
    }

    /**
     * Rounded average rating, or null when withRatingStats() wasn't applied
     * or the product has no reviews yet.
     */
    public function getAverageRatingAttribute(): ?float
    {
        return $this->reviews_avg_rating !== null ? round((float) $this->reviews_avg_rating, 1) : null;
    }

    /**
     * Splits the description into typed blocks so the page can give each
     * its own weight instead of rendering one flat run of text.
     *
     * Every seeded description follows the same shape: a lead paragraph,
     * a chapter title, then alternating sub-heading/body pairs. Anything
     * that does not follow it still renders — trailing lines fall through
     * as body paragraphs.
     *
     * @return list<array{type: 'lead'|'chapter'|'heading'|'body', text: string}>
     */
    public function getDescriptionBlocksAttribute(): array
    {
        $lines = array_values(array_filter(
            array_map('trim', preg_split('/\R/', (string) $this->description) ?: []),
            fn (string $line) => $line !== ''
        ));

        if ($lines === []) {
            return [];
        }

        $blocks = [['type' => 'lead', 'text' => array_shift($lines)]];

        if ($lines !== []) {
            $blocks[] = ['type' => 'chapter', 'text' => array_shift($lines)];
        }

        // What is left alternates heading, body, heading, body…
        foreach ($lines as $index => $line) {
            $blocks[] = ['type' => $index % 2 === 0 ? 'heading' : 'body', 'text' => $line];
        }

        return $blocks;
    }

    /**
     * Distinct storages across this product's variants, smallest first.
     *
     * @return Collection<int, string>
     */
    public function getStorageOptionsAttribute(): Collection
    {
        return $this->variants
            ->pluck('storage')
            ->unique()
            ->sortBy(fn (string $storage) => self::storageSortKey($storage))
            ->values();
    }

    /**
     * Distinct storages across every active product's variants, for the
     * listing/search filter sidebar. Cheap enough on this catalog size to
     * run uncached — revisit if the catalog grows into the thousands.
     *
     * @return Collection<int, string>
     */
    public static function availableStorages(): Collection
    {
        return ProductVariant::whereHas('product', fn (Builder $q) => $q->active())
            ->distinct()
            ->pluck('storage')
            ->sortBy(fn (string $storage) => self::storageSortKey($storage))
            ->values();
    }

    /**
     * Distinct colors across every active product's variants, for the
     * listing/search filter sidebar.
     *
     * @return Collection<int, string>
     */
    public static function availableColors(): Collection
    {
        return ProductVariant::whereHas('product', fn (Builder $q) => $q->active())
            ->distinct()
            ->orderBy('color')
            ->pluck('color');
    }

    /**
     * Sorts "128GB" < "256GB" < ... < "1TB" — a plain string sort would put
     * "1TB" before "256GB".
     */
    private static function storageSortKey(string $storage): int
    {
        return str_contains($storage, 'TB') ? ((int) $storage) * 1024 : (int) $storage;
    }
}
