<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'phuonghihi') }}</title>

        @if ($description)
            <meta name="description" content="{{ $description }}">
        @endif

        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $title ?? config('app.name', 'phuonghihi') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @if ($description)
            <meta property="og:description" content="{{ $description }}">
        @endif
        @if ($ogImage)
            <meta property="og:image" content="{{ $ogImage }}">
        @endif

        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{ $head ?? '' }}
    </head>
    <body class="font-sans antialiased bg-paper text-ink [padding-left:env(safe-area-inset-left)] [padding-right:env(safe-area-inset-right)]">
        <div class="min-h-screen flex flex-col pb-16 sm:pb-0" x-data="{ accountSheetOpen: false }">
            <header class="bg-brand [padding-top:env(safe-area-inset-top)]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4 sm:gap-6 h-[76px]">
                        <a href="{{ route('home') }}" class="shrink-0 font-extrabold text-xl tracking-tight flex items-center gap-2.5">
                            <img src="{{ asset('images/logo-icon.svg') }}" alt="" class="h-9 w-9 rounded-[11px] shadow-sm">
                            <span class="hidden sm:inline"><span class="text-white">phuong</span><span class="text-[#FF6B4A]">hihi</span></span>
                        </a>

                        <x-search-bar />

                        <nav class="hidden lg:flex lg:space-x-8 shrink-0">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 text-sm font-semibold border-b-2 {{ request()->routeIs('home') ? 'text-white border-white' : 'text-white/70 hover:text-white border-transparent' }}">
                                Trang chủ
                            </a>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-semibold border-b-2 {{ request()->routeIs('products.*') ? 'text-white border-white' : 'text-white/70 hover:text-white border-transparent' }}">
                                Sản phẩm
                            </a>
                        </nav>

                        <div class="flex items-center gap-4 shrink-0">
                            <a href="{{ route('cart.index') }}" class="hidden sm:block relative text-white/80 hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.907-4.925 2.29-7.68l.062-.469a1.125 1.125 0 00-1.115-1.276H6.106M7.5 14.25L5.106 5.272M7.5 14.25L6.6 20.4A.75.75 0 007.35 21h9.3m-7.5-1.5h7.5m-7.5 0a.75.75 0 100 1.5.75.75 0 000-1.5zm7.5 0a.75.75 0 100 1.5.75.75 0 000-1.5z" />
                                </svg>
                                @if (($cartItemCount ?? 0) > 0)
                                    <span class="absolute -top-2 -right-2 bg-white text-brand text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center shadow-sm">{{ $cartItemCount }}</span>
                                @endif
                            </a>

                            @auth
                                <div class="hidden sm:block">
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="inline-flex items-center px-3 py-2 text-sm leading-4 font-semibold rounded-xl text-white/90 hover:text-white bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150">
                                                {{ Auth::user()->name }}
                                                <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link :href="route('dashboard')">Dashboard</x-dropdown-link>
                                            <x-dropdown-link :href="route('orders.index')">Đơn hàng của tôi</x-dropdown-link>
                                            <x-dropdown-link :href="route('profile.edit')">Hồ sơ</x-dropdown-link>
                                            @if (Auth::user()->is_admin)
                                                <x-dropdown-link :href="route('admin.dashboard')">Quản trị</x-dropdown-link>
                                            @endif
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                                    Đăng xuất
                                                </x-dropdown-link>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            @else
                                <div class="hidden sm:flex sm:items-center sm:gap-4">
                                    <a href="{{ route('login') }}" class="text-sm font-medium text-white/80 hover:text-white transition">Đăng nhập</a>
                                    <a href="{{ route('register') }}" class="text-sm font-semibold text-brand bg-white hover:bg-white/90 px-4 py-2 rounded-xl shadow-sm transition">Đăng ký</a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            @if (session('status'))
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-50 text-green-800 border border-green-200 rounded-xl px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-50 text-red-800 border border-red-200 rounded-xl px-4 py-3 text-sm">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="bg-white border-t border-line mt-12 [padding-bottom:env(safe-area-inset-bottom)]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                    <div class="flex flex-wrap justify-center gap-x-8 gap-y-2 text-sm font-medium text-ink-soft">
                        <a href="{{ route('pages.services') }}" class="hover:text-brand transition">Dịch vụ</a>
                        <a href="{{ route('pages.policies') }}" class="hover:text-brand transition">Chính sách</a>
                        <a href="{{ route('pages.about') }}" class="hover:text-brand transition">Giới thiệu</a>
                        <a href="{{ route('pages.contact') }}" class="hover:text-brand transition">Liên hệ</a>
                    </div>
                    <div class="mt-6 text-sm text-ink-soft text-center">
                        &copy; {{ date('Y') }} phuonghihi.
                    </div>
                </div>
            </footer>

            <!-- Bottom tab bar (mobile) -->
            @unless ($hideBottomNav)
            <nav class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-line [padding-bottom:env(safe-area-inset-bottom)]">
                <div class="grid grid-cols-4">
                    <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] py-2 {{ request()->routeIs('home') ? 'text-brand' : 'text-ink-soft' }}">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                        </svg>
                        <span class="text-[11px] font-medium">Trang chủ</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] py-2 {{ request()->routeIs('products.*') ? 'text-brand' : 'text-ink-soft' }}">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        <span class="text-[11px] font-medium">Sản phẩm</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="relative flex flex-col items-center justify-center gap-0.5 min-h-[56px] py-2 {{ request()->routeIs('cart.*') ? 'text-brand' : 'text-ink-soft' }}">
                        <span class="relative">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.907-4.925 2.29-7.68l.062-.469a1.125 1.125 0 00-1.115-1.276H6.106M7.5 14.25L5.106 5.272M7.5 14.25L6.6 20.4A.75.75 0 007.35 21h9.3m-7.5-1.5h7.5m-7.5 0a.75.75 0 100 1.5.75.75 0 000-1.5zm7.5 0a.75.75 0 100 1.5.75.75 0 000-1.5z" />
                            </svg>
                            @if (($cartItemCount ?? 0) > 0)
                                <span class="absolute -top-1.5 -right-1.5 bg-brand text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center">{{ $cartItemCount }}</span>
                            @endif
                        </span>
                        <span class="text-[11px] font-medium">Giỏ hàng</span>
                    </a>
                    <button type="button" @click="accountSheetOpen = !accountSheetOpen" :class="accountSheetOpen ? 'text-brand' : 'text-ink-soft'" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] py-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span class="text-[11px] font-medium">Tài khoản</span>
                    </button>
                </div>
            </nav>
            @endunless

            <!-- Account bottom sheet (mobile) -->
            <div
                x-show="accountSheetOpen"
                @click="accountSheetOpen = false"
                class="sm:hidden fixed inset-0 z-40 bg-ink/40"
                style="display: none;"
            ></div>
            <div
                x-show="accountSheetOpen"
                class="sm:hidden fixed bottom-0 inset-x-0 z-50 bg-white rounded-t-2xl shadow-lg [padding-bottom:env(safe-area-inset-bottom)]"
                style="display: none;"
            >
                <div class="px-4 pt-3 pb-2">
                    <div class="mx-auto h-1 w-10 rounded-full bg-line"></div>
                </div>

                @auth
                    <div class="px-4 pb-2">
                        <p class="text-sm font-semibold text-ink">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-ink-soft">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="px-2 pb-2 space-y-0.5">
                        <a href="{{ route('dashboard') }}" class="block px-3 py-3 rounded-xl text-sm font-medium text-ink hover:bg-paper">Dashboard</a>
                        <a href="{{ route('orders.index') }}" class="block px-3 py-3 rounded-xl text-sm font-medium text-ink hover:bg-paper">Đơn hàng của tôi</a>
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-3 rounded-xl text-sm font-medium text-ink hover:bg-paper">Hồ sơ</a>
                        @if (Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-3 rounded-xl text-sm font-medium text-ink hover:bg-paper">Quản trị</a>
                        @endif
                    </div>
                @else
                    <div class="px-2 pb-2 space-y-0.5">
                        <a href="{{ route('login') }}" class="block px-3 py-3 rounded-xl text-sm font-medium text-ink hover:bg-paper">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="block px-3 py-3 rounded-xl text-sm font-medium text-ink hover:bg-paper">Đăng ký</a>
                    </div>
                @endauth

                <div class="border-t border-line px-2 py-2 space-y-0.5">
                    <a href="{{ route('pages.services') }}" class="block px-3 py-3 rounded-xl text-sm text-ink-soft hover:bg-paper">Dịch vụ</a>
                    <a href="{{ route('pages.policies') }}" class="block px-3 py-3 rounded-xl text-sm text-ink-soft hover:bg-paper">Chính sách</a>
                    <a href="{{ route('pages.about') }}" class="block px-3 py-3 rounded-xl text-sm text-ink-soft hover:bg-paper">Giới thiệu</a>
                    <a href="{{ route('pages.contact') }}" class="block px-3 py-3 rounded-xl text-sm text-ink-soft hover:bg-paper">Liên hệ</a>
                </div>

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-line px-2 py-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50">Đăng xuất</button>
                    </form>
                @endauth
            </div>
        </div>
    </body>
</html>
