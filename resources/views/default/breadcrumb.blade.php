<nav aria-label="breadcrumb" class="d-none d-md-block d-md-none">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
        @if(!blank($path_array))
            @if (count($path_array) < 5)
                @foreach ($path_array as $key => $value)
                    @if(end($path_array) === $value && $key === (count($path_array) - 1))
                        <li class="breadcrumb-item active">{{ \Str::limit($value,20)  }}</li>
                    @else
                        @if (!blank($value))
                            <li class="breadcrumb-item "><a
                                    href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getBreadcrumbUrl($key + 1,$path_array))) }}">{{  \Str::limit($value,20) }}</a>
                            </li>
                        @endif
                    @endif
                @endforeach
            @else
                <li class="breadcrumb-item active"> ...</li>
                @foreach ($path_array as $key => $value)
                    @if(end($path_array) === $value && $key === (count($path_array) - 1))
                        <li class="breadcrumb-item active">{{  \Str::limit($value,20)  }}</li>
                    @else
                        @if (!blank($value) && $key === (count($path_array) - 2))
                            <li class="breadcrumb-item "><a
                                    href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getBreadcrumbUrl($key + 1,$path_array))) }}">{{  \Str::limit($value,20) }}</a>
                            </li>
                        @endif
                    @endif
                @endforeach
            @endif
        @endif
    </ol>
</nav>
