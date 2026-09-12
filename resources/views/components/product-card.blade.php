@props(['product'])

<a href="{{ route('products.show', $product->slug) }}" class="group block bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition">
    <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
        @if ($product->thumbnail)
            <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition" />
        @else
            <span class="text-gray-300 text-sm">Không có ảnh</span>
        @endif
    </div>
    <div class="p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">{{ $product->brand->name }}</p>
        <h3 class="mt-1 text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h3>
        <p class="mt-2 text-indigo-600 font-semibold">{{ number_format($product->base_price, 0, ',', '.') }}đ</p>
    </div>
</a>
