<x-admin-layout title="Thêm danh mục - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thêm danh mục</h2>

    <form method="POST" action="{{ route('admin.categories.store') }}">
        @include('admin.categories._form')
    </form>
</x-admin-layout>
