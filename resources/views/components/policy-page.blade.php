@props(['title'])

<x-shop-layout :title="$title.' - Phone Shop'">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <a href="{{ route('pages.policies') }}" class="hover:text-brand transition">Chính sách</a> /
            <span class="text-ink">{{ $title }}</span>
        </nav>

        <h1 class="font-bold text-3xl text-ink mb-6">{{ $title }}</h1>

        <div class="border border-line rounded-2xl bg-white shadow-sm p-6 sm:p-8 space-y-6 text-sm text-ink-soft leading-relaxed [&_h2]:font-bold [&_h2]:text-base [&_h2]:text-ink [&_h2]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1">
            {{ $slot }}
        </div>

        <p class="mt-6 text-sm text-ink-soft">Có thắc mắc? <a href="{{ route('pages.contact') }}" class="text-brand hover:text-brand-dark font-medium">Liên hệ với chúng tôi</a>.</p>
    </div>
</x-shop-layout>
