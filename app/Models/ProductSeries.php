<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'sort_order'])]
class ProductSeries extends Model
{
    protected $table = 'product_series';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'series_id');
    }
}
