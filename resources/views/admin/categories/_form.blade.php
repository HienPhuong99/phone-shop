@csrf
@isset($category)
    @method('PATCH')
@endisset

<div class="space-y-4 max-w-lg">
    <div>
        <x-input-label for="name" value="Tên danh mục" />
        <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $category->name ?? '')" required />
    </div>

    <div>
        <x-input-label for="parent_id" value="Danh mục cha (tuỳ chọn)" />
        <select id="parent_id" name="parent_id" class="block mt-1 w-full rounded-md border-gray-300">
            <option value="">— Không —</option>
            @foreach ($categories as $option)
                <option value="{{ $option->id }}" {{ old('parent_id', $category->parent_id ?? '') == $option->id ? 'selected' : '' }}>
                    {{ $option->name }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-md">Lưu</button>
</div>
