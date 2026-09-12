@csrf
@isset($brand)
    @method('PATCH')
@endisset

<div class="space-y-4 max-w-lg">
    <div>
        <x-input-label for="name" value="Tên thương hiệu" />
        <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $brand->name ?? '')" required />
    </div>

    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-md">Lưu</button>
</div>
