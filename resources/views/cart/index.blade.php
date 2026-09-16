@php
    $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->variant->price);
@endphp

<x-shop-layout title="Giỏ hàng - phuonghihi">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-bold text-3xl text-ink mb-6">Giỏ hàng</h1>

        @if ($cart->items->isEmpty())
            <div class="border border-line rounded-2xl bg-white shadow-sm p-10 text-center text-ink-soft">
                Giỏ hàng của bạn đang trống.
                <a href="{{ route('products.index') }}" class="text-brand hover:text-brand-dark font-medium block mt-2">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="border border-line rounded-2xl bg-white shadow-sm divide-y divide-line overflow-hidden">
                @foreach ($cart->items as $item)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 p-4">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item->variant->product->slug) }}" class="font-semibold text-ink hover:text-brand transition">
                                {{ $item->variant->product->name }}
                            </a>
                            <p class="text-sm text-ink-soft">{{ $item->variant->label }}</p>
                            <p class="text-sm text-brand font-semibold">{{ number_format($item->variant->price, 0, ',', '.') }}đ</p>
                        </div>

                        <div class="flex items-center justify-between gap-3 sm:justify-end sm:gap-4">
                            <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" inputmode="numeric" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->variant->stock_quantity }}" class="w-16 rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                                <button type="submit" class="text-sm font-medium text-brand hover:text-brand-dark">Cập nhật</button>
                            </form>

                            <p class="sm:w-28 text-right font-semibold text-ink">{{ number_format($item->quantity * $item->variant->price, 0, ',', '.') }}đ</p>

                            <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-500 hover:text-red-700 hover:underline">Xoá</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 border border-line rounded-2xl bg-white shadow-sm p-4">
                <p class="text-sm font-semibold text-ink mb-2">Mã giảm giá</p>
                @if ($coupon)
                    <div class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 rounded-xl px-3.5 py-2.5">
                        <span class="text-sm text-emerald-800">
                            Mã <span class="font-bold">{{ $coupon->code }}</span> đã áp dụng — giảm {{ number_format($discount, 0, ',', '.') }}đ
                        </span>
                        <form method="POST" action="{{ route('cart.coupon.remove') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Bỏ</button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="code" placeholder="Nhập mã giảm giá" class="flex-1 rounded-xl border-line text-sm focus:border-brand focus:ring-brand uppercase placeholder:normal-case">
                        <button type="submit" class="px-4 py-2 rounded-xl border-[1.5px] border-brand text-brand text-sm font-semibold hover:bg-brand hover:text-white transition">Áp dụng</button>
                    </form>
                @endif
            </div>

            <div class="mt-4 border border-line rounded-2xl bg-white shadow-sm p-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <p class="text-ink-soft">Tạm tính</p>
                    <p class="font-medium text-ink">{{ number_format($subtotal, 0, ',', '.') }}đ</p>
                </div>
                @if ($discount > 0)
                    <div class="flex items-center justify-between text-sm">
                        <p class="text-ink-soft">Giảm giá</p>
                        <p class="font-medium text-emerald-600">−{{ number_format($discount, 0, ',', '.') }}đ</p>
                    </div>
                @endif
                <div class="flex items-center justify-between pt-2 border-t border-line">
                    <p class="text-ink-soft">Tạm tính sau giảm giá</p>
                    <p class="text-xl font-bold text-ink">{{ number_format($subtotal - $discount, 0, ',', '.') }}đ</p>
                </div>
                <p class="text-xs text-ink-soft">Chưa gồm phí vận chuyển, tính theo khu vực nhận hàng ở bước thanh toán.</p>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('checkout.index') }}" class="bg-brand hover:bg-brand-dark text-white font-semibold px-8 py-3 rounded-2xl shadow-sm transition">
                    Tiến hành thanh toán
                </a>
            </div>
        @endif
    </div>
</x-shop-layout>
