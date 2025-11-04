@props([
    'title' => 'No data',
    'message' => 'There is nothing to show here yet.',
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    <div class="card-body" style="display:grid;place-items:center;text-align:center;gap:8px;">
        <div class="title-md">{{ $title }}</div>
        <div class="text-muted">{{ $message }}</div>
        {{ $slot ?? '' }}
    </div>
</div>


