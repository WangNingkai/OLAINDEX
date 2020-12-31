<nav aria-label="breadcrumb" class="d-none d-md-block d-md-none">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('drive.query', ['hash' => $hash]) }}"><i
                    class="ri-home-fill"></i> Home</a></li>
        @if(!blank($path))
            @if (count($path) < 6)
                @foreach ($path as $key => $value)
                    @if(end($path) === $value && $key === (count($path) - 1))
                        <li class="breadcrumb-item active">{{ str_limit($value, 20)  }}</li>
                    @else
                        @if (!blank($value))
                            <li class="breadcrumb-item ">
                                <a href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::combineBreadcrumb($key + 1, $path))]) }}">
                                    {{  str_limit($value,20) }}
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
            @else
                <li class="breadcrumb-item active"> ...</li>
                @foreach ($path as $key => $value)
                    @if(end($path) === $value && $key === (count($path) - 1))
                        <li class="breadcrumb-item active">{{  str_limit($value,20)  }}</li>
                    @else
                        @if (!blank($value) && $key === (count($path) - 2))
                            <li class="breadcrumb-item ">
                                <a href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::combineBreadcrumb($key + 1, $path))]) }}">
                                    {{  str_limit($value,20) }}
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
            @endif
        @endif
    </ol>
</nav>
