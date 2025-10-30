<div {{ $attributes->merge(['class' => 'card']) }} style="display: flex; flex-direction: column;">
    @if(isset($title) || isset($actions))
        <div class="card-header">
            @if(is_string($title ?? null))
                <div class="title-md">{{ $title }}</div>
            @else
                {{ $title ?? '' }}
            @endif
            <div>{{ $actions ?? '' }}</div>
        </div>
    @endif
    <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
        {{ $slot }}
    </div>
    @if(isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>


