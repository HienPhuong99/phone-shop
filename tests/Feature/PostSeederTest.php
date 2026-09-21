<?php

namespace Tests\Feature;

use App\Models\Post;
use Database\Seeders\PostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PostSeeder::class);
    }

    public function test_every_seeded_article_is_readable_on_the_storefront(): void
    {
        $posts = Post::all();

        $this->assertNotEmpty($posts);

        foreach ($posts as $post) {
            $this->get(route('posts.show', $post->slug))->assertOk();
        }
    }

    /**
     * The articles cross-link to each other and to the pages that sell. A
     * typo in one of those paths is invisible on the page — the link just
     * renders and quietly leads to a 404, which costs a reader and tells
     * search engines the site links to nothing.
     */
    public function test_every_internal_link_in_the_seeded_articles_resolves(): void
    {
        $links = Post::all()
            ->flatMap(function (Post $post) {
                preg_match_all('~\]\((/[^)\s]*)\)~', $post->body, $matches);

                return $matches[1];
            })
            ->unique()
            ->values();

        $this->assertGreaterThan(10, $links->count());

        foreach ($links as $link) {
            $this->get($link)->assertOk();
        }
    }

    public function test_every_seeded_article_carries_the_three_faq_rows_the_schema_needs(): void
    {
        foreach (Post::all() as $post) {
            $this->assertCount(3, $post->answered_faqs, "Bài {$post->slug} thiếu câu hỏi thường gặp");
        }
    }
}
