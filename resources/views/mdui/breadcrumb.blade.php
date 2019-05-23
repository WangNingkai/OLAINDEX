{{--@if(!blank($path_array))
    @foreach ($path_array as $key => $value)
        @if(end($path_array) === $value && $key === (count($path_array) - 1))
            <i class="mdui-icon material-icons mdui-icon-dark mdui-m-a-0">chevron_right</i>
            <span>{{ str_limit($value,20)  }}</span>
        @else
            @if (!blank($value))
                <i class="mdui-icon material-icons mdui-icon-dark  mdui-m-a-0">chevron_right</i>
                <a
                    href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(getBreadcrumbUrl($key + 1,$path_array))) }}">{{ str_limit($value,20) }}</a>
            @endif
        @endif
    @endforeach
@endif--}}
