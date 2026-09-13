@php
    $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->variant->price);
@endphp

<x-shop-layout title="Giỏ hàng - Phone Shop">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-serif text-[32px] font-medium text-ink mb-6">Giỏ hàng</h1>

        @if ($cart->items->isEmpty())
            <div class="border border-line rounded-[2px] bg-white p-10 text-center text-ink-soft">
                Giỏ hàng của bạn đang trống.
                <a href="{{ route('products.index') }}" class="text-accent hover:text-accent-dark block mt-2">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="border border-line rounded-[2px] bg-white divide-y divide-line">
                @foreach ($cart->items as $item)
                    <div class="flex items-center gap-4 p-4">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item->variant->product->slug) }}" class="font-semibold text-ink hover:text-accent">
                                {{ $item->variant->product->name }}
                            </a>
                            <p class="text-sm text-ink-soft">{{ $item->variant->label }}</p>
                            <p class="text-sm text-accent font-medium">{{ number_format($item->variant->price, 0, ',', '.') }}đ</p>
                        </div>

                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->variant->stock_quantity }}" class="w-16 rounded-[2px] border-line text-sm">
                            <button type="submit" class="text-sm text-accent hover:text-accent-dark">Cập nhật</button>
                        </form>

                        <p class="w-28 text-right font-semibold text-ink">{{ number_format($item->quantity * $item->variant->price, 0, ',', '.') }}đ</p>

                        <form method="POST" action="{{ route('cart.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:underline">Xoá</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 border border-line rounded-[2px] bg-white p-4 flex items-center justify-between">
                <p class="text-ink-soft">Tạm tính</p>
                <p class="text-xl font-semibold text-ink">{{ number_format($subtotal, 0, ',', '.') }}đ</p>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('checkout.index') }}" class="bg-accent hover:bg-accent-dark text-white font-medium px-8 py-3 rounded-[2px]">
                    Tiến hành thanh toán
                </a>
            </div>
        @endif
    </div>
</x-shop-layout>
