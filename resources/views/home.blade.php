<x-shop-layout title="Trang chủ - Phone Shop">
    <!-- Banner -->
    <section class="bg-ink">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-xs font-bold tracking-[2px] uppercase text-accent-light">Bộ sưu tập mới</p>
                <h1 class="mt-3 font-serif font-medium text-[clamp(38px,5vw,58px)] leading-tight text-white">Điện thoại chính hãng, giá tốt mỗi ngày</h1>
                <p class="mt-4 text-ink-soft">Apple, Samsung, Xiaomi, Oppo và nhiều thương hiệu khác</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-6 bg-accent hover:bg-accent-dark text-white font-medium px-8 py-4 rounded-[2px]">
                    Xem tất cả sản phẩm
                </a>
            </div>
            <div class="aspect-square bg-white/5 border border-white/10 rounded-[2px] flex items-center justify-center bg-[repeating-linear-gradient(45deg,rgba(255,255,255,0.04),rgba(255,255,255,0.04)_10px,transparent_10px,transparent_20px)]">
                <span class="font-mono text-xs text-ink-soft">ảnh sản phẩm nổi bật</span>
            </div>
        </div>
    </section>

    <!-- Danh mục -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h2 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-4">Danh mục</h2>
        <div class="flex flex-wrap gap-3">
            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="px-4 py-2 rounded-[2px] border border-line bg-white text-sm text-ink hover:border-accent hover:text-accent">
                    {{ $category->name }}
                </a>
            @endforeach

            @foreach ($brands as $brand)
                <a href="{{ route('products.index', ['brand' => $brand->slug]) }}" class="px-4 py-2 rounded-[2px] border border-line bg-white text-sm text-ink hover:border-accent hover:text-accent">
                    {{ $brand->name }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- Sản phẩm nổi bật -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-serif text-[28px] font-medium text-ink">Sản phẩm mới nhất</h2>
            <a href="{{ route('products.index') }}" class="text-sm text-accent hover:text-accent-dark">Xem tất cả &rarr;</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
</x-shop-layout>
