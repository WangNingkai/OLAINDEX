<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootswatch@4/dist/{{ \App\Helpers\Tool::config('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@7/dist/sweetalert2.min.css">
    @yield('css')
    <script>
        Config = {
            'routes': {
                'upload_image': '{{ route('image.upload') }}',
                'upload_file': '{{ route('admin.file.upload') }}'
            },
            '_token': '{{ csrf_token() }}',
        };
    </script>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ \App\Helpers\Tool::config('name','OLAINDEX') }}</a>
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
                       aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs"></i> 设置</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.basic') }}">基础设置 </a>
                        <a class="dropdown-item" href="{{ route('admin.show') }}">显示设置 </a>
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">其他设置 </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.file') }}"><i class="fa fa-cloud-upload"></i> 文件上传 </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.clear') }}"><i class="fa fa-bolt"></i> 缓存清理 </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://onedrive.live.com" target="_blank"><i class="fa fa-cloud"></i> OneDrive管理 </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> 退出</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                          style="display: none;">
                        @csrf
                    </form>
                </li>

            </ul>
        </div>
    </div>
</nav>
<div class="container" style="margin-top: 10px">
    @if (session()->has('alertMessage'))
        <div class="alert alert-dismissible alert-{{ session()->pull('alertType')}}">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{{ session()->pull('alertMessage') }}</p>
        </div>
    @endif
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
                <p>Made by <a href="http://imwnk.cn">IMWNK</a>.</p>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/return-top@1/dist/x-return-top.min.js" left="85%" bottom="10%"
        text="返回顶部"></script>
@yield('js')
</body>

</html>
