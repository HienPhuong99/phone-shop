@csrf
@isset($productSeries)
    @method('PATCH')
@endisset

<div class="space-y-4 max-w-lg">
    <div>
        <x-input-label for="name" value="Tên dòng sản phẩm" />
        <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $productSeries->name ?? '')" required />
    </div>

    <div>
        <x-input-label for="description" value="Mô tả" />
        <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $productSeries->description ?? '') }}</textarea>
    </div>

    <div>
        <x-input-label for="sort_order" value="Thứ tự hiển thị" />
        <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="block mt-1 w-full" :value="old('sort_order', $productSeries->sort_order ?? '')" placeholder="Để trống = xếp cuối" />
    </div>

    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-md">Lưu</button>
</div>
