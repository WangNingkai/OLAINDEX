<nav aria-label="breadcrumb" class="d-none d-md-block d-md-none">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('onedrive.list') }}"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('home', ['onedrove' => app('onedrive')->id]) }}"><i class="fa fa-cloud" aria-hidden="true"></i> {{ app('onedrive')->name }}</a>
        </li>
    @if (Arr::get(app('request')->route()->action, 'as') == 'search')
        <li class="breadcrumb-item active">搜索: {{ app('request')->get('keywords') }}</li>
    @endif
        
    @if(!blank($path_array))
        @if (count($path_array) < 5)
            @foreach ($path_array as $key => $value)
                @if(end($path_array) === $value && $key === (count($path_array) - 1))
                    <li class="breadcrumb-item active">{{ Str::limit($value,20) }}</li>
                @else
                    @if (!blank($value))
                    <li class="breadcrumb-item">
                        <a href="{{ route('home', [
                                'query'    => Tool::getEncodeUrl(getBreadcrumbUrl($key + 1, $path_array)),
                                'onedrive' => app('onedrive')->id
                            ]) }}">{{ Str::limit($value, 20) }}
                        </a>
                    </li>
                    @endif
                @endif
            @endforeach
        @else
            <li class="breadcrumb-item active"> ...</li>
            @foreach ($path_array as $key => $value)
                @if(end($path_array) === $value && $key === (count($path_array) - 1))
                    <li class="breadcrumb-item active">{{ Str::limit($value,20) }}</li>
                @else
                    @if (!blank($value) && $key === (count($path_array) - 2))
                    <li class="breadcrumb-item">
                        <a href="{{ route('home', [
                                'query'    => Tool::getEncodeUrl(getBreadcrumbUrl($key + 1, $path_array)),
                                'onedrive' => app('onedrive')->id
                            ]) }}">{{ Str::limit($value, 20) }}
                        </a>
                    </li>
                    @endif
                @endif
            @endforeach
        @endif
    @endif
    </ol>
</nav>
