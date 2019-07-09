<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://fonts.loli.net/css?family=Lato:400,700,400italic">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet"
          href="https://cdnjs.loli.net/ajax/libs/bootswatch/4.3.1/{{ getAdminConfig('theme') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
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
        <a class="navbar-brand" href="{{ route('home') }}">{{ getAdminConfig('site_name') }}</a>
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
                        <a class="dropdown-item" href="{{ route('admin.profile.show') }}">密码设置 </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.onedrive.index') }}" ><i class="fa fa-cloud"></i>
                        OneDrive列表 </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs"></i> 文件操作</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.file') }}">普通文件上传 </a>
                        <a class="dropdown-item" href="{{ route('admin.other') }}">其它操作 </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('log-viewer::dashboard') }}" target="_blank"><i
                            class="fa fa-bug"></i>
                        调试日志 </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://onedrive.live.com" target="_blank"><i class="fa fa-cloud"></i>
                        OneDrive管理 </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.logout') }}" class="nav-link"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> 管理员退出</a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="invisible">
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
    {{--  TODO: move  --}}
    <div class="bg-white">
        {{--  <p class="text-center text-muted">{{ \App\Helpers\Tool::getBindAccount() }}</p>
        <p class="text-center">
            <span class="text-info">状态: {{ \App\Helpers\Tool::getOneDriveInfo('state') }} &nbsp;&nbsp;</span>
            <span class="text-danger">已使用: {{ \App\Helpers\Tool::getOneDriveInfo('used') }} &nbsp;&nbsp;</span>
            <span class="text-warning">剩余: {{ \App\Helpers\Tool::getOneDriveInfo('remaining') }} &nbsp;&nbsp;</span>
            <span class="text-success">全部: {{ \App\Helpers\Tool::getOneDriveInfo('total') }} &nbsp;&nbsp;</span>
        </p>  --}}
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
<script src="https://cdnjs.loli.net/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.all.min.js"></script>
@yield('js')
</body>

</html>
