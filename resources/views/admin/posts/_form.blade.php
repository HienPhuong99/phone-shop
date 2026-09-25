@csrf
@isset($post)
    @method('PATCH')
@endisset

@php
    $faqRows = old('faqs', $post->faqs ?? []);
    $faqRows = array_pad(array_values($faqRows ?: []), 3, ['question' => '', 'answer' => '']);
@endphp

<div
    class="grid grid-cols-1 xl:grid-cols-3 gap-6"
    x-data="{
        title: @js(old('title', $post->title ?? '')),
        excerpt: @js(old('excerpt', $post->excerpt ?? '')),
        metaDescription: @js(old('meta_description', $post->meta_description ?? '')),
        body: @js(old('body', $post->body ?? '')),
        get headingCount() { return (this.body.match(/^## /gm) || []).length },
        get linkCount() { return (this.body.match(/\]\(\//g) || []).length },
        get wordCount() { return this.body.split(/\s+/).filter(Boolean).length },
    }"
>
    <div class="xl:col-span-2 space-y-5">
        <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-4">
            <div>
                <x-input-label for="title" value="1. Tiêu đề bài viết" />
                <p class="text-xs text-gray-500 mt-0.5">Viết đúng câu khách sẽ gõ lên Google. Tốt nhất 50–60 ký tự và có chứa từ khoá chính. Ví dụ: <em>Nên mua iPhone nào 2026? Chọn theo nhu cầu và túi tiền</em>.</p>
                <x-text-input id="title" name="title" x-model="title" class="block mt-1.5 w-full" :value="old('title', $post->title ?? '')" required />
                <p class="text-xs mt-1" :class="title.length > 60 ? 'text-amber-600' : 'text-gray-400'">
                    <span x-text="title.length"></span>/60 ký tự
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="focus_keyword" value="2. Từ khoá chính" />
                    <p class="text-xs text-gray-500 mt-0.5">Cụm khách gõ khi tìm. Gõ đúng như họ gõ, không viết hoa, không dấu câu.</p>
                    <x-text-input id="focus_keyword" name="focus_keyword" class="block mt-1.5 w-full" :value="old('focus_keyword', $post->focus_keyword ?? '')" placeholder="nên mua iphone nào" />
                </div>

                <div>
                    <x-input-label for="topic" value="3. Chủ đề" />
                    <p class="text-xs text-gray-500 mt-0.5">Quyết định bài nằm ở tab nào ngoài trang tin tức.</p>
                    <select id="topic" name="topic" class="block mt-1.5 w-full rounded-md border-gray-300 text-sm" required>
                        @foreach (App\Models\Post::TOPICS as $value => $label)
                            <option value="{{ $value }}" @selected(old('topic', $post->topic ?? 'tu-van') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <x-input-label for="excerpt" value="4. Tóm tắt" />
                <p class="text-xs text-gray-500 mt-0.5">Một đến hai câu trả lời thẳng câu hỏi ở tiêu đề. Đây là đoạn hiện trên thẻ bài viết và dưới tiêu đề trên Google.</p>
                <textarea id="excerpt" name="excerpt" x-model="excerpt" rows="3" class="block mt-1.5 w-full rounded-md border-gray-300 text-sm" required>{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                <p class="text-xs mt-1" :class="excerpt.length > 300 ? 'text-red-600' : 'text-gray-400'">
                    <span x-text="excerpt.length"></span>/300 ký tự
                </p>
            </div>

            <div>
                <x-input-label for="thumbnail" value="5. Ảnh đại diện (không bắt buộc)" />
                <p class="text-xs text-gray-500 mt-0.5">Ảnh ngang, tối thiểu 1200×675. Không có ảnh bài vẫn đăng được.</p>
                <input id="thumbnail" name="thumbnail" type="file" accept="image/*" class="block mt-1.5 w-full text-sm text-gray-600">
                @if ($post->thumbnail ?? null)
                    <img src="{{ $post->thumbnail_thumb ?? $post->thumbnail }}" alt="" class="mt-2 h-24 rounded-md border border-gray-200">
                @endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-3">
            <div>
                <x-input-label for="body" value="6. Nội dung bài viết" />
                <p class="text-xs text-gray-500 mt-0.5">Viết như đang trả lời khách đứng trước mặt. Không cần biết HTML — chỉ cần bốn ký hiệu dưới đây.</p>
            </div>

            <div class="rounded-md bg-gray-50 border border-gray-200 p-3 text-xs text-gray-600 space-y-1 font-mono">
                <p><span class="text-indigo-600">## </span>Tiêu đề mục &rarr; mục lớn, tự động vào mục lục</p>
                <p><span class="text-indigo-600">### </span>Tiêu đề nhỏ &rarr; ý nhỏ bên trong mục</p>
                <p><span class="text-indigo-600">- </span>Gạch đầu dòng &rarr; danh sách</p>
                <p><span class="text-indigo-600">&gt; </span>Ghi chú &rarr; khung nhấn mạnh</p>
                <p><span class="text-indigo-600">**chữ đậm**</span> &nbsp; <span class="text-indigo-600">[chữ hiển thị](/san-pham)</span> &rarr; link sang trang khác</p>
            </div>

            <textarea id="body" name="body" x-model="body" rows="24" required
                      class="block w-full rounded-md border-gray-300 text-sm font-mono leading-relaxed"
                      placeholder="Đoạn đầu: trả lời thẳng câu hỏi ở tiêu đề trong 2-3 câu.&#10;## Mục 1 viết dưới dạng câu hỏi&#10;Nội dung mục 1...&#10;- Ý ngắn thứ nhất&#10;- Ý ngắn thứ hai&#10;## Mục 2&#10;Nội dung mục 2, có [link sang trang sản phẩm](/san-pham)."
            >{{ old('body', $post->body ?? '') }}</textarea>

            <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                <span>Số chữ: <strong x-text="wordCount"></strong> <span class="text-gray-400">(nên từ 600)</span></span>
                <span>Mục lớn (##): <strong x-text="headingCount"></strong> <span class="text-gray-400">(nên từ 3)</span></span>
                <span>Link nội bộ: <strong x-text="linkCount"></strong> <span class="text-gray-400">(nên từ 2)</span></span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-4">
            <div>
                <x-input-label value="7. Câu hỏi thường gặp" />
                <p class="text-xs text-gray-500 mt-0.5">Ba câu khách hay hỏi nhất về chủ đề này, trả lời gọn trong 2–3 câu. Phần này là thứ Google và các trợ lý AI trích dẫn nhiều nhất — không bỏ trống.</p>
            </div>

            @foreach ($faqRows as $index => $faq)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <x-text-input name="faqs[{{ $index }}][question]" class="block w-full text-sm" :value="$faq['question'] ?? ''" placeholder="Câu hỏi {{ $index + 1 }}" />
                    <textarea name="faqs[{{ $index }}][answer]" rows="2" class="sm:col-span-2 block w-full rounded-md border-gray-300 text-sm" placeholder="Câu trả lời {{ $index + 1 }}">{{ $faq['answer'] ?? '' }}</textarea>
                </div>
            @endforeach
        </div>

        <details class="bg-white border border-gray-200 rounded-lg p-5" @if ($errors->hasAny(['slug', 'meta_title', 'meta_description'])) open @endif>
            <summary class="text-sm font-medium text-gray-700 cursor-pointer">8. Tuỳ chọn nâng cao (để trống cũng được)</summary>

            <div class="mt-4 space-y-4">
                <div>
                    <x-input-label for="slug" value="Đường dẫn" />
                    <p class="text-xs text-gray-500 mt-0.5">Để trống thì hệ thống tự tạo từ tiêu đề. Đã đăng rồi thì đừng sửa, đổi đường dẫn là mất thứ hạng đã có.</p>
                    <x-text-input id="slug" name="slug" class="block mt-1.5 w-full" :value="old('slug', $post->slug ?? '')" placeholder="nen-mua-iphone-nao-2026" />
                </div>

                <div>
                    <x-input-label for="meta_title" value="Tiêu đề hiện trên Google" />
                    <p class="text-xs text-gray-500 mt-0.5">Để trống thì dùng luôn tiêu đề bài. Chỉ điền khi tiêu đề bài quá dài.</p>
                    <x-text-input id="meta_title" name="meta_title" class="block mt-1.5 w-full" :value="old('meta_title', $post->meta_title ?? '')" />
                </div>

                <div>
                    <x-input-label for="meta_description" value="Mô tả hiện trên Google" />
                    <p class="text-xs text-gray-500 mt-0.5">Để trống thì dùng phần tóm tắt. Tối đa 160 ký tự, dài hơn Google sẽ cắt.</p>
                    <textarea id="meta_description" name="meta_description" x-model="metaDescription" rows="2" class="block mt-1.5 w-full rounded-md border-gray-300 text-sm">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                    <p class="text-xs mt-1" :class="metaDescription.length > 160 ? 'text-red-600' : 'text-gray-400'">
                        <span x-text="metaDescription.length"></span>/160 ký tự
                    </p>
                </div>
            </div>
        </details>
    </div>

    <div class="space-y-5">
        <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-4">
            <div>
                <x-input-label for="status" value="9. Trạng thái" />
                <select id="status" name="status" class="block mt-1.5 w-full rounded-md border-gray-300 text-sm">
                    <option value="draft" @selected(old('status', $post->status ?? 'draft') === 'draft')>Bản nháp (chưa ai thấy)</option>
                    <option value="published" @selected(old('status', $post->status ?? 'draft') === 'published')>Đăng</option>
                </select>
            </div>

            <div>
                <x-input-label for="published_at" value="Thời gian đăng" />
                <p class="text-xs text-gray-500 mt-0.5">Để trống là đăng ngay. Đặt ngày giờ tương lai để hẹn giờ.</p>
                <x-text-input id="published_at" name="published_at" type="datetime-local" class="block mt-1.5 w-full"
                              :value="old('published_at', isset($post) ? $post->published_at?->format('Y-m-d\TH:i') : '')" />
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2.5 rounded-md">Lưu bài viết</button>
        </div>

        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-5">
            <p class="text-sm font-semibold text-indigo-900">Kiểm tra trước khi bấm Lưu</p>
            <ul class="mt-3 space-y-2 text-xs text-indigo-900/80 list-disc list-inside leading-relaxed">
                <li>Từ khoá chính có nằm trong tiêu đề không?</li>
                <li>Đoạn đầu có trả lời thẳng câu hỏi ở tiêu đề trong 2–3 câu không?</li>
                <li>Có ít nhất 3 mục <span class="font-mono">##</span>, mỗi mục là một câu hỏi thật của khách?</li>
                <li>Có ít nhất 2 link sang trang sản phẩm, trả góp, thu cũ hoặc chính sách?</li>
                <li>Có con số cụ thể (giá, dung lượng, phần trăm pin, số ngày bảo hành) thay vì lời khen chung chung?</li>
                <li>Đã điền đủ 3 câu hỏi thường gặp?</li>
                <li>Ảnh đại diện đã tải lên?</li>
            </ul>
        </div>
    </div>
</div>
