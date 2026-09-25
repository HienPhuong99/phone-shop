@php
    $publishedAt = $post->published_at ?? $post->created_at;
    $canonical = route('posts.show', $post->slug);

    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->seo_title,
        'description' => $post->seo_description,
        'datePublished' => $publishedAt->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
        'author' => ['@type' => 'Organization', 'name' => config('app.name')],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'logo' => ['@type' => 'ImageObject', 'url' => url('/images/logo-icon.svg')],
        ],
    ];

    if ($post->thumbnail) {
        $articleSchema['image'] = [url($post->thumbnail)];
    }

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tin tức', 'item' => route('posts.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $canonical],
        ],
    ];

    // The FAQ block is what an AI assistant can lift as a standalone
    // answer, so it ships as both visible text and FAQPage markup.
    $faqSchema = $post->answered_faqs === [] ? null : [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn (array $faq) => [
            '@type' => 'Question',
            'name' => $faq['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
        ], $post->answered_faqs),
    ];
@endphp

<x-shop-layout
    :title="$post->seo_title.' - phuonghihi'"
    :description="$post->seo_description"
    :canonical="$canonical"
    :og-image="$post->thumbnail ? url($post->thumbnail) : null"
    og-type="article"
>
    <x-slot:head>
        {{-- Plain json_encode() rather than Js::from() — see products/show.blade.php for why. --}}
        <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE) !!}</script>
        @if ($faqSchema)
            <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
    </x-slot:head>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <a href="{{ route('posts.index') }}" class="hover:text-brand transition">Tin tức</a> /
            <span class="text-ink">{{ $post->topic_label }}</span>
        </nav>

        @if ($post->status !== 'published' || $post->published_at?->isFuture())
            <p class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Bài này chưa hiện với khách — bạn đang xem bản xem trước với quyền quản trị.
            </p>
        @endif

        <article>
            <header>
                <p class="text-xs font-bold tracking-wide uppercase text-brand">{{ $post->topic_label }}</p>
                <h1 class="mt-2 font-bold text-3xl text-ink leading-tight">{{ $post->title }}</h1>
                <p class="mt-3 text-sm text-ink-soft flex flex-wrap items-center gap-2">
                    <time datetime="{{ $publishedAt->toDateString() }}">Cập nhật {{ $publishedAt->format('d/m/Y') }}</time>
                    <span aria-hidden="true">&middot;</span>
                    <span>đọc {{ $post->reading_minutes }} phút</span>
                </p>
            </header>

            @if ($post->thumbnail)
                <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="mt-6 w-full rounded-2xl border border-line" />
            @endif

            <p class="mt-6 text-base text-ink leading-relaxed font-medium">{{ $post->excerpt }}</p>

            @if (count($post->table_of_contents) > 1)
                <nav aria-label="Mục lục" class="mt-6 rounded-2xl border border-line bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-ink">Nội dung bài viết</p>
                    <ol class="mt-3 space-y-1.5 text-sm text-ink-soft list-decimal list-inside">
                        @foreach ($post->table_of_contents as $heading)
                            <li><a href="#{{ $heading['id'] }}" class="hover:text-brand transition">{{ $heading['text'] }}</a></li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            <div class="mt-8 space-y-4 [&_a]:text-brand [&_a]:underline [&_a]:underline-offset-2">
                @foreach ($post->body_blocks as $block)
                    @switch ($block['type'])
                        @case ('heading')
                            <h2 id="{{ $block['id'] }}" class="pt-4 text-xl font-bold text-ink scroll-mt-24">{{ $block['text'] }}</h2>
                            @break

                        @case ('subheading')
                            <h3 class="pt-2 text-base font-semibold text-ink">{{ $block['text'] }}</h3>
                            @break

                        @case ('list')
                            <ul class="space-y-2 pl-5 list-disc text-sm text-ink-soft leading-relaxed marker:text-brand">
                                @foreach ($block['items'] as $item)
                                    <li>{!! App\Models\Post::inlineHtml($item) !!}</li>
                                @endforeach
                            </ul>
                            @break

                        @case ('note')
                            <p class="rounded-2xl border border-line bg-paper px-5 py-4 text-sm text-ink leading-relaxed">{!! App\Models\Post::inlineHtml($block['text']) !!}</p>
                            @break

                        @default
                            <p class="text-sm text-ink-soft leading-relaxed">{!! App\Models\Post::inlineHtml($block['text']) !!}</p>
                    @endswitch
                @endforeach
            </div>

            @if ($post->answered_faqs !== [])
                <section class="mt-10">
                    <h2 class="text-xl font-bold text-ink">Câu hỏi thường gặp</h2>
                    <div class="mt-4 divide-y divide-line border border-line rounded-2xl bg-white shadow-sm">
                        @foreach ($post->answered_faqs as $faq)
                            <div class="p-5">
                                <p class="text-sm font-semibold text-ink">{{ $faq['question'] }}</p>
                                <p class="mt-2 text-sm text-ink-soft leading-relaxed">{{ $faq['answer'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </article>

        @if ($suggestedProducts->isNotEmpty())
            <section class="mt-12">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-xl font-bold text-ink">Máy đang bán chạy</h2>
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold text-brand hover:underline">Xem tất cả</a>
                </div>
                <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($suggestedProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($relatedPosts->isNotEmpty())
            <section class="mt-12">
                <h2 class="text-xl font-bold text-ink">Bài viết liên quan</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @foreach ($relatedPosts as $relatedPost)
                        <x-post-card :post="$relatedPost" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-shop-layout>
