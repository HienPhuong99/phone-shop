<x-admin-layout title="Sửa dòng sản phẩm - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Sửa dòng sản phẩm: {{ $productSeries->name }}</h2>

    <form method="POST" action="{{ route('admin.product-series.update', $productSeries) }}">
        @include('admin.product-series._form')
    </form>
</x-admin-layout>
