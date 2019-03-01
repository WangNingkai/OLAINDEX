<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登陆</title>
    <link rel="stylesheet" href="https://fonts.loli.net/css?family=Lato:400,700,400italic">
    <link rel="stylesheet"
          href="https://cdnjs.loli.net/ajax/libs/bootswatch/4.3.1/{{ \App\Helpers\Tool::config('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    @yield('css')
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
    <div class="card border-light mb-3">
        <div class="card-header">
            <i class="fa fa-sign-in"></i> 登陆
        </div>
        <div class="card-body">
            <form action="" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="password">请输入密码</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">登陆</button>
            </form>
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
<script src="https://cdnjs.loli.net/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
@yield('js')
</body>

</html>
