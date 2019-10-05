<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="keywords" content="OLAINDEX,OneDrive,Index,Microsoft OneDrive,Directory Index"/>
    <meta name="description" content="OLAINDEX,Another OneDrive Directory Index"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
          href="https://cdn.staticfile.org/bootswatch/4.3.1/{{ setting('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/github-markdown-css/3.0.1/github-markdown.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/fancybox/3.5.6/jquery.fancybox.min.css">

    @yield('css')
    <style>
        .item-list .list-group-item {
            border: 0;
        }
    </style>
    <script>
        Config = {
            'routes': {
                'upload_image': '{{ route('image.upload') }}'
            },
            '_token': '{{ csrf_token() }}',
        };
    </script>
</head>

<body>
<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ setting('name','OLAINDEX') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
                aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="fa fa-home"></i> 首页</a>
                </li>
                @if( (int)setting('image_hosting') === 1 || ((int)setting('image_hosting') === 2 && auth()->user()))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('image') }}"><i class="fa fa-cloud-upload"></i> 图床</a>
                    </li>
                @endif
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.basic') }}"><i class="fa fa-tachometer"></i> 管理</a>
                    </li>
                @endauth
            </ul>
            @if(setting('open_search',0))
                <form class="form-inline my-2 my-lg-0" action="{{ route('search') }}">
                    <input class="form-control mr-sm-2" type="text" name="keywords" placeholder="搜索">
                    <button class="btn btn-secondary my-2 my-sm-0" type="submit">搜索</button>
                </form>
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
    <footer id="footer">
        <div class="row text-center">
            <div class="col-lg-12">
                <p class="text-muted">
                    {!! \App\Utils\Tool::markdown2Html(setting('copyright','Designed
                    by [IMWNK](https://imwnk.cn/) | Powered by [OLAINDEX](https://git.io/OLAINDEX)'),true) !!}.
                </p>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.staticfile.org/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.staticfile.org/limonte-sweetalert2/7.33.1/sweetalert2.all.min.js"></script>
<script src="https://cdn.staticfile.org/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="https://cdn.staticfile.org/fancybox/3.5.6/jquery.fancybox.min.js"></script>
<script src="https://cdn.staticfile.org/clipboard.js/2.0.4/clipboard.min.js"></script>
@yield('js')
{!! setting('statistics') !!}
<script>
    $(function () {
        $('[data-fancybox="image-list"]').fancybox({
            type: "image",
            thumbs: {
                autoStart: true,
                axis: 'x'
            },
            buttons: [
                "zoom",
                "slideShow",
                "fullScreen",
                "download",
                "thumbs",
                "close"
            ],
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
