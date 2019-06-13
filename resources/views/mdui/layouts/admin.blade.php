<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OLAINDEX 管理</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.staticfile.org/mdui/0.4.2/css/mdui.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
    @yield('css')
    <script>
        Config = {
            'routes': {
                'upload_image': '{{ route('image.upload') }}',
                'upload_file': '{{ route('admin.file.upload') }}',
                'copy': '{{ route('admin.copy') }}',
                'move': '{{ route('admin.move') }}',
                'path2id': '{{ route('admin.path2id') }}',
                'share': '{{ route('admin.share') }}',
                'delete_share': '{{ route('admin.share.delete') }}',
                'upload_url': '{{ route('admin.url.upload') }}',
            },
            '_token': '{{ csrf_token() }}',
        };
    </script>
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-indigo mdui-theme-accent-blue">
<header class="mdui-appbar mdui-appbar-fixed">
    <div class="mdui-toolbar mdui-color-theme">
        <span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white"
              mdui-drawer="{target: '#main-drawer', swipe: true}"><i class="mdui-icon material-icons">menu</i></span>
        <a href="{{ route('home') }}" target="_blank" class="mdui-typo-headline mdui-hidden-xs">OLAINDEX</a>
        <div class="mdui-toolbar-spacer"></div>
        <a href="{{ route('admin.bind') }}" class="mdui-btn"><i
                class="mdui-icon material-icons">face</i> {{ \Illuminate\Support\Str::before(setting('account_email'),'@')  }}
        </a>
        <a onclick="event.preventDefault();document.getElementById('logout-form').submit();"
           href="javascript:void(0)" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">exit_to_app</i></a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST"
              class="mdui-hidden">
            @csrf
        </form>
    </div>
</header>
<div class="mdui-drawer" id="main-drawer">
    <div class="mdui-list" mdui-collapse="{accordion: true}">
        <a href="{{ route('search') }}" class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-red">search</i>
            <div class="mdui-list-item-content">搜索</div>
        </a>

        <div class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-indigo">storage</i>
            <div class="mdui-list-item-content"><span
                    class="mdui-text-color-red">{{ one_info('used') }}</span> / <span
                    class="mdui-text-color-green">{{ one_info('total') }}</span></div>
        </div>
        <div
            class="mdui-collapse-item @if(in_array(request()->route()->getName(),['admin.basic','admin.show','admin.profile','admin.bind'])) mdui-collapse-item-open @endif">
            <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-blue">settings</i>
                <div class="mdui-list-item-content">设置</div>
                <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
            </div>
            <div class="mdui-collapse-item-body mdui-list">
                <a class="mdui-list-item mdui-ripple @if(request()->routeIs('admin.basic')) mdui-list-item-active @endif"
                   href="{{ route('admin.basic') }}">基础设置 </a>
                <a class="mdui-list-item mdui-ripple @if(request()->routeIs('admin.show')) mdui-list-item-active @endif"
                   href="{{ route('admin.show') }}">显示设置 </a>
                <a class="mdui-list-item mdui-ripple @if(request()->routeIs('admin.profile')) mdui-list-item-active @endif"
                   href="{{ route('admin.profile') }}">密码设置 </a>
                <a class="mdui-list-item mdui-ripple @if(request()->routeIs('admin.bind')) mdui-list-item-active @endif"
                   href="{{ route('admin.bind') }}">绑定设置 </a>
            </div>
        </div>
        <div
            class="mdui-collapse-item @if(in_array(request()->route()->getName(),['admin.file','admin.other'])) mdui-collapse-item-open @endif">
            <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-green">widgets</i>
                <div class="mdui-list-item-content">文件操作</div>
                <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
            </div>
            <div class="mdui-collapse-item-body mdui-list">
                <a class="mdui-list-item mdui-ripple @if(request()->routeIs('admin.file')) mdui-list-item-active @endif"
                   href="{{ route('admin.file') }}">普通文件上传 </a>
                <a class="mdui-list-item mdui-ripple @if(request()->routeIs('admin.other')) mdui-list-item-active @endif"
                   href="{{ route('admin.other') }}">其它操作 </a>
            </div>
        </div>

        <div
            class="mdui-collapse-item">
            <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-red">clear_all</i>
                <div class="mdui-list-item-content">缓存</div>
                <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
            </div>
            <div class="mdui-collapse-item-body mdui-list">
                <a class="mdui-list-item mdui-ripple"
                   href="{{ route('admin.cache.clear') }}">缓存清理 </a>
                <a class="mdui-list-item mdui-ripple"
                   href="{{ route('admin.cache.refresh') }}"
                   onclick="mdui.snackbar({ message: '正在刷新缓存，请稍等', position: 'right-top' });">缓存刷新 </a>
            </div>
        </div>
        <a href="{{ route('image') }}" class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-pink">image</i>
            <div class="mdui-list-item-content">图床</div>
        </a>
        <a href="{{ route('log-viewer::dashboard') }}" class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-grey">bug_report</i>
            <div class="mdui-list-item-content">调试日志</div>
        </a>
    </div>
</div>

<a id="anchor-top"></a>

<div class="mdui-container">
    @yield('content')
</div>
<script src="https://cdn.staticfile.org/mdui/0.4.2/js/mdui.min.js"></script>
<script src="https://cdn.staticfile.org/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/limonte-sweetalert2/7.33.1/sweetalert2.all.min.js"></script>
@if (session()->has('alertMessage'))
    <script>
        $(function () {
            mdui.snackbar({
                message: '{{ session()->pull('alertMessage') }}',
                position: 'right-top'
            });
        });
    </script>
@endif
@yield('js')
</body>

</html>
