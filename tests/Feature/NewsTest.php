<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_shows_published_posts_and_hides_drafts(): void
    {
        Post::factory()->create(['title' => 'Nên mua iPhone nào 2026']);
        Post::factory()->draft()->create(['title' => 'Bài còn đang viết dở']);

        $response = $this->get(route('posts.index'));

        $response->assertSee('Nên mua iPhone nào 2026');
        $response->assertDontSee('Bài còn đang viết dở');
    }

    public function test_listing_hides_a_post_scheduled_for_later(): void
    {
        Post::factory()->scheduled()->create(['title' => 'Bài hẹn giờ tuần sau']);

        $response = $this->get(route('posts.index'));

        $response->assertDontSee('Bài hẹn giờ tuần sau');
    }

    public function test_topic_filter_keeps_only_posts_of_that_topic(): void
    {
        Post::factory()->create(['title' => 'So sánh hai đời máy', 'topic' => 'so-sanh']);
        Post::factory()->create(['title' => 'Hướng dẫn kiểm tra pin', 'topic' => 'huong-dan']);

        $response = $this->get(route('posts.index', ['chu-de' => 'so-sanh']));

        $response->assertSee('So sánh hai đời máy');
        $response->assertDontSee('Hướng dẫn kiểm tra pin');
    }

    public function test_article_renders_its_headings_lists_and_internal_links(): void
    {
        $post = Post::factory()->create([
            'title' => 'Cách kiểm tra pin iPhone',
            'body' => "Mở Cài đặt rồi xem dung lượng tối đa.\n## Mốc nào nên thay pin\n- Dưới 80% thì nên thay\n> Nhiệt độ hại pin hơn số lần sạc\nXem thêm [bảng giá máy](/san-pham).",
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertSee('<h2 id="moc-nao-nen-thay-pin"', false);
        $response->assertSee('Dưới 80% thì nên thay');
        $response->assertSee('Nhiệt độ hại pin hơn số lần sạc');
        $response->assertSee('<a href="/san-pham">bảng giá máy</a>', false);
    }

    public function test_article_body_cannot_smuggle_html_or_a_javascript_link(): void
    {
        $post = Post::factory()->create([
            'body' => "## Mục\n<script>alert('xin chao')</script> và [bấm đây](javascript:alert(1)).",
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertDontSee('<script>alert', false);
        $response->assertDontSee('href="javascript:', false);
    }

    public function test_faq_rows_render_as_visible_text_and_faqpage_markup(): void
    {
        $post = Post::factory()->create([
            'faqs' => [
                ['question' => 'Pin còn 80% có nên thay không?', 'answer' => 'Nên thay ở mốc này.'],
                ['question' => '', 'answer' => 'Câu trả lời không có câu hỏi'],
            ],
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertSee('Pin còn 80% có nên thay không?');
        $response->assertSee('"@type":"FAQPage"', false);
        $response->assertDontSee('Câu trả lời không có câu hỏi');
    }

    public function test_draft_article_returns_404_for_a_visitor(): void
    {
        $post = Post::factory()->draft()->create();

        $this->get(route('posts.show', $post->slug))->assertNotFound();
    }

    public function test_draft_article_is_previewable_by_an_admin(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'password', 'is_admin' => true]);
        $post = Post::factory()->draft()->create(['title' => 'Bản nháp chờ duyệt']);

        $response = $this->actingAs($admin)->get(route('posts.show', $post->slug));

        $response->assertSee('Bản nháp chờ duyệt');
        $response->assertSee('bạn đang xem bản xem trước với quyền quản trị', false);
    }

    public function test_home_page_links_to_the_three_newest_articles(): void
    {
        Post::factory()->count(4)->sequence(
            ['title' => 'Bài mới nhất', 'published_at' => now()->subDay()],
            ['title' => 'Bài thứ hai', 'published_at' => now()->subDays(2)],
            ['title' => 'Bài thứ ba', 'published_at' => now()->subDays(3)],
            ['title' => 'Bài cũ nhất', 'published_at' => now()->subDays(4)],
        )->create();

        $response = $this->get(route('home'));

        $response->assertSee('Bài mới nhất');
        $response->assertSee('Bài thứ ba');
        $response->assertDontSee('Bài cũ nhất');
    }
}
