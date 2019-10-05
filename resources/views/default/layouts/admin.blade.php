<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
          href="https://cdn.staticfile.org/bootswatch/4.3.1/{{ setting('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
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
                'upload_url': '{{ route('admin.url.upload') }}'
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
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="fa fa-home"></i> 首页</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog"></i> 设置</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.basic') }}">基础设置 </a>
                        <a class="dropdown-item" href="{{ route('admin.show') }}">显示设置 </a>
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">密码设置 </a>
                        <a class="dropdown-item" href="{{ route('admin.bind') }}">绑定设置 </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs"></i> 文件操作</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.file') }}">普通文件上传 </a>
                        <a class="dropdown-item" href="{{ route('admin.other') }}">其它操作 </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false"><i class="fa fa-bolt"></i> 缓存</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.cache.clear') }}">缓存清理 </a>
                        <a class="dropdown-item" href="{{ route('admin.cache.refresh') }}"
                           onclick="swal('正在刷新缓存，请稍等');">刷新缓存 </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('log-viewer::dashboard') }}" target="_blank"><i
                            class="fa fa-bug"></i>
                        调试日志 </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                            class="fa fa-sign-out"></i> 退出</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                          class="invisible">
                        @csrf
                    </form>
                </li>

            </ul>
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
    <div class="bg-white">
        <p class="text-center text-muted">{{ one_account('account_email') }}</p>
        <p class="text-center">
            <span class="text-info">状态: {{ one_info('state') }} &nbsp;&nbsp;</span>
            <span class="text-danger">已使用: {{ one_info('used') }} &nbsp;&nbsp;</span>
            <span class="text-warning">剩余: {{ one_info('remaining') }} &nbsp;&nbsp;</span>
            <span class="text-success">全部: {{ one_info('total') }} &nbsp;&nbsp;</span>
        </p>
    </div>
    <div class="card border-light mb-3">
        <div class="card-header">
            @yield('title')
        </div>
        <div class="card-body">
            @yield('content')
        </div>
    </div>
    <footer id="footer">
        <div class="row text-center">
            <div class="col-lg-12">
                <p class="text-muted">
                    Made by <a href="https://imwnk.cn">IMWNK</a>.
                </p>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.staticfile.org/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/popper.js/1.15.0/umd/popper.min.js"></script>
<script src="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.staticfile.org/limonte-sweetalert2/7.33.1/sweetalert2.all.min.js"></script>
@yield('js')
</body>

</html>
