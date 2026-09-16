<x-shop-layout title="Tra cứu đơn hàng - phuonghihi">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-bold text-3xl text-ink mb-2 text-center">Tra cứu đơn hàng</h1>
        <p class="text-ink-soft mb-8 text-center">Nhập mã đơn hàng và số điện thoại đã dùng khi đặt để xem tình trạng đơn hàng.</p>

        @if (! empty($error))
            <div class="mb-6 bg-red-50 text-red-800 border border-red-200 rounded-xl px-4 py-3 text-sm">{{ $error }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 text-red-800 border border-red-200 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('orders.lookup.show') }}" class="bg-white border border-line rounded-2xl shadow-sm p-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="order_code" value="Mã đơn hàng" />
                <x-text-input id="order_code" name="order_code" class="block mt-1 w-full rounded-xl border-line focus:border-brand focus:ring-brand" placeholder="VD: DH260916ABCDE" :value="old('order_code')" required />
            </div>

            <div>
                <x-input-label for="phone" value="Số điện thoại đặt hàng" />
                <x-text-input id="phone" name="phone" type="tel" inputmode="tel" class="block mt-1 w-full rounded-xl border-line focus:border-brand focus:ring-brand" :value="old('phone')" required />
            </div>

            <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-semibold py-3 rounded-2xl shadow-sm transition">
                Tra cứu
            </button>
        </form>

        @auth
            <p class="mt-6 text-center text-sm text-ink-soft">
                Đã đăng nhập? <a href="{{ route('orders.index') }}" class="font-semibold text-brand hover:text-brand-dark transition">Xem tất cả đơn hàng của tôi</a>
            </p>
        @endauth
    </div>
</x-shop-layout>
