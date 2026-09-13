<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin - phuonghihi' }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900">
        <div class="min-h-screen flex">
            <aside class="w-56 bg-gray-900 text-gray-300 flex-shrink-0">
                <div class="px-4 py-4 border-b border-gray-800 flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-icon.svg') }}" alt="" class="h-8 w-8 rounded-[9px]">
                    <span class="font-bold text-lg leading-tight">
                        <span class="text-white">phuong</span><span class="text-[#FF6B4A]">hihi</span><br>
                        <span class="text-[11px] font-medium tracking-wide uppercase text-gray-400">Admin</span>
                    </span>
                </div>
                <nav class="mt-4 space-y-1">
                    @php
                        $links = [
                            ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
                            ['route' => 'admin.products.index', 'label' => 'Sản phẩm'],
                            ['route' => 'admin.categories.index', 'label' => 'Danh mục'],
                            ['route' => 'admin.brands.index', 'label' => 'Thương hiệu'],
                            ['route' => 'admin.product-series.index', 'label' => 'Dòng sản phẩm'],
                            ['route' => 'admin.orders.index', 'label' => 'Đơn hàng'],
                        ];
                    @endphp
                    @foreach ($links as $link)
                        <a href="{{ route($link['route']) }}" class="block px-4 py-2 text-sm {{ request()->routeIs($link['route'].'*') || request()->routeIs(str_replace('.index', '.*', $link['route'])) ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
                <div class="mt-8 px-4">
                    <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-white">&larr; Về cửa hàng</a>
                </div>
            </aside>

            <div class="flex-1 flex flex-col min-w-0">
                <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
                    <h1 class="text-lg font-semibold text-gray-900">{{ $header ?? '' }}</h1>
                    <div class="text-sm text-gray-500">{{ Auth::user()->name }}</div>
                </header>

                @if (session('status'))
                    <div class="mx-6 mt-4 bg-green-50 text-green-800 border border-green-200 rounded-md px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-6 mt-4 bg-red-50 text-red-800 border border-red-200 rounded-md px-4 py-3 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mx-6 mt-4 bg-red-50 text-red-800 border border-red-200 rounded-md px-4 py-3 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
