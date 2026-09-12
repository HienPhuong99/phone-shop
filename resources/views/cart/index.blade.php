@php
    $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->variant->price);
@endphp

<x-shop-layout title="Giỏ hàng - Phone Shop">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Giỏ hàng</h1>

        @if ($cart->items->isEmpty())
            <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-gray-500">
                Giỏ hàng của bạn đang trống.
                <a href="{{ route('products.index') }}" class="text-indigo-600 hover:underline block mt-2">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
                @foreach ($cart->items as $item)
                    <div class="flex items-center gap-4 p-4">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item->variant->product->slug) }}" class="font-medium text-gray-900 hover:underline">
                                {{ $item->variant->product->name }}
                            </a>
                            <p class="text-sm text-gray-500">{{ $item->variant->label }}</p>
                            <p class="text-sm text-indigo-600 font-medium">{{ number_format($item->variant->price, 0, ',', '.') }}đ</p>
                        </div>

                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->variant->stock_quantity }}" class="w-16 rounded-md border-gray-300 text-sm">
                            <button type="submit" class="text-sm text-indigo-600 hover:underline">Cập nhật</button>
                        </form>

                        <p class="w-28 text-right font-medium text-gray-900">{{ number_format($item->quantity * $item->variant->price, 0, ',', '.') }}đ</p>

                        <form method="POST" action="{{ route('cart.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:underline">Xoá</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                <p class="text-gray-600">Tạm tính</p>
                <p class="text-xl font-semibold text-gray-900">{{ number_format($subtotal, 0, ',', '.') }}đ</p>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('checkout.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-8 py-3 rounded-md">
                    Tiến hành thanh toán
                </a>
            </div>
        @endif
    </div>
</x-shop-layout>
