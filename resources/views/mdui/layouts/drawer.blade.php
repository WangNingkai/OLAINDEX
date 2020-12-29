<div class="mdui-drawer mdui-drawer-close" id="main-drawer">
    <div class="mdui-list" mdui-collapse="{accordion: true}">
        <li class="mdui-list-item mdui-ripple">
            <a href="{{ route('home') }}" class="mdui-list-item-icon mdui-icon material-icons">home</a>
            <a href="{{ route('home') }}" class="mdui-list-item-content">
                首页
            </a>
        </li>
        @if( setting('open_image_host',0) && (setting('public_image_host',0) || (!setting('public_image_host',0) && auth()->check())) && !request()->routeIs(['image']))
            <li class="mdui-list-item mdui-ripple">
                <a href="{{ route('image') }}" class="mdui-list-item-icon mdui-icon material-icons">insert_photo</a>
                <a href="{{ route('image') }}" class="mdui-list-item-content">
                    图床
                </a>
            </li>
        @endif

        <li class="mdui-list-item mdui-ripple"
            onclick="window.theme.toggle_theme();">
            <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">brightness_4</a>
            <a class="mdui-list-item-content">暗黑模式</a>
        </li>

        @if(request()->routeIs(['home','drive.query']) && !$need_pass)
            <div class="mdui-divider"></div>
            @foreach($accounts as $key => $account)
                <a
                    href="{{ route('drive.query', ['hash' => $account['hash_id']]) }}"
                    class="mdui-list-item mdui-ripple"><i
                        class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-light-blue">cloud</i>
                    <div class="mdui-list-item-content">
                        {{ $account['remark'] }}
                    </div>
                </a>
            @endforeach
        @endif
        <div class="mdui-divider"></div>
        <a href="https://github.com/wangningkai/OLAINDEX"
           class="mdui-list-item mdui-ripple mdui-hidden-sm-up">
            <i class="mdui-list-item-icon mdui-icon material-icons">code</i>
            <div class="mdui-list-item-content">Github</div>
        </a>
    </div>
</div>
