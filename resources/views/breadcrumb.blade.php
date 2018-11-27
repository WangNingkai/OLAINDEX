<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
        @if(!blank($path_array))
            @foreach ($path_array as $key => $value)
                @if(end($path_array) === $value && $key === (count($path_array) - 1))
                    <li class="breadcrumb-item active">{{ str_limit($value,20)  }}</li>
                @else
                    @if (!blank($value))
                        <li class="breadcrumb-item "><a
                                href="{{ route('home',\App\Helpers\Tool::handleUrl(\App\Helpers\Tool::getBreadcrumbUrl($key + 1,$path_array))) }}">{{ str_limit($value,20) }}</a>
                        </li>
                    @endif
                @endif
            @endforeach
        @endif
    </ol>
</nav>
