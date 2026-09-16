<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductSuggestionResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Below this length we don't query at all — too short to be
     * meaningful, and it would match almost every row.
     */
    private const MIN_QUERY_LENGTH = 2;

    private const SUGGESTION_LIMIT = 5;

    public function index(Request $request): View
    {
        $term = trim($request->string('q')->toString());

        $products = Product::query()
            ->active()
            ->with(['series', 'category', 'variants'])
            ->withRatingStats()
            ->when($term !== '', fn ($query) => $query->search($term))
            ->filter($request->all())
            ->sorted($request->string('sort')->toString())
            ->paginate(12)
            ->withQueryString();

        $suggestion = $products->isEmpty() && $term !== ''
            ? $this->closestProductName($term)
            : null;

        $allSeries = ProductSeries::navList();
        $categories = Category::navList();
        $storageOptions = Product::availableStorages();
        $colorOptions = Product::availableColors();

        return view('search.index', compact('products', 'term', 'suggestion', 'allSeries', 'categories', 'storageOptions', 'colorOptions'));
    }

    /**
     * JSON suggestions for the header search dropdown.
     */
    public function suggest(Request $request): JsonResponse
    {
        $term = trim($request->string('q')->toString());

        if (mb_strlen($term) < self::MIN_QUERY_LENGTH) {
            return response()->json(['products' => [], 'total' => 0]);
        }

        $query = Product::query()->active()->search($term);

        $products = (clone $query)
            ->with(['series', 'variants'])
            ->limit(self::SUGGESTION_LIMIT)
            ->get();

        return response()->json([
            'products' => ProductSuggestionResource::collection($products),
            'total' => $query->count(),
        ]);
    }

    /**
     * "Có phải bạn muốn tìm...?" — the closest active product name to a
     * mistyped search term, for the zero-result page. Only suggested when
     * genuinely close (a handful of edits), not just the least-bad option.
     *
     * @return array{name: string, slug: string}|null
     */
    private function closestProductName(string $term): ?array
    {
        $normalizedTerm = Str::lower(Str::ascii($term));

        $best = null;
        $bestDistance = null;

        foreach (Product::query()->active()->pluck('name', 'slug') as $slug => $name) {
            $distance = levenshtein($normalizedTerm, Str::lower(Str::ascii($name)));

            if ($bestDistance === null || $distance < $bestDistance) {
                $best = ['name' => $name, 'slug' => (string) $slug];
                $bestDistance = $distance;
            }
        }

        return $bestDistance !== null && $bestDistance <= 3 ? $best : null;
    }
}
