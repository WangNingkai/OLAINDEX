<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('list') }}"><i class="fa fa-home"></i> Home</a></li>
        @if(!blank($pathArr))
            @foreach ($pathArr as $key => $value)
                @if(end($pathArr) == $value)
                    <li class="breadcrumb-item active">{{ $value }}</li>
                @else
                    @if (!blank($value))
                        <li class="breadcrumb-item "><a
                                href="{{ route('list',\App\Helpers\Tool::getUrl($key + 1,$pathArr)) }}">{{ $value }}</a>
                        </li>
                    @endif
                @endif
            @endforeach
        @endif
    </ol>
</nav>
