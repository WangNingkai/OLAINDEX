<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <link rel="preconnect" href="https://cdn.staticfile.org">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="keywords" content="OLAINDEX,OneDrive,Index,Microsoft OneDrive,Directory Index"/>
    <meta name="description" content="OLAINDEX,Another OneDrive Directory Index"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.staticfile.org/bootswatch/4.5.3/{{ setting('site_theme','lux') }}/bootstrap.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.staticfile.org/github-markdown-css/4.0.0/github-markdown.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.8.1/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/fancybox/3.5.7/jquery.fancybox.min.css">
    @stack('stylesheet')
    {!! setting('stats_code') !!}
    <style>
        @media (max-width: 991px){
            /*超出不换行，省略掉*/
            .table-sm td:nth-child(1),td:nth-child(2),td:nth-child(3) {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
        }
        @media (max-width: 767px){
            /*文件名字*/
            .table-sm td:nth-child(1) {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            /*标题，文件大小、日期*/
            .w-100 tr>th:nth-child(2),tr>th:nth-child(3){
                display: none;
            }
            /*文件大小、日期*/
            .list-item td:nth-child(2),td:nth-child(3){
                display: none;
            }
            /*操作*/
            .w-100 tr>th:nth-child(4){
                position: absolute;
                right: 24px;
            }
            /*按钮*/
            .list-item td:nth-child(4){
                white-space: nowrap;
                position: absolute;
                right: 24px;
            }
        }
    </style>
    <script>
        const App = {
            'routes': {
                'upload_image': '{{ route('image.upload') }}',
            },
            '_token': '{{ csrf_token() }}',
        }
    </script>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ setting('site_name','OLAINDEX') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="ri-home-fill"></i> 首页</a>
                </li>
                @if( setting('open_image_host',0) && (setting('public_image_host',0) || (!setting('public_image_host',0) && auth()->check())))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('image') }}"><i class="ri-image-fill"></i> 图床</a>
                    </li>
                @endif
            </ul>
            @auth
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                我的信息
                            </a>
                            @auth
                                <a class="dropdown-item" href="{{ route('admin') }}">
                                    控制台
                                </a>
                            @endauth
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                退出
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>

<div class="container mt-3">
    @includeWhen(session()->has('alertMessage') || $errors->any(), 'default.components.toast')
    <div class="mt-2" style="min-height: 750px">
        @yield('content')
    </div>
    <footer class="footer">
        <div class="row text-center">
            <div class="col-lg-12">
                <p class="text-muted">
                    {!! marked(setting('copyright','Designed
                    by [IMWNK](https://imwnk.cn/) | Powered by [OLAINDEX](https://git.io/OLAINDEX)'),true) !!}
                </p>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.staticfile.org/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdn.staticfile.org/twitter-bootstrap/4.5.3/js/bootstrap.min.js"></script>
<script src="https://cdn.staticfile.org/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="https://cdn.staticfile.org/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script src="https://cdn.staticfile.org/clipboard.js/2.0.6/clipboard.min.js"></script>
<script src="https://cdn.staticfile.org/axios/0.21.0/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.8.1/dist/sweetalert2.all.min.js"></script>
<script>
    $(function() {
        $('[data-fancybox="image-list"]').fancybox({
            type: 'image',
            thumbs: {
                autoStart: true,
                axis: 'x',
            },
            buttons: [
                'zoom',
                'slideShow',
                'fullScreen',
                'download',
                'thumbs',
                'close',
            ],
        })
        let clipboard = new ClipboardJS('.clipboard')
        clipboard.on('success', function(e) {
            console.info('Action:', e.action)
            console.info('Text:', e.text)
            console.info('Trigger:', e.trigger)
            e.clearSelection()
        })
        clipboard.on('error', function(e) {
            console.error('Action:', e.action)
            console.error('Trigger:', e.trigger)
        })
        $('[data-toggle="tooltip"]').tooltip({
            title: '已复制',
            trigger: 'click',
        })
        $('img.lazy').lazyload()
    })
</script>
@stack('scripts')

</body>

</html>
