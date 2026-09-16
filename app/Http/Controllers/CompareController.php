<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    private const MAX_COMPARE = 3;

    public function show(Request $request): View
    {
        $slugs = array_slice(
            array_filter(explode(',', (string) $request->query('slugs', ''))),
            0,
            self::MAX_COMPARE
        );

        $products = Product::query()
            ->active()
            ->whereIn('slug', $slugs)
            ->with(['series', 'variants'])
            ->get()
            // whereIn() doesn't preserve order — keep the order the user
            // picked the products in (as they appear in the slugs list).
            ->sortBy(fn (Product $product) => array_search($product->slug, $slugs, true))
            ->values();

        $specLabels = $products
            ->flatMap(fn (Product $product) => array_keys($product->specifications ?? []))
            ->unique()
            ->values();

        return view('compare.show', compact('products', 'specLabels'));
    }
}
