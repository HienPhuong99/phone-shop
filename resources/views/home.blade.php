<x-shop-layout title="Trang chủ - phuonghihi">
    <!-- Banner -->
    <section class="bg-paper border-b border-line">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-xs font-bold tracking-[2px] uppercase text-brand">Bộ sưu tập mới</p>
                <h1 class="mt-3 font-extrabold text-[clamp(32px,4.5vw,48px)] leading-tight text-ink">Điện thoại chính hãng, giá tốt mỗi ngày</h1>
                <p class="mt-4 text-ink-soft text-base sm:text-lg">Chuyên iPhone chính hãng — từ dòng mới nhất đến các đời máy giá tốt, bảo hành rõ ràng.</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-6 bg-brand hover:bg-brand-dark text-white rounded-2xl px-8 py-4 font-semibold shadow-sm transition">
                    Xem tất cả sản phẩm
                </a>
            </div>
            @if ($heroProducts->isNotEmpty())
                <x-hero-product-slider :products="$heroProducts" />
            @else
                <div class="aspect-square bg-white border border-line rounded-2xl shadow-sm flex items-center justify-center p-8">
                    <div class="w-full h-full rounded-xl bg-slate-50 border border-dashed border-line flex flex-col items-center justify-center text-center p-6">
                        <svg class="w-16 h-16 text-brand/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                        <span class="text-xs font-semibold text-ink-soft uppercase tracking-wider">Sản phẩm nổi bật</span>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Value propositions -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-line border-l-[3px] border-l-emerald-600 rounded-2xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.5l2 2 4-5" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-ink">Sản phẩm chính hãng</h3>
                </div>
                <p class="text-sm text-ink-soft">100% điện thoại có nguồn gốc rõ ràng, kiểm định chất lượng kỹ lưỡng trước khi đến tay bạn.</p>
            </div>
            <div class="bg-white border border-line border-l-[3px] border-l-amber-600 rounded-2xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v5.2c0 4.4-3 8.3-7 9.3-4-1-7-4.9-7-9.3V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 12l1.8 1.8L14.8 10" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-ink">Bảo hành uy tín</h3>
                </div>
                <p class="text-sm text-ink-soft">Chính sách bảo hành minh bạch, hỗ trợ kỹ thuật tận tâm và đổi trả nhanh chóng.</p>
            </div>
            <div class="bg-white border border-line border-l-[3px] border-l-brand rounded-2xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 3L4 14h6l-1 7 9-11h-6z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-ink">Giao hàng nhanh chóng</h3>
                </div>
                <p class="text-sm text-ink-soft">Đóng gói tiêu chuẩn an toàn, vận chuyển toàn quốc và cho phép đồng kiểm khi nhận hàng.</p>
            </div>
        </div>
    </section>

    <!-- Dòng sản phẩm -->
    @php
        $initialSeriesCount = 6;
    @endphp
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ expanded: false }">
        <h2 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-4">Dòng sản phẩm</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($series as $index => $item)
                <a
                    href="{{ route('products.index', ['series' => $item->slug]) }}"
                    @if ($index >= $initialSeriesCount)
                        x-show="expanded" x-cloak
                    @endif
                    class="block px-4 py-3 rounded-2xl border border-line bg-white shadow-sm hover:border-brand transition"
                >
                    <p class="text-sm font-semibold text-ink">{{ $item->name }}</p>
                    <p class="mt-1 text-xs text-ink-soft line-clamp-2">{{ $item->description }}</p>
                </a>
            @endforeach
        </div>

        @if ($series->count() > $initialSeriesCount)
            <div class="mt-4 text-center">
                <button type="button" @click="expanded = !expanded" class="text-sm font-semibold text-brand hover:text-brand-dark transition">
                    <span x-show="!expanded">Xem thêm dòng sản phẩm</span>
                    <span x-show="expanded" x-cloak>Thu gọn</span>
                </button>
            </div>
        @endif
    </section>

    <!-- Danh mục -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h2 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-4">Danh mục</h2>
        <div class="flex flex-wrap gap-3">
            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="px-4 py-2 rounded-2xl border border-line bg-white text-sm font-medium text-ink shadow-sm hover:border-brand hover:text-brand transition">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- Sản phẩm nổi bật -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-2xl text-ink">Sản phẩm mới nhất</h2>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-brand hover:text-brand-dark transition">Xem tất cả &rarr;</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
</x-shop-layout>
