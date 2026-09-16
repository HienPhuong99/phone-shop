<x-admin-layout title="Sửa mã giảm giá - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Sửa mã giảm giá: {{ $coupon->code }}</h2>

    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
        @include('admin.coupons._form')
    </form>
</x-admin-layout>
