@php
    $metaDescription = $activeTopic
        ? App\Models\Post::TOPICS[$activeTopic].' iPhone: bài viết mới nhất từ phuonghihi — tư vấn chọn máy, so sánh đời máy và hướng dẫn sử dụng, cập nhật thường xuyên.'
        : 'Tin tức iPhone, tư vấn chọn máy, so sánh đời máy và hướng dẫn sử dụng từ phuonghihi. Bài viết ngắn gọn, đi thẳng vào câu hỏi người mua hay gặp.';
@endphp

<x-shop-layout
    title="Tin tức - phuonghihi"
    :description="$metaDescription"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <span class="text-ink">Tin tức</span>
        </nav>

        <h1 class="font-bold text-3xl text-ink">Tin tức &amp; tư vấn</h1>
        <p class="mt-2 text-sm text-ink-soft max-w-2xl leading-relaxed">
            Mọi thứ cần biết trước khi xuống tiền mua iPhone: chọn máy theo ngân sách, so sánh các bản, và những bước kiểm tra máy nên làm ngay tại quầy.
        </p>

        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('posts.index') }}"
               class="px-3 py-1.5 rounded-xl text-sm font-medium border transition {{ $activeTopic === null ? 'bg-brand text-white border-brand' : 'bg-white text-ink-soft border-line hover:border-brand hover:text-brand' }}">
                Tất cả
            </a>
            @foreach (App\Models\Post::TOPICS as $slug => $label)
                <a href="{{ route('posts.index', ['chu-de' => $slug]) }}"
                   class="px-3 py-1.5 rounded-xl text-sm font-medium border transition {{ $activeTopic === $slug ? 'bg-brand text-white border-brand' : 'bg-white text-ink-soft border-line hover:border-brand hover:text-brand' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($posts->isEmpty())
            <p class="mt-8 text-sm text-ink-soft">Chưa có bài viết nào trong mục này.</p>
        @else
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </div>
</x-shop-layout>
