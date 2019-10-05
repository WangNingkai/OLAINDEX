<nav aria-label="breadcrumb" class="d-none d-md-block d-md-none">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
        @if(!blank($pathArray))
            @if (count($pathArray) < 5)
                @foreach ($pathArray as $key => $value)
                    @if(end($pathArray) === $value && $key === (count($pathArray) - 1))
                        <li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($value,20)  }}</li>
                    @else
                        @if (!blank($value))
                            <li class="breadcrumb-item "><a
                                    href="{{ route('home',\App\Utils\Tool::encodeUrl(\App\Utils\Tool::getBreadcrumbUrl($key + 1,$pathArray))) }}">{{  \Illuminate\Support\Str::limit($value,20) }}</a>
                            </li>
                        @endif
                    @endif
                @endforeach
            @else
                <li class="breadcrumb-item active"> ...</li>
                @foreach ($pathArray as $key => $value)
                    @if(end($pathArray) === $value && $key === (count($pathArray) - 1))
                        <li class="breadcrumb-item active">{{  \Illuminate\Support\Str::limit($value,20)  }}</li>
                    @else
                        @if (!blank($value) && $key === (count($pathArray) - 2))
                            <li class="breadcrumb-item "><a
                                    href="{{ route('home',\App\Utils\Tool::encodeUrl(\App\Utils\Tool::getBreadcrumbUrl($key + 1,$pathArray))) }}">{{  \Illuminate\Support\Str::limit($value,20) }}</a>
                            </li>
                        @endif
                    @endif
                @endforeach
            @endif
        @endif
    </ol>
</nav>
