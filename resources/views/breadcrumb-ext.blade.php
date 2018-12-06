@if(!blank($path_array))
    @foreach ($path_array as $key => $value)
        @if(end($path_array) === $value && $key === (count($path_array) - 1))
            <i class="mdui-icon material-icons mdui-icon-dark" style="margin:0;">chevron_right</i>
            <span>{{ str_limit($value,20)  }}</span>
        @else
            @if (!blank($value))
                <i class="mdui-icon material-icons mdui-icon-dark" style="margin:0;">chevron_right</i>
                <a
                    href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getBreadcrumbUrl($key + 1,$path_array))) }}">{{ str_limit($value,20) }}</a>
            @endif
        @endif
    @endforeach
@endif

@if ($switch)
    <label class="mdui-switch" style="position: absolute;right: 0">
        <img src="https://i.loli.net/2018/12/04/5c05f0c25aebd.png"
             style="width: 18px;position: relative;top: 5px;right: 5px;" alt="切换">
        <input class="display-type" id="display-type-chk" type="checkbox"/>
        <i class="mdui-switch-icon"></i>
    </label>
@endif
