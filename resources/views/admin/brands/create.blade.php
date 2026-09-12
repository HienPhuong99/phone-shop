<x-admin-layout title="Thêm thương hiệu - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thêm thương hiệu</h2>

    <form method="POST" action="{{ route('admin.brands.store') }}">
        @include('admin.brands._form')
    </form>
</x-admin-layout>
