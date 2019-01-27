<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>绑定帐号</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootswatch@4/dist/{{ \App\Helpers\Tool::config('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4/css/font-awesome.min.css">
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
        <div class="card-header">绑定帐号
            <small class="text-danger">请确认以下信息</small>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-control-label" for="client_id">client_id </label>
                <input type="text" class="form-control" id="client_id" name="client_id"
                       value="{{ \App\Helpers\Tool::config('client_id') }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="client_secret">client_secret </label>
                <input type="text" class="form-control" id="client_secret" name="client_secret"
                       value="{{ substr_replace(\App\Helpers\Tool::config('client_secret'),"*****",3,5)}}"
                       disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="redirect_uri">redirect_uri </label>
                <input type="text" class="form-control" id="redirect_uri" name="redirect_uri"
                       value="{{ \App\Helpers\Tool::config('redirect_uri') }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="account_type">账号类型 </label>
                <input type="text" class="form-control" id="account_type" name="account_type"
                       value="{{ \App\Helpers\Tool::config('account_type') }}" disabled>
            </div>
            <form id="bind-form" action="{{ route('bind') }}" method="POST"
                  class="invisible">
                @csrf
            </form>
            <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('bind-form').submit();"
               class="btn btn-info">绑定</a>
            <a href="{{ route('reset') }}" class="btn btn-danger">返回更改</a>
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
</body>

</html>
