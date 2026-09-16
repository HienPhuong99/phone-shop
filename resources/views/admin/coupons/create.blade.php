<x-admin-layout title="Thêm mã giảm giá - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thêm mã giảm giá</h2>

    <form method="POST" action="{{ route('admin.coupons.store') }}">
        @include('admin.coupons._form')
    </form>
</x-admin-layout>
