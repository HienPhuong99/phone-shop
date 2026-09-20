<x-admin-layout title="Tin tức - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Bài viết</h2>
        <a href="{{ route('admin.posts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">+ Viết bài mới</a>
    </div>

    <form method="GET" class="mb-4">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm theo tiêu đề..."
               class="w-full max-w-sm rounded-md border-gray-300 text-sm">
    </form>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($posts as $post)
            @php
                $statusColor = match ($post->status_label) {
                    'Đang hiện' => 'bg-emerald-100 text-emerald-700',
                    'Lên lịch' => 'bg-amber-100 text-amber-700',
                    default => 'bg-gray-100 text-gray-500',
                };
            @endphp
            <div class="flex items-center justify-between p-4 gap-4">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 truncate">{{ $post->title }}</p>
                    <p class="text-sm text-gray-500 truncate">
                        {{ $post->topic_label }}
                        @if ($post->focus_keyword)
                            &middot; từ khoá: {{ $post->focus_keyword }}
                        @endif
                        @if ($post->published_at)
                            &middot; {{ $post->published_at->format('d/m/Y H:i') }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-3 text-sm shrink-0">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $statusColor }}">{{ $post->status_label }}</span>
                    <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="text-gray-500 hover:underline">Xem</a>
                    <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Xoá bài viết này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Xoá</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-4 text-sm text-gray-500">Chưa có bài viết nào. Bấm "Viết bài mới" để bắt đầu.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
</x-admin-layout>
