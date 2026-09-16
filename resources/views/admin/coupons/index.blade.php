<x-admin-layout title="Mã giảm giá - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Mã giảm giá</h2>
        <a href="{{ route('admin.coupons.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">+ Thêm mã giảm giá</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($coupons as $coupon)
            <div class="flex items-center justify-between p-4">
                <div>
                    <p class="font-medium text-gray-900 font-mono">{{ $coupon->code }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $coupon->type === 'percent' ? 'Giảm '.rtrim(rtrim(number_format($coupon->value, 2), '0'), '.').'%' : 'Giảm '.number_format($coupon->value, 0, ',', '.').'đ' }}
                        @if ($coupon->min_order_amount)
                            &middot; đơn tối thiểu {{ number_format($coupon->min_order_amount, 0, ',', '.') }}đ
                        @endif
                        &middot; đã dùng {{ $coupon->used_count }}{{ $coupon->max_uses ? '/'.$coupon->max_uses : '' }}
                        @if ($coupon->expires_at)
                            &middot; hết hạn {{ $coupon->expires_at->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $coupon->active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $coupon->active ? 'Đang bật' : 'Đã tắt' }}
                    </span>
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-indigo-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Xoá mã giảm giá này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Xoá</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-4 text-sm text-gray-500">Chưa có mã giảm giá nào.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $coupons->links() }}</div>
</x-admin-layout>
