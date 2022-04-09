@component('mail::message')
# Exception

Detailes :-

<ul>
    @foreach ($exception as $key => $value)
        @if ($key != "Trace")
            <li>{{ $key }} : {{ $value }}</li>
        @else
            <li>{{ $key }} :
                @foreach (Str::of($value)->split('(#[0-9]*)') as $row)
                    @if ($row != null)
                        <p style="background-color: #eeeef5; padding: 2px 5px; border-radius: 8px; overflow:auto;">{{ $loop->iteration - 1 }} - {{ $row }}</p>
                    @endif
                @endforeach
            </li>
        @endif
    @endforeach
</ul>

{{-- @component('mail::button', ['url' => "https:://loc"])
App
@endcomponent --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
