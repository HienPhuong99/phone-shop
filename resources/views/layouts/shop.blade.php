<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Phone Shop') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            <header class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">
                            Phone Shop
                        </a>

                        <nav class="hidden sm:flex sm:space-x-8">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('home') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                Trang chủ
                            </a>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('products.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                Sản phẩm
                            </a>
                        </nav>

                        <div class="flex items-center gap-4">
                            @auth
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            {{ Auth::user()->name }}
                                            <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('dashboard')">Dashboard</x-dropdown-link>
                                        <x-dropdown-link :href="route('profile.edit')">Hồ sơ</x-dropdown-link>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                                Đăng xuất
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Đăng nhập</a>
                                <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-md">Đăng ký</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            @if (session('status'))
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-50 text-green-800 border border-green-200 rounded-md px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="bg-white border-t border-gray-100 mt-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-500 text-center">
                    &copy; {{ date('Y') }} Phone Shop — dự án luyện tập.
                </div>
            </footer>
        </div>
    </body>
</html>
