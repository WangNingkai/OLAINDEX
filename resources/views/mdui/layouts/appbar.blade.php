<div class="mdui-appbar mdui-appbar-fixed mdui-shadow-0 mdui-appbar-scroll-toolbar-hide">
    <div class="mdui-toolbar">
        <span
            class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white"
            mdui-tooltip="{content: '菜单'}"
            onclick='mdui.$("body").toggleClass("mdui-drawer-body-left");'
            id="toggle-drawer"
        ><i class="mdui-icon material-icons">menu</i></span>
        <a
            mdui-tooltip="{content: '返回'}"
            class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white mdui-hidden-sm-up"
            onclick="window.history.back()">
            <i class="mdui-icon material-icons">arrow_back</i>
        </a>
        <a href="{{ route('home') }}" class="mdui-typo-headline">
            {{ setting('site_name','OLAINDEX') }}
        </a>
        @if(request()->routeIs(['home','drive.query']) && !$need_pass)
            @if (count($path) < 6)
                @foreach($path as $key => $value)
                    @if(end($path) === $value && $key === (count($path) - 1))
                        <i class="mdui-icon material-icons mdui-hidden-xs mdui-m-a-0"
                        >chevron_right</i>
                        <a href="javascript:void(0)" class="mdui-typo-subheading mdui-hidden-xs">
                            {{ $value }}
                        </a>
                    @else
                        @if (!blank($value))
                            <i class="mdui-icon material-icons mdui-hidden-xs mdui-m-a-0"
                            >chevron_right</i>
                            <a href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::combineBreadcrumb($key + 1, $path))]) }}"
                               class="mdui-typo-subheading mdui-hidden-xs">
                                {{ $value }}
                            </a>
                        @endif
                    @endif
                @endforeach
            @else
                <i class="mdui-icon material-icons mdui-hidden-xs mdui-m-a-0"
                >chevron_right</i>
                <a href="javascript:void(0)" class="mdui-typo-subheading mdui-hidden-xs">
                    ...
                </a>
                @foreach($path as $key => $value)
                    @if(end($path) === $value && $key === (count($path) - 1))
                        <i class="mdui-icon material-icons mdui-hidden-xs mdui-m-a-0"
                        >chevron_right</i>
                        <a href="javascript:void(0)" class="mdui-typo-subheading mdui-hidden-xs">
                            {{ $value }}
                        </a>
                    @else
                        @if (!blank($value) && $key === (count($path) - 2))
                            <i class="mdui-icon material-icons mdui-hidden-xs mdui-m-a-0"
                            >chevron_right</i>
                            <a href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::combineBreadcrumb($key + 1, $path))]) }}"
                               class="mdui-typo-subheading mdui-hidden-xs">
                                {{ $value }}
                            </a>
                        @endif
                    @endif
                @endforeach
            @endif
        @endif
        <div class="mdui-toolbar-spacer"></div>

    </div>
</div>
