<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(private readonly ImageUploadService $imageUploadService) {}

    public function index(Request $request): View
    {
        $posts = Post::query()
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('thumbnail')) {
            $image = $this->imageUploadService->store($request->file('thumbnail'), 'posts');
            $data['thumbnail'] = $image->url;
            $data['thumbnail_thumb'] = $image->thumbUrl;
        }

        $post = Post::create($data);

        return redirect()->route('admin.posts.edit', $post)->with('status', 'Đã tạo bài viết.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validated($request, $post);

        if ($request->hasFile('thumbnail')) {
            $image = $this->imageUploadService->store($request->file('thumbnail'), 'posts');
            $data['thumbnail'] = $image->url;
            $data['thumbnail_thumb'] = $image->thumbUrl;
        }

        $post->update($data);

        return redirect()->route('admin.posts.edit', $post)->with('status', 'Đã cập nhật bài viết.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Đã xoá bài viết.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'topic' => ['required', 'string', 'in:'.implode(',', array_keys(Post::TOPICS))],
            'focus_keyword' => ['nullable', 'string', 'max:120'],
            'excerpt' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string'],
            // Google cuts a title around 60 characters and a description
            // around 160, so the form refuses what would be truncated.
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'faqs' => ['nullable', 'array', 'max:6'],
            'faqs.*.question' => ['nullable', 'string', 'max:200', 'required_with:faqs.*.answer'],
            'faqs.*.answer' => ['nullable', 'string', 'max:600', 'required_with:faqs.*.question'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
        ]);

        unset($data['thumbnail']);

        // Nullable fields the admin left empty are absent from the validated
        // data, so every optional key is normalised before it is used.
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['title'], $post);
        $data['faqs'] = $this->cleanFaqs($data['faqs'] ?? []);
        $data['focus_keyword'] = $data['focus_keyword'] ?? null;
        $data['meta_title'] = $data['meta_title'] ?? null;
        $data['meta_description'] = $data['meta_description'] ?? null;
        $data['published_at'] = $data['published_at'] ?? null;

        // Publishing without picking a time means "now", otherwise the post
        // would count as scheduled-with-no-date and never surface.
        if ($data['status'] === 'published' && blank($data['published_at'])) {
            $data['published_at'] = $post?->published_at ?? now();
        }

        return $data;
    }

    /**
     * A slug is the article's permanent address: changing it throws away
     * whatever ranking and inbound links that address had earned. So an
     * existing post keeps its slug unless the admin typed a new one by
     * hand, and a new post derives one from the title.
     */
    private function resolveSlug(?string $submitted, string $title, ?Post $post): string
    {
        if (blank($submitted) && $post) {
            return $post->slug;
        }

        $slug = Str::slug($submitted ?: $title);

        if ($post && $post->slug === $slug) {
            return $slug;
        }

        return Post::where('slug', $slug)->whereKeyNot($post?->getKey())->exists()
            ? $slug.'-'.Str::lower(Str::random(4))
            : $slug;
    }

    /**
     * Drop the rows the admin left blank — the form always posts its three
     * FAQ rows, filled in or not.
     *
     * @param  array<int, array{question?: string|null, answer?: string|null}>  $faqs
     * @return list<array{question: string, answer: string}>|null
     */
    private function cleanFaqs(array $faqs): ?array
    {
        $cleaned = array_values(array_map(
            fn (array $faq) => ['question' => trim($faq['question']), 'answer' => trim($faq['answer'])],
            array_filter($faqs, fn (array $faq) => filled($faq['question'] ?? null) && filled($faq['answer'] ?? null))
        ));

        return $cleaned === [] ? null : $cleaned;
    }
}
