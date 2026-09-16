@php
    $firstAddress = $addresses->first();
@endphp

<x-shop-layout title="Thanh toán - phuonghihi">
    <div
        class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="{
            shippingFees: {{ Illuminate\Support\Js::from($shippingFees) }},
            savedAddresses: {{ Illuminate\Support\Js::from($addresses->map(fn ($a) => ['id' => $a->id, 'province' => $a->province])) }},
            selectedAddressId: {{ $firstAddress?->id ?? 'null' }},
            newProvince: '',
            subtotal: {{ $subtotal }},
            discount: {{ $discount }},
            get shippingFee() {
                let province = this.newProvince;
                if (this.selectedAddressId) {
                    const addr = this.savedAddresses.find((a) => a.id === this.selectedAddressId);
                    province = addr?.province ?? '';
                }
                return this.shippingFees[province] ?? Math.max(...Object.values(this.shippingFees));
            },
            get total() {
                return this.subtotal - this.discount + this.shippingFee;
            },
        }"
    >
        <h1 class="font-bold text-3xl text-ink mb-6">Thanh toán</h1>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 text-red-800 border border-red-200 rounded-xl px-4 py-3 text-sm">
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
                <section class="border border-line rounded-2xl bg-white shadow-sm p-5">
                    <h2 class="font-semibold text-ink mb-4">Địa chỉ giao hàng</h2>

                    @if ($addresses->isNotEmpty())
                        <div class="space-y-2 mb-4">
                            @foreach ($addresses as $address)
                                <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer transition {{ $loop->first ? 'border-brand border-2 bg-brand/5' : 'border-line' }}">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" x-model.number="selectedAddressId" {{ $loop->first ? 'checked' : '' }} class="mt-1 text-brand focus:ring-brand">
                                    <span class="text-sm">
                                        <span class="font-medium text-ink">{{ $address->recipient_name }}</span> — {{ $address->phone }}<br>
                                        <span class="text-ink-soft">{{ $address->address_line }}{{ $address->province ? ', '.$address->province : '' }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <details {{ $addresses->isEmpty() ? 'open' : '' }}>
                        <summary class="text-sm text-brand hover:text-brand-dark font-medium cursor-pointer" @click="selectedAddressId = null">+ Thêm địa chỉ mới</summary>
                        <div class="mt-3 space-y-3">
                            <div>
                                <x-input-label for="recipient_name" value="Họ tên người nhận" />
                                <x-text-input id="recipient_name" name="recipient_name" autocomplete="name" @focus="selectedAddressId = null" class="block mt-1 w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand" :value="old('recipient_name')" />
                            </div>
                            <div>
                                <x-input-label for="phone" value="Số điện thoại" />
                                <x-text-input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" @focus="selectedAddressId = null" class="block mt-1 w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand" :value="old('phone')" />
                            </div>
                            <div>
                                <x-input-label for="address_line" value="Địa chỉ (số nhà, đường, phường/xã)" />
                                <x-text-input id="address_line" name="address_line" autocomplete="street-address" @focus="selectedAddressId = null" class="block mt-1 w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand" :value="old('address_line')" />
                            </div>
                            <div>
                                <x-input-label for="province" value="Tỉnh / Thành phố" />
                                <select id="province" name="province" x-model="newProvince" @change="selectedAddressId = null" class="block mt-1 w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                                    <option value="">— Chọn tỉnh/thành —</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province }}" {{ old('province') === $province ? 'selected' : '' }}>{{ $province }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-ink-soft mt-1">Phí vận chuyển được tính theo tỉnh/thành bạn chọn.</p>
                            </div>
                        </div>
                    </details>
                </section>

                <section class="border border-line rounded-2xl bg-white shadow-sm p-5">
                    <h2 class="font-semibold text-ink mb-4">Phương thức thanh toán</h2>
                    <label class="flex items-center gap-3 p-3 border border-brand border-2 bg-brand/5 rounded-xl cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" checked class="text-brand focus:ring-brand">
                        <span class="text-sm font-medium text-ink">Thanh toán khi nhận hàng (COD)</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-line rounded-xl mt-2 cursor-pointer hover:border-brand/50 transition">
                        <input type="radio" name="payment_method" value="vnpay" class="text-brand focus:ring-brand">
                        <span class="text-sm font-medium text-ink">Thanh toán qua VNPay</span>
                    </label>
                </section>
            </div>

            <div class="lg:col-span-1">
                <div class="border border-line rounded-2xl bg-white shadow-sm p-5 space-y-3">
                    <h2 class="font-semibold text-ink">Đơn hàng</h2>

                    @foreach ($cart->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-ink-soft">{{ $item->variant->product->name }} ({{ $item->variant->label }}) x{{ $item->quantity }}</span>
                            <span class="text-ink font-medium">{{ number_format($item->quantity * $item->variant->price, 0, ',', '.') }}đ</span>
                        </div>
                    @endforeach

                    <div class="border-t border-line pt-3 flex justify-between text-sm">
                        <span class="text-ink-soft">Tạm tính</span>
                        <span class="text-ink">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    @if ($coupon)
                        <div class="flex justify-between text-sm">
                            <span class="text-ink-soft">Giảm giá ({{ $coupon->code }})</span>
                            <span class="text-emerald-600">−{{ number_format($discount, 0, ',', '.') }}đ</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-soft">Phí vận chuyển</span>
                        <span class="text-ink" x-text="new Intl.NumberFormat('vi-VN').format(shippingFee) + 'đ'"></span>
                    </div>
                    <div class="border-t border-line pt-3 flex justify-between font-semibold">
                        <span>Tổng cộng</span>
                        <span class="text-brand font-extrabold text-xl" x-text="new Intl.NumberFormat('vi-VN').format(total) + 'đ'"></span>
                    </div>

                    <button type="submit" class="w-full mt-2 bg-brand hover:bg-brand-dark text-white font-semibold py-3 rounded-2xl shadow-sm transition">
                        Đặt hàng
                    </button>

                    @guest
                        <p class="text-xs text-ink-soft text-center pt-1">
                            Đặt hàng không cần tài khoản. Đã có tài khoản?
                            <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-brand-dark transition">Đăng nhập</a>
                            để lưu địa chỉ và xem lại đơn hàng dễ dàng hơn.
                        </p>
                    @endguest
                </div>
            </div>
        </form>
    </div>
</x-shop-layout>
