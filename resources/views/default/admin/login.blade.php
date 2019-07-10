<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登陆</title>
    <link rel="stylesheet" href="https://fonts.loli.net/css?family=Lato:400,700,400italic">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/bootswatch/4.3.1/{{ getAdminConfig('theme') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    @yield('css')
</head>

<body>
<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('onedrive.list') }}">{{ getAdminConfig('name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
                aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('onedrive.list')}}"><i class="fa fa-home"></i> 首页</a>
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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-light mb-6">
                <div class="card-header">
                    <i class="fa fa-sign-in"></i> {{ __('messages.admin_login') }}
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.login') }}" method="post">
                        @csrf
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">E-mail</label>
                            <div class="col-md-6">
                                <input id="email" type="email" name="email" value="" required="required" autofocus="autofocus" class="form-control">
                            </div>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.loli.net/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
@yield('js')
</body>

</html>
