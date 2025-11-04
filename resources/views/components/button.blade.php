@props([
    'variant' => 'primary',
    'size' => 'default',
    'type' => 'button',
    'href' => null
])

@php
    $classes = 'btn btn-' . $variant;
    if ($size === 'sm') {
        $classes .= ' btn-sm';
    } elseif ($size === 'icon') {
        $classes .= ' btn-icon';
    }
    $tag = $href ? 'a' : 'button';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif


