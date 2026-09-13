<x-admin-layout title="Thêm sản phẩm - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thêm sản phẩm</h2>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="max-w-2xl space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Tên sản phẩm" />
            <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name')" required />
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <x-input-label for="category_id" value="Danh mục" />
                <select id="category_id" name="category_id" class="block mt-1 w-full rounded-md border-gray-300" required>
                    <option value="">— Chọn —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="brand_id" value="Thương hiệu" />
                <select id="brand_id" name="brand_id" class="block mt-1 w-full rounded-md border-gray-300" required>
                    <option value="">— Chọn —</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="series_id" value="Dòng sản phẩm" />
                <select id="series_id" name="series_id" class="block mt-1 w-full rounded-md border-gray-300" required>
                    <option value="">— Chọn —</option>
                    @foreach ($allSeries as $item)
                        <option value="{{ $item->id }}" {{ old('series_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <x-input-label for="base_price" value="Giá gốc" />
            <x-text-input id="base_price" name="base_price" type="number" step="1000" class="block mt-1 w-full" :value="old('base_price')" required />
        </div>

        <div>
            <x-input-label for="status" value="Trạng thái" />
            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300">
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Đang bán</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <div>
            <x-input-label for="thumbnail" value="Ảnh đại diện" />
            <input type="file" id="thumbnail" name="thumbnail" class="block mt-1 w-full text-sm" accept="image/*">
        </div>

        <div>
            <x-input-label for="description" value="Mô tả" />
            <textarea id="description" name="description" rows="5" class="block mt-1 w-full rounded-md border-gray-300">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-md">Tạo sản phẩm</button>
    </form>
</x-admin-layout>
