<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'password', 'is_admin' => true]);
        $this->regularUser = User::create(['name' => 'User', 'email' => 'user@test.com', 'password' => 'password']);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/posts')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_posts(): void
    {
        $this->actingAs($this->regularUser)->get('/admin/posts')->assertForbidden();
    }

    public function test_admin_can_publish_a_post_and_the_slug_is_built_from_the_title(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/posts', $this->payload());

        $post = Post::firstWhere('slug', 'nen-mua-iphone-nao-2026');

        $response->assertRedirect(route('admin.posts.edit', $post));
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_a_draft_is_saved_without_a_publish_time(): void
    {
        $this->actingAs($this->admin)->post('/admin/posts', $this->payload(['status' => 'draft']));

        $this->assertNull(Post::firstWhere('slug', 'nen-mua-iphone-nao-2026')->published_at);
    }

    public function test_a_publish_time_in_the_future_is_kept_and_the_post_stays_hidden(): void
    {
        $publishAt = now()->addWeek()->startOfMinute();

        $this->actingAs($this->admin)->post('/admin/posts', $this->payload([
            'published_at' => $publishAt->format('Y-m-d\TH:i'),
        ]));

        $post = Post::firstWhere('slug', 'nen-mua-iphone-nao-2026');

        $this->assertTrue($publishAt->equalTo($post->published_at));
        $this->assertSame('Lên lịch', $post->status_label);
        $this->get(route('posts.index'))->assertDontSee($post->title);
    }

    public function test_a_second_post_with_the_same_title_gets_its_own_slug(): void
    {
        Post::factory()->create(['slug' => 'nen-mua-iphone-nao-2026']);

        $this->actingAs($this->admin)->post('/admin/posts', $this->payload());

        $this->assertSame(2, Post::where('slug', 'like', 'nen-mua-iphone-nao-2026%')->count());
    }

    public function test_empty_faq_rows_are_dropped_and_filled_rows_are_kept(): void
    {
        $this->actingAs($this->admin)->post('/admin/posts', $this->payload([
            'faqs' => [
                ['question' => 'Pin 80% có nên thay?', 'answer' => 'Nên thay ở mốc này.'],
                ['question' => '', 'answer' => ''],
                ['question' => '', 'answer' => ''],
            ],
        ]));

        $this->assertSame(
            [['question' => 'Pin 80% có nên thay?', 'answer' => 'Nên thay ở mốc này.']],
            Post::firstWhere('slug', 'nen-mua-iphone-nao-2026')->faqs
        );
    }

    public function test_an_answer_without_its_question_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/posts', $this->payload([
            'faqs' => [['question' => '', 'answer' => 'Câu trả lời mồ côi']],
        ]));

        $response->assertInvalid(['faqs.0.question']);
    }

    public function test_an_empty_post_reports_every_missing_field(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/posts', []);

        $response->assertInvalid(['title', 'topic', 'excerpt', 'body', 'status']);
    }

    public function test_a_meta_description_over_160_characters_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/posts', $this->payload([
            'meta_description' => str_repeat('a', 161),
        ]));

        $response->assertInvalid(['meta_description']);
    }

    public function test_an_unknown_topic_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/posts', $this->payload(['topic' => 'khong-co-that']));

        $response->assertInvalid(['topic']);
    }

    public function test_editing_a_post_keeps_the_address_it_was_published_at(): void
    {
        $post = Post::factory()->create(['slug' => 'cach-kiem-tra-pin-iphone', 'title' => 'Tiêu đề cũ']);

        $this->actingAs($this->admin)->patch(route('admin.posts.update', $post), $this->payload([
            'title' => 'Tiêu đề đã sửa hoàn toàn khác',
        ]));

        $this->assertSame('cach-kiem-tra-pin-iphone', $post->refresh()->slug);
        $this->assertSame('Tiêu đề đã sửa hoàn toàn khác', $post->title);
    }

    public function test_the_list_filters_by_state(): void
    {
        Post::factory()->create(['title' => 'Bài đang hiện']);
        Post::factory()->scheduled()->create(['title' => 'Bài hẹn giờ']);
        Post::factory()->draft()->create(['title' => 'Bài nháp']);

        $response = $this->actingAs($this->admin)->get('/admin/posts?trang-thai=len-lich');

        $response->assertSee('Bài hẹn giờ');
        $response->assertDontSee('Bài đang hiện');
        $response->assertDontSee('Bài nháp');
    }

    public function test_the_list_puts_scheduled_articles_first(): void
    {
        Post::factory()->create(['title' => 'Bài đã đăng hôm qua']);
        Post::factory()->scheduled()->create(['title' => 'Bài hẹn giờ tuần sau']);

        $response = $this->actingAs($this->admin)->get('/admin/posts');

        $response->assertSeeInOrder(['Bài hẹn giờ tuần sau', 'Bài đã đăng hôm qua']);
    }

    public function test_admin_can_delete_a_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.posts.destroy', $post));

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertModelMissing($post);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Nên mua iPhone nào 2026',
            'topic' => 'tu-van',
            'excerpt' => 'Bốn nhóm ngân sách, bốn gợi ý máy cụ thể.',
            'body' => "Đoạn mở đầu trả lời thẳng câu hỏi.\n## Chọn theo ngân sách\nNội dung mục một.",
            'status' => 'published',
        ], $overrides);
    }
}
