<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A single product row inside the search dropdown's JSON response. Expects
 * `series` and `variants` to already be eager-loaded on the resource.
 */
class ProductSuggestionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cheapestVariant = $this->variants->sortBy('price')->first();

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'series' => $this->series?->name,
            'thumbnail' => $this->thumbnail_thumb ?? $this->thumbnail,
            'price' => (float) ($cheapestVariant?->price ?? $this->base_price),
            'in_stock' => $this->variants->sum('stock_quantity') > 0,
            'url' => route('products.show', $this->slug),
        ];
    }
}
