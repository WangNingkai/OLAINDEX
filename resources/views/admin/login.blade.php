<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ setting('site_name','OLAINDEX') . '- 登录' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tabler@1.0.0-alpha.8/dist/css/tabler.min.css">
</head>
<body class="antialiased border-top-wide border-primary d-flex flex-column">
<div class="flex-fill d-flex flex-column justify-content-center py-4">
    <div class="container-tight py-6">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}">
                {{--                <img src="{{ asset('img/log.svg') }}" height="36" alt="">--}}
                <span class="h1">OLAINDEX Admin</span>
            </a>
        </div>
        <form class="card card-md" action="" method="post" autocomplete="off">
            @csrf
            <div class="card-body">
                <h2 class="card-title text-center mb-4">登录账户</h2>
                <div class="mb-3">
                    <label class="form-label" for="name">用户名</label>
                    <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" id="name"
                           name="name" value="{{ old('name') }}" placeholder="输入用户名">
                    @if($errors->has('name'))
                        <div class="invalid-feedback"> {{ $errors->first('name') }}</div>
                    @endif

                </div>
                <div class="mb-2">
                    <label class="form-label" for="password">
                        密码
                    </label>
                    <input type="password" class="form-control @if($errors->has('password')) is-invalid @endif"
                           name="password" id="password" placeholder="输入密码" autocomplete="off">
                    @if($errors->has('password'))
                        <div class="invalid-feedback"> {{ $errors->first('password') }}</div>
                    @endif

                </div>
                <div class="mb-2">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember"/>
                        <span class="form-check-label">记住设备</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">登录</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="//cdn.jsdelivr.net/npm/tabler@1.0.0-alpha.8/dist/libs/jquery/dist/jquery.slim.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/tabler@1.0.0-alpha.8/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/tabler@1.0.0-alpha.8/dist/js/tabler.min.js"></script>
</body>
</html>
