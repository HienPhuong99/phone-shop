@php
    $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->variant->price);
@endphp

<x-shop-layout title="Thanh toán - Phone Shop">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-serif text-[32px] font-medium text-ink mb-6">Thanh toán</h1>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 text-red-800 border border-red-200 rounded-md px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            <div class="lg:col-span-2 space-y-6">
                <section class="border border-line rounded-[2px] bg-white p-5">
                    <h2 class="font-semibold text-ink mb-4">Địa chỉ giao hàng</h2>

                    @if ($addresses->isNotEmpty())
                        <div class="space-y-2 mb-4">
                            @foreach ($addresses as $address)
                                <label class="flex items-start gap-3 p-3 border rounded-[2px] cursor-pointer {{ $loop->first ? 'border-accent border-2' : 'border-line' }}">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }} class="mt-1">
                                    <span class="text-sm">
                                        <span class="font-medium text-ink">{{ $address->recipient_name }}</span> — {{ $address->phone }}<br>
                                        <span class="text-ink-soft">{{ $address->address_line }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <details {{ $addresses->isEmpty() ? 'open' : '' }}>
                        <summary class="text-sm text-accent cursor-pointer">+ Thêm địa chỉ mới</summary>
                        <div class="mt-3 space-y-3">
                            <div>
                                <x-input-label for="recipient_name" value="Họ tên người nhận" />
                                <x-text-input id="recipient_name" name="recipient_name" class="block mt-1 w-full" :value="old('recipient_name')" />
                            </div>
                            <div>
                                <x-input-label for="phone" value="Số điện thoại" />
                                <x-text-input id="phone" name="phone" class="block mt-1 w-full" :value="old('phone')" />
                            </div>
                            <div>
                                <x-input-label for="address_line" value="Địa chỉ" />
                                <x-text-input id="address_line" name="address_line" class="block mt-1 w-full" :value="old('address_line')" />
                            </div>
                        </div>
                    </details>
                </section>

                <section class="border border-line rounded-[2px] bg-white p-5">
                    <h2 class="font-semibold text-ink mb-4">Phương thức thanh toán</h2>
                    <label class="flex items-center gap-3 p-3 border border-accent border-2 rounded-[2px] cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" checked>
                        <span class="text-sm text-ink">Thanh toán khi nhận hàng (COD)</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-line rounded-[2px] mt-2 cursor-pointer">
                        <input type="radio" name="payment_method" value="vnpay">
                        <span class="text-sm text-ink">Thanh toán qua VNPay</span>
                    </label>
                </section>
            </div>

            <div class="lg:col-span-1">
                <div class="border border-line rounded-[2px] bg-white p-5 space-y-3">
                    <h2 class="font-semibold text-ink">Đơn hàng</h2>

                    @foreach ($cart->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-ink-soft">{{ $item->variant->product->name }} ({{ $item->variant->label }}) x{{ $item->quantity }}</span>
                            <span class="text-ink">{{ number_format($item->quantity * $item->variant->price, 0, ',', '.') }}đ</span>
                        </div>
                    @endforeach

                    <div class="border-t border-line pt-3 flex justify-between text-sm">
                        <span class="text-ink-soft">Tạm tính</span>
                        <span class="text-ink">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-soft">Phí vận chuyển</span>
                        <span class="text-ink">{{ number_format($shippingFee, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="border-t border-line pt-3 flex justify-between font-semibold">
                        <span>Tổng cộng</span>
                        <span class="text-accent">{{ number_format($subtotal + $shippingFee, 0, ',', '.') }}đ</span>
                    </div>

                    <button type="submit" class="w-full mt-2 bg-ink hover:bg-ink/90 text-white font-medium py-3 rounded-[2px]">
                        Đặt hàng
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-shop-layout>
