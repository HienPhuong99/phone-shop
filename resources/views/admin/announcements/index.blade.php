<x-admin-layout title="Thông báo - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Thông báo nghỉ lễ / khuyến mãi</h2>
        <a href="{{ route('admin.announcements.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">+ Thêm thông báo</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($announcements as $announcement)
            @php
                $statusColor = match ($announcement->schedule_label) {
                    'Đang hiện' => 'bg-emerald-100 text-emerald-700',
                    'Lên lịch' => 'bg-amber-100 text-amber-700',
                    default => 'bg-gray-100 text-gray-500',
                };
            @endphp
            <div class="flex items-center justify-between p-4">
                <div>
                    <p class="font-medium text-gray-900">{{ $announcement->title }}</p>
                    <p class="text-sm text-gray-500">
                        {{ Illuminate\Support\Str::limit($announcement->message, 80) }}
                        @if ($announcement->starts_at)
                            &middot; từ {{ $announcement->starts_at->format('d/m/Y H:i') }}
                        @endif
                        @if ($announcement->ends_at)
                            &middot; đến {{ $announcement->ends_at->format('d/m/Y H:i') }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $statusColor }}">
                        {{ $announcement->schedule_label }}
                    </span>
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-indigo-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Xoá thông báo này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Xoá</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-4 text-sm text-gray-500">Chưa có thông báo nào.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $announcements->links() }}</div>
</x-admin-layout>
