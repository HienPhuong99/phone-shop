<x-admin-layout title="Sửa thương hiệu - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Sửa thương hiệu</h2>

    <form method="POST" action="{{ route('admin.brands.update', $brand) }}">
        @include('admin.brands._form')
    </form>
</x-admin-layout>
