<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Paths with nothing to index: a cart or a set of search results is
     * different for every visitor, and crawling them only burns the budget
     * that should go to products and articles. Served from a route rather
     * than public/robots.txt so the Sitemap line carries whatever domain
     * the app is actually running on.
     *
     * @var list<string>
     */
    private const DISALLOWED = [
        '/admin',
        '/gio-hang',
        '/thanh-toan',
        '/tim-kiem',
        '/so-sanh',
        '/yeu-thich',
        '/don-hang',
        '/profile',
        '/dashboard',
        '/vnpay',
    ];

    public function __invoke(): Response
    {
        $lines = ['User-agent: *', 'Allow: /'];

        foreach (self::DISALLOWED as $path) {
            $lines[] = 'Disallow: '.$path;
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.route('sitemap');

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
