<x-admin-layout title="Thêm dòng sản phẩm - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thêm dòng sản phẩm</h2>

    <form method="POST" action="{{ route('admin.product-series.store') }}">
        @include('admin.product-series._form')
    </form>
</x-admin-layout>
