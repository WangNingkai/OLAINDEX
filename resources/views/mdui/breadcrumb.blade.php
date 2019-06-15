@if(!blank($pathArray))
    @foreach ($pathArray as $key => $value)
        @if(end($pathArray) === $value && $key === (count($pathArray) - 1))
            <i class="mdui-icon material-icons mdui-icon-dark mdui-m-a-0">chevron_right</i>
            <span>{{ \Illuminate\Support\Str::limit($value,20)  }}</span>
        @else
            @if (!blank($value))
                <i class="mdui-icon material-icons mdui-icon-dark  mdui-m-a-0">chevron_right</i>
                <a
                    href="{{ route('home',\App\Utils\Tool::encodeUrl(\App\Utils\Tool::getBreadcrumbUrl($key + 1,$pathArray))) }}">{{ str_limit($value,20) }}</a>
            @endif
        @endif
    @endforeach
@endif
