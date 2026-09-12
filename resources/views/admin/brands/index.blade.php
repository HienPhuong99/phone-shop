<x-admin-layout title="Thương hiệu - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Thương hiệu</h2>
        <a href="{{ route('admin.brands.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">+ Thêm thương hiệu</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @foreach ($brands as $brand)
            <div class="flex items-center justify-between p-4">
                <div>
                    <p class="font-medium text-gray-900">{{ $brand->name }}</p>
                    <p class="text-sm text-gray-500">{{ $brand->slug }}</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="text-indigo-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Xoá thương hiệu này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Xoá</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $brands->links() }}</div>
</x-admin-layout>
