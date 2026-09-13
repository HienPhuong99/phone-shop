<x-admin-layout title="Sản phẩm - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Sản phẩm</h2>
        <a href="{{ route('admin.products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">+ Thêm sản phẩm</a>
    </div>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên..." class="rounded-md border-gray-300 text-sm w-64">
    </form>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @foreach ($products as $product)
            <div class="flex items-center justify-between p-4">
                <div>
                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                    <p class="text-sm text-gray-500">{{ $product->series->name }} — {{ $product->category->name }} — {{ number_format($product->base_price, 0, ',', '.') }}đ</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="px-2 py-1 rounded-full text-xs {{ $product->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $product->status === 'active' ? 'Đang bán' : 'Ẩn' }}
                    </span>
                    <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Xoá sản phẩm này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Xoá</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</x-admin-layout>
