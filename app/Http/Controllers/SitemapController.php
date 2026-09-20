<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Route names of the pages that always exist, paired with how often
     * they are worth recrawling. Filtered listing URLs (/san-pham?series=…)
     * are deliberately absent: they all canonicalise back to /san-pham, so
     * listing them here would point crawlers at addresses the pages
     * themselves disown.
     *
     * @var array<string, string>
     */
    private const STATIC_ROUTES = [
        'home' => 'daily',
        'products.index' => 'daily',
        'posts.index' => 'daily',
        'pages.services' => 'monthly',
        'pages.installment' => 'monthly',
        'pages.trade-in' => 'monthly',
        'pages.policies' => 'yearly',
        'pages.policies.warranty' => 'yearly',
        'pages.policies.returns' => 'yearly',
        'pages.policies.shipping' => 'yearly',
        'pages.policies.privacy' => 'yearly',
        'pages.about' => 'yearly',
        'pages.contact' => 'yearly',
    ];

    public function __invoke(): Response
    {
        $urls = [];

        foreach (self::STATIC_ROUTES as $name => $changeFrequency) {
            $urls[] = ['loc' => route($name), 'changefreq' => $changeFrequency];
        }

        foreach (Product::active()->latest('updated_at')->get(['slug', 'updated_at']) as $product) {
            $urls[] = [
                'loc' => route('products.show', $product->slug),
                'lastmod' => $product->updated_at->toAtomString(),
                'changefreq' => 'weekly',
            ];
        }

        foreach (Post::published()->latest('updated_at')->get(['slug', 'updated_at']) as $post) {
            $urls[] = [
                'loc' => route('posts.show', $post->slug),
                'lastmod' => $post->updated_at->toAtomString(),
                'changefreq' => 'monthly',
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
