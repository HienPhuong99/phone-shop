<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $topic = $request->string('chu-de')->toString();

        $posts = Post::query()
            ->published()
            ->topic($topic)
            ->latestPublished()
            ->paginate(9)
            ->withQueryString();

        $activeTopic = array_key_exists($topic, Post::TOPICS) ? $topic : null;

        return view('posts.index', compact('posts', 'activeTopic'));
    }

    public function show(Request $request, Post $post): View
    {
        // An unpublished post is a 404 for everyone except an admin, who
        // needs to open the real page to proofread before publishing.
        abort_unless($this->isReadable($post) || $request->user()?->is_admin, 404);

        $relatedPosts = Post::query()
            ->published()
            ->where('topic', $post->topic)
            ->whereKeyNot($post->getKey())
            ->latestPublished()
            ->take(3)
            ->get();

        // Every article ends on something buyable — an article that links
        // nowhere earns traffic the shop never converts.
        $suggestedProducts = Product::query()
            ->active()
            ->with(['series', 'variants'])
            ->withRatingStats()
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        return view('posts.show', compact('post', 'relatedPosts', 'suggestedProducts'));
    }

    private function isReadable(Post $post): bool
    {
        return $post->status === 'published'
            && ($post->published_at === null || ! $post->published_at->isFuture());
    }
}
