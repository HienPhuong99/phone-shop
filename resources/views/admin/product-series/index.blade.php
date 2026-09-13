<x-admin-layout title="Dòng sản phẩm - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Dòng sản phẩm</h2>
        <a href="{{ route('admin.product-series.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">+ Thêm dòng sản phẩm</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($productSeries as $series)
            <div class="flex items-center justify-between p-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center bg-gray-100 text-gray-700 text-xs font-medium px-2 py-0.5 rounded">
                            #{{ $series->sort_order }}
                        </span>
                        <p class="font-medium text-gray-900">{{ $series->name }}</p>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-500">
                        <span>{{ $series->slug }}</span>
                        <span>&bull;</span>
                        <span>{{ $series->products_count }} sản phẩm</span>
                    </div>
                    @if ($series->description)
                        <p class="text-xs text-gray-400">{{ $series->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('admin.product-series.edit', $series) }}" class="text-indigo-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.product-series.destroy', $series) }}" onsubmit="return confirm('Xoá dòng sản phẩm này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Xoá</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                Chưa có dòng sản phẩm nào.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $productSeries->links() }}</div>
</x-admin-layout>
