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
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootswatch@4/dist/{{ \App\Helpers\Tool::config('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/github-markdown-css@2/github-markdown.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fancybox@3/dist/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@7/dist/sweetalert2.min.css">
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
                    {!! \App\Helpers\Tool::markdown2Html(\App\Helpers\Tool::config('copyright',''),true) !!} Made
                    by <a href="https://imwnk.cn">IMWNK</a>.
                </p>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fancybox@3/dist/js/jquery.fancybox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.bootcss.com/jquery_lazyload/1.9.7/jquery.lazyload.min.js"></script>
@yield('js')
{!! \App\Helpers\Tool::config('statistics') !!}
<script>
    $(function () {
        $('[data-fancybox="image-list"]').fancybox({
            type: "image",
            protect: true
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
