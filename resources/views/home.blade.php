<x-shop-layout title="Trang chủ - Phone Shop">
    <!-- Banner -->
    <section class="bg-indigo-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold text-white">Điện thoại chính hãng, giá tốt mỗi ngày</h1>
            <p class="mt-4 text-indigo-100">Apple, Samsung, Xiaomi, Oppo và nhiều thương hiệu khác</p>
            <a href="{{ route('products.index') }}" class="inline-block mt-6 bg-white text-indigo-600 font-medium px-6 py-3 rounded-md hover:bg-indigo-50">
                Xem tất cả sản phẩm
            </a>
        </div>
    </section>

    <!-- Danh mục -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Danh mục</h2>
        <div class="flex flex-wrap gap-3">
            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="px-4 py-2 rounded-full border border-gray-200 bg-white text-sm text-gray-700 hover:border-indigo-400 hover:text-indigo-600">
                    {{ $category->name }}
                </a>
            @endforeach

            @foreach ($brands as $brand)
                <a href="{{ route('products.index', ['brand' => $brand->slug]) }}" class="px-4 py-2 rounded-full border border-gray-200 bg-white text-sm text-gray-700 hover:border-indigo-400 hover:text-indigo-600">
                    {{ $brand->name }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- Sản phẩm nổi bật -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Sản phẩm mới nhất</h2>
            <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:underline">Xem tất cả &rarr;</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
</x-shop-layout>
