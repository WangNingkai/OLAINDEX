<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('tabler/dist/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('tabler/dist/css/tabler-vendors.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.8.1/dist/sweetalert2.min.css">
    @stack('stylesheet')
</head>
<body class="antialiased">
<div class="page">
    <header class="navbar navbar-expand-md navbar-light d-print-none">
        <div class="container-xl">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                <a href="{{ route('home') }}">
                    OLAINDEX
                </a>
            </h1>
            <div class="navbar-nav flex-row order-md-last">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                       aria-label="Open user menu">
                        <span class="avatar avatar-sm" style="background-image: url({{ asset('img/log.svg') }})"></span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="mt-1 small text-muted">管理员</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('admin.profile') }}" class="dropdown-item">我的信息</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            退出
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    @include('admin.components.nav')
    <div class="content">
        <div class="container-xl">
            @includeWhen(session()->has('alertMessage') || $errors->any(), 'admin.components.toast')
            @yield('content')
        </div>
        <footer class="footer footer-transparent d-print-none">
            <div class="container">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ms-lg-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                <a href="//olaindex.js.org" target="_blank" class="link-secondary">文档</a>
                            </li>
                            <li class="list-inline-item">
                                <a href="//github.com/WangNingkai/OLAINDEX/issues/new/choose"
                                   target="_blank"
                                   class="link-secondary">反馈</a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://github.com/wangningkai/olaindex" target="_blank" class="link-secondary"
                                   rel="noopener">源码</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                Copyright &copy; {{ date('Y') }}
                                <a href="https://github.com/wangningkai/olaindex" target="_blank"
                                   class="link-secondary">OLAINDEX</a>.
                                All rights reserved.
                            </li>
                            <li class="list-inline-item">
                                <a href="https://github.com/wangningkai/olaindex" target="_blank" class="link-secondary"
                                   rel="noopener">v6.0</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="{{ asset('tabler/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('tabler/dist/libs/jquery/dist/jquery.slim.min.js') }}"></script>
<script src="{{ asset('tabler/dist/js/tabler.min.js') }}"></script>
<script src="https://cdn.staticfile.org/axios/0.21.0/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.8.1/dist/sweetalert2.all.min.js"></script>
@stack('scripts')
</body>
</html>
