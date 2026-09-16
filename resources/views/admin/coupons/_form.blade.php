@csrf
@isset($coupon)
    @method('PATCH')
@endisset

<div class="space-y-4 max-w-lg">
    <div>
        <x-input-label for="code" value="Mã giảm giá" />
        <x-text-input id="code" name="code" class="block mt-1 w-full font-mono uppercase" :value="old('code', $coupon->code ?? '')" required />
        <p class="text-xs text-gray-500 mt-1">Chỉ chữ, số và dấu gạch dưới/ngang. Sẽ tự động viết hoa.</p>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="type" value="Loại giảm giá" />
            <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300">
                <option value="percent" {{ old('type', $coupon->type ?? '') === 'percent' ? 'selected' : '' }}>Theo % đơn hàng</option>
                <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>Số tiền cố định</option>
            </select>
        </div>
        <div>
            <x-input-label for="value" value="Giá trị" />
            <x-text-input id="value" name="value" type="number" step="0.01" class="block mt-1 w-full" :value="old('value', $coupon->value ?? '')" required />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="min_order_amount" value="Đơn tối thiểu (không bắt buộc)" />
            <x-text-input id="min_order_amount" name="min_order_amount" type="number" step="1000" class="block mt-1 w-full" :value="old('min_order_amount', $coupon->min_order_amount ?? '')" />
        </div>
        <div>
            <x-input-label for="max_uses" value="Số lượt dùng tối đa (không bắt buộc)" />
            <x-text-input id="max_uses" name="max_uses" type="number" min="1" class="block mt-1 w-full" :value="old('max_uses', $coupon->max_uses ?? '')" />
        </div>
    </div>

    <div>
        <x-input-label for="expires_at" value="Ngày hết hạn (không bắt buộc)" />
        <x-text-input id="expires_at" name="expires_at" type="date" class="block mt-1 w-full" :value="old('expires_at', isset($coupon) ? $coupon->expires_at?->format('Y-m-d') : '')" />
    </div>

    <label class="flex items-center gap-2">
        <input type="checkbox" name="active" value="1" {{ old('active', $coupon->active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
        <span class="text-sm font-medium text-gray-700">Đang bật (khách hàng dùng được)</span>
    </label>

    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-md">Lưu</button>
</div>
