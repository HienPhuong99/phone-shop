@props(['name'])

@php
    $paths = [
        'display' => '<rect x="5" y="2.5" width="14" height="19" rx="2.5"/><path d="M10 5.5h4"/>',
        'chip' => '<rect x="7" y="7" width="10" height="10" rx="1.5"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4"/>',
        'ram' => '<rect x="3" y="8" width="18" height="9" rx="1.5"/><path d="M7 17v3M12 17v3M17 17v3"/>',
        'storage' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'camera' => '<path d="M3 8.5A2 2 0 015 6.5h2.2l1.3-2h7l1.3 2H19a2 2 0 012 2V18a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><circle cx="12" cy="13" r="3.4"/>',
        'battery' => '<rect x="2.5" y="7" width="16" height="10" rx="2"/><path d="M21.5 11v2"/><path d="M6 11v2M9.5 11v2M13 11v2"/>',
        'shield' => '<path d="M12 2.5l7.5 3.4v5.3c0 4.4-3.1 8.4-7.5 9.3-4.4-.9-7.5-4.9-7.5-9.3V5.9z"/>',
        'signal' => '<path d="M12 19.5v.5"/><path d="M8.2 15.8a5.4 5.4 0 017.6 0"/><path d="M5 12.6a9.9 9.9 0 0114 0"/><path d="M2.2 9.4a13.9 13.9 0 0119.6 0"/>',
        'dots' => '<circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $paths[$name] ?? $paths['dots'] !!}
</svg>
