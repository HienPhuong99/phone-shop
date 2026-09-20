@props(['post'])

<article class="group bg-white border border-line rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden flex flex-col">
    <a href="{{ route('posts.show', $post->slug) }}" class="block">
        <div class="relative aspect-[16/9] bg-gray-50 flex items-center justify-center overflow-hidden">
            @if ($post->thumbnail)
                <img src="{{ $post->thumbnail_thumb ?? $post->thumbnail }}" alt="{{ $post->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition" />
            @else
                <div class="w-full h-full flex items-center justify-center bg-[repeating-linear-gradient(45deg,theme(colors.line),theme(colors.line)_8px,transparent_8px,transparent_16px)]">
                    <span class="font-mono text-xs text-ink-soft bg-white px-2 py-0.5 rounded shadow-xs">ảnh bài viết</span>
                </div>
            @endif
            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-lg bg-brand text-white text-[11px] font-bold">{{ $post->topic_label }}</span>
        </div>
    </a>

    <div class="p-4 flex flex-col flex-1">
        <h3 class="text-base font-semibold text-ink leading-snug">
            <a href="{{ route('posts.show', $post->slug) }}" class="group-hover:text-brand transition">{{ $post->title }}</a>
        </h3>

        <p class="mt-2 text-sm text-ink-soft leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>

        <p class="mt-3 pt-3 border-t border-line text-xs text-ink-soft/80 flex items-center gap-2">
            <time datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">
                {{ ($post->published_at ?? $post->created_at)->format('d/m/Y') }}
            </time>
            <span aria-hidden="true">&middot;</span>
            <span>đọc {{ $post->reading_minutes }} phút</span>
        </p>
    </div>
</article>
