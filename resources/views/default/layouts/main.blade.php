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
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('/favicon.ico') }}">
    <link rel="stylesheet" href="https://fonts.loli.net/css?family=Lato:400,700,400italic">
    <link rel="stylesheet"
          href="https://cdnjs.loli.net/ajax/libs/bootswatch/4.3.1/{{ \App\Helpers\Tool::config('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/github-markdown-css/3.0.1/github-markdown.min.css">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/fancybox/3.5.6/jquery.fancybox.min.css">

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
        <a class="navbar-brand" href="{{ route('home') }}">{{ \App\Helpers\Tool::config('name','OLAINDEX') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
                aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="fa fa-home"></i> 首页</a>
                </li>
                @if (\App\Helpers\Tool::config('image_hosting',0))
                    @if( (int)\App\Helpers\Tool::config('image_hosting') === 2 && session()->has('LogInfo') || (int)\App\Helpers\Tool::config('image_hosting') === 1)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('image') }}"><i class="fa fa-cloud-upload"></i> 图床</a>
                        </li>
                    @endif
                @endif
                @if (session()->has('LogInfo'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.basic') }}"><i class="fa fa-tachometer"></i> 管理</a>
                    </li>
                @endif
            </ul>
            @if (session()->has('LogInfo'))
                <form class="form-inline my-2 my-lg-0" action="{{ route('search') }}">
                    <input class="form-control mr-sm-2" type="text" name="keywords" placeholder="搜索">
                    <button class="btn btn-secondary my-2 my-sm-0" type="submit">搜索</button>
                </form>
            @endif
            @if (session()->has('index_log_info'))
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
    <footer id="footer">
        <div class="row text-center">
            <div class="col-lg-12">
                <p class="text-muted">
                    {!! \App\Helpers\Tool::markdown2Html(\App\Helpers\Tool::config('copyright','Designed
                    by [IMWNK](https://imwnk.cn/) | Powered by [OLAINDEX](https://git.io/OLAINDEX)'),true) !!}.
                </p>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdnjs.loli.net/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/fancybox/3.5.6/jquery.fancybox.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
@yield('js')
{!! \App\Helpers\Tool::config('statistics') !!}
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
