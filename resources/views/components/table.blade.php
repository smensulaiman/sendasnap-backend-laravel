@props([
    'headers' => []
])

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="table">
            @if(!empty($headers))
                <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
                </thead>
            @endif
            <tbody>
            {{ $slot }}
            </tbody>
        </table>
    </div>
    @isset($footer)
        <div class="card-footer">{{ $footer }}</div>
    @endisset
</div>


