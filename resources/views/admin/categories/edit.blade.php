<x-admin-layout title="Sửa danh mục - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Sửa danh mục</h2>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @include('admin.categories._form')
    </form>
</x-admin-layout>
