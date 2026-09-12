<x-admin-layout title="Danh mục - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Danh mục</h2>
        <a href="{{ route('admin.categories.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">+ Thêm danh mục</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @foreach ($categories as $category)
            <div class="flex items-center justify-between p-4">
                <div>
                    <p class="font-medium text-gray-900">{{ $category->name }}</p>
                    <p class="text-sm text-gray-500">{{ $category->slug }} @if($category->parent) — thuộc {{ $category->parent->name }} @endif</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Xoá danh mục này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Xoá</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-admin-layout>
