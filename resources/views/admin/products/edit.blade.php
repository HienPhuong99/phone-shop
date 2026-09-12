<x-admin-layout title="Sửa sản phẩm - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Sửa sản phẩm: {{ $product->name }}</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Thông tin sản phẩm -->
        <section class="bg-white border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 mb-4">Thông tin</h3>

            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="name" value="Tên sản phẩm" />
                    <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $product->name)" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="category_id" value="Danh mục" />
                        <select id="category_id" name="category_id" class="block mt-1 w-full rounded-md border-gray-300" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="brand_id" value="Thương hiệu" />
                        <select id="brand_id" name="brand_id" class="block mt-1 w-full rounded-md border-gray-300" required>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <x-input-label for="base_price" value="Giá gốc" />
                    <x-text-input id="base_price" name="base_price" type="number" step="1000" class="block mt-1 w-full" :value="old('base_price', $product->base_price)" required />
                </div>

                <div>
                    <x-input-label for="status" value="Trạng thái" />
                    <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300">
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Đang bán</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                </div>

                @if ($product->thumbnail)
                    <img src="{{ $product->thumbnail }}" class="h-24 w-24 object-cover rounded-md border border-gray-200">
                @endif

                <div>
                    <x-input-label for="thumbnail" value="Đổi ảnh đại diện" />
                    <input type="file" id="thumbnail" name="thumbnail" class="block mt-1 w-full text-sm" accept="image/*">
                </div>

                <div>
                    <x-input-label for="description" value="Mô tả" />
                    <textarea id="description" name="description" rows="5" class="block mt-1 w-full rounded-md border-gray-300">{{ old('description', $product->description) }}</textarea>
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-md">Lưu thay đổi</button>
            </form>
        </section>

        <div class="space-y-8">
            <!-- Biến thể -->
            <section class="bg-white border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-4">Biến thể</h3>

                <div class="space-y-2 mb-4">
                    @foreach ($product->variants as $variant)
                        <form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}" class="flex flex-wrap items-center gap-2 text-sm border-b border-gray-100 pb-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="color" value="{{ $variant->color }}" class="w-20 rounded-md border-gray-300 text-xs" placeholder="Màu">
                            <input type="text" name="storage" value="{{ $variant->storage }}" class="w-20 rounded-md border-gray-300 text-xs" placeholder="Dung lượng">
                            <input type="number" name="price" value="{{ $variant->price }}" class="w-24 rounded-md border-gray-300 text-xs" placeholder="Giá">
                            <input type="text" name="sku" value="{{ $variant->sku }}" class="w-24 rounded-md border-gray-300 text-xs" placeholder="SKU">
                            <input type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" class="w-16 rounded-md border-gray-300 text-xs" placeholder="Tồn kho">
                            <button type="submit" class="text-indigo-600 hover:underline text-xs">Lưu</button>
                        </form>
                    @endforeach
                </div>

                @foreach ($product->variants as $variant)
                    <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" class="inline" onsubmit="return confirm('Xoá biến thể {{ $variant->color }} - {{ $variant->storage }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:underline">Xoá {{ $variant->color }} - {{ $variant->storage }}</button>
                    </form>
                @endforeach

                <div class="border-t border-gray-100 mt-4 pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Thêm biến thể mới</p>
                    <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" class="flex flex-wrap items-center gap-2 text-sm">
                        @csrf
                        <input type="text" name="color" placeholder="Màu" class="w-20 rounded-md border-gray-300 text-xs" required>
                        <input type="text" name="storage" placeholder="Dung lượng" class="w-20 rounded-md border-gray-300 text-xs" required>
                        <input type="number" name="price" placeholder="Giá" class="w-24 rounded-md border-gray-300 text-xs" required>
                        <input type="text" name="sku" placeholder="SKU" class="w-24 rounded-md border-gray-300 text-xs" required>
                        <input type="number" name="stock_quantity" placeholder="Tồn kho" class="w-16 rounded-md border-gray-300 text-xs" required>
                        <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-md text-xs">Thêm</button>
                    </form>
                </div>
            </section>

            <!-- Ảnh -->
            <section class="bg-white border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-4">Ảnh sản phẩm</h3>

                <div class="grid grid-cols-4 gap-2 mb-4">
                    @foreach ($product->images as $image)
                        <div class="relative">
                            <img src="{{ $image->url }}" class="aspect-square object-cover rounded-md border border-gray-200">
                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" class="absolute top-1 right-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-white/90 text-red-500 text-xs rounded-full w-5 h-5">×</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="file" name="image" accept="image/*" required class="text-sm">
                    <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-md text-xs">Thêm ảnh</button>
                </form>
            </section>
        </div>
    </div>
</x-admin-layout>
