<x-shop-layout title="Sản phẩm yêu thích - phuonghihi" :noindex="true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-4">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <span class="text-ink">Yêu thích</span>
        </nav>

        <h1 class="font-bold text-3xl text-ink mb-6">Sản phẩm yêu thích</h1>

        @if ($products->isEmpty())
            <div class="bg-white border border-line rounded-2xl shadow-sm p-10 text-center">
                <p class="text-ink-soft">Bạn chưa lưu sản phẩm nào vào danh sách yêu thích.</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-4 px-6 py-2.5 rounded-2xl bg-brand hover:bg-brand-dark text-white text-sm font-semibold shadow-sm transition">Khám phá sản phẩm</a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div>
                        <x-product-card :product="$product" />
                        <form method="POST" action="{{ route('wishlist.destroy', $product) }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center text-xs font-semibold text-ink-soft hover:text-red-600 transition py-1">Bỏ khỏi yêu thích</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-shop-layout>
