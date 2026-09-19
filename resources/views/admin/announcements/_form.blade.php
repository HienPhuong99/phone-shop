@csrf
@isset($announcement)
    @method('PATCH')
@endisset

<div class="space-y-4 max-w-lg">
    <div>
        <x-input-label for="title" value="Tiêu đề" />
        <x-text-input id="title" name="title" class="block mt-1 w-full" :value="old('title', $announcement->title ?? '')" required />
    </div>

    <div>
        <x-input-label for="message" value="Nội dung" />
        <textarea id="message" name="message" rows="3" class="block mt-1 w-full rounded-md border-gray-300" required>{{ old('message', $announcement->message ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="cta_label" value="Chữ trên nút (không bắt buộc)" />
            <x-text-input id="cta_label" name="cta_label" class="block mt-1 w-full" :value="old('cta_label', $announcement->cta_label ?? '')" placeholder="Xem ngay" />
        </div>
        <div>
            <x-input-label for="cta_url" value="Link khi bấm nút" />
            <x-text-input id="cta_url" name="cta_url" class="block mt-1 w-full" :value="old('cta_url', $announcement->cta_url ?? '')" placeholder="/san-pham" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="starts_at" value="Bắt đầu hiện (để trống = hiện ngay)" />
            <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="block mt-1 w-full" :value="old('starts_at', isset($announcement) ? $announcement->starts_at?->format('Y-m-d\TH:i') : '')" />
        </div>
        <div>
            <x-input-label for="ends_at" value="Hết hiện (để trống = không hết hạn)" />
            <x-text-input id="ends_at" name="ends_at" type="datetime-local" class="block mt-1 w-full" :value="old('ends_at', isset($announcement) ? $announcement->ends_at?->format('Y-m-d\TH:i') : '')" />
        </div>
    </div>

    <label class="flex items-center gap-2">
        <input type="checkbox" name="active" value="1" {{ old('active', $announcement->active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
        <span class="text-sm font-medium text-gray-700">Đang bật</span>
    </label>

    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-md">Lưu</button>
</div>
