@props(['announcement'])

<div
    x-data="{
        show: false,
        key: 'dismissedAnnouncementId',
        init() {
            let dismissedId = null;
            try {
                dismissedId = localStorage.getItem(this.key);
            } catch (e) {}
            this.show = dismissedId !== String({{ $announcement->id }});
        },
        dismiss() {
            this.show = false;
            try {
                localStorage.setItem(this.key, String({{ $announcement->id }}));
            } catch (e) {}
        },
    }"
    x-show="show"
    x-cloak
    x-transition.opacity
    @keydown.escape.window="dismiss()"
    class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-ink/60 backdrop-blur-sm [padding-top:calc(env(safe-area-inset-top)+1rem)] [padding-bottom:calc(env(safe-area-inset-bottom)+1rem)]"
    @click="dismiss()"
    role="dialog"
    aria-modal="true"
    aria-labelledby="announcement-title"
>
    <div
        @click.stop
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="relative w-full max-w-sm bg-white rounded-2xl shadow-lg max-h-full overflow-y-auto"
    >
        <button
            type="button"
            @click="dismiss()"
            aria-label="Đóng thông báo"
            class="absolute top-2.5 right-2.5 w-9 h-9 rounded-full flex items-center justify-center text-ink-soft hover:bg-paper hover:text-ink transition"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="p-6 pt-8">
            <div class="w-11 h-11 rounded-2xl bg-brand/10 text-brand flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6M2 7h20M12 3v4M8 5l1.5 2M16 5l-1.5 2" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7a3 3 0 100-6 3 3 0 000 6z" />
                </svg>
            </div>

            <h2 id="announcement-title" class="font-bold text-lg text-ink pr-6">{{ $announcement->title }}</h2>
            <p class="mt-2 text-sm text-ink-soft leading-relaxed whitespace-pre-line">{{ $announcement->message }}</p>

            @if ($announcement->cta_label && $announcement->cta_url)
                <a
                    href="{{ $announcement->cta_url }}"
                    class="mt-5 inline-flex w-full items-center justify-center px-5 py-3 rounded-2xl bg-brand hover:bg-brand-dark text-white font-semibold text-sm shadow-sm transition"
                >
                    {{ $announcement->cta_label }}
                </a>
            @endif
        </div>
    </div>
</div>
