<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="keywords" content="OLAINDEX,OneDrive,Index,Microsoft OneDrive,Directory Index"/>
    <meta name="description" content="OLAINDEX,Another OneDrive Directory Index"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.loli.net/css?family=Lato:400,700,400italic" rel="stylesheet">
    <link href="https://cdn.bootcss.com/bootswatch/4.3.1/{{ getAdminConfig('theme') }}/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/limonte-sweetalert2/7.33.1/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/github-markdown-css/3.0.1/github-markdown.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/fancybox/3.5.6/jquery.fancybox.min.css" rel="stylesheet">

    @yield('css')
    <style>
        .item-list .list-group-item {
            border: 0;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('onedrive.list') }}">{{ getAdminConfig('site_name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
                aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('onedrive.list') }}"><i class="fa fa-home"></i> 首页</a>
                </li>
        @if (app()->bound('onedrive'))
            @if (Arr::get(app('onedrive')->settings, 'image_hosting') != 'disabled')
                @if(
                    Arr::get(app('onedrive')->settings, 'image_hosting') == 'admin_enabled' && auth()->guard('admin')->check()
                        || Arr::get(app('onedrive')->settings, 'image_hosting') == 'enabled'
                )
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('image') }}"><i class="fa fa-cloud-upload"></i> 图床</a>
                </li>
                @endif
            @endif
        @endif
            @if (auth()->guard('admin')->check())
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.basic') }}"><i class="fa fa-tachometer"></i> 管理</a>
                </li>
            @endif
            </ul>
            @if (app()->bound('onedrive') && auth()->guard('admin')->check())
                <form class="form-inline my-2 my-lg-0" action="{{ route('search', ['onedrive' => app('onedrive')->id]) }}">
                    <input class="form-control mr-sm-2" type="text" name="keywords" placeholder="搜索">
                    <button class="btn btn-secondary my-2 my-sm-0" type="submit">搜索</button>
                </form>
            @endif
            @if (auth()->guard('web')->check())
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> 退出</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="invisible">
                        @csrf
                    </form>
                </li>
            </ul>
            @endif
        </div>
    </div>
</nav>
<div class="container mt-3">
    @if (session()->has('alertMessage'))
        <div class="alert alert-dismissible alert-{{ session()->pull('alertType')}}">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{{ session()->pull('alertMessage') }}</p>
        </div>
    @endif
    @yield('content')
</div>
<div class="container mt-3">
    <footer id="footer">
        <div class="row text-center">
            <div class="col-lg-12">
                <p class="text-muted">
                    {!! markdown2Html(
                        !empty(getAdminConfig('copyright'))
                            ? !empty(getAdminConfig('copyright'))
                            : 'Designed by [IMWNK](https://imwnk.cn/) | Powered by [OLAINDEX](https://git.io/OLAINDEX)',
                        true
                    ) !!}.
                </p>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdn.bootcss.com/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.bootcss.com/limonte-sweetalert2/7.33.1/sweetalert2.all.min.js"></script>
<script src="https://cdn.bootcss.com/jquery_lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="https://cdn.bootcss.com/fancybox/3.5.6/jquery.fancybox.min.js"></script>
<script src="https://cdn.bootcss.com/clipboard.js/2.0.4/clipboard.min.js"></script>
@yield('js')
{!! getAdminConfig('statistics') !!}
<script>
    $(function () {
        $('[data-fancybox="image-list"]').fancybox({
            type: "image"
        });
        let clipboard = new ClipboardJS('.clipboard');
        clipboard.on('success', function (e) {
            console.info('Action:', e.action);
            console.info('Text:', e.text);
            console.info('Trigger:', e.trigger);
            e.clearSelection();
        });
        clipboard.on('error', function (e) {
            console.error('Action:', e.action);
            console.error('Trigger:', e.trigger);
        });
        $('[data-toggle="tooltip"]').tooltip({
            title: '已复制',
            trigger: 'click'
        });
        $('img.lazy').lazyload();
    });
</script>
</body>

</html>
