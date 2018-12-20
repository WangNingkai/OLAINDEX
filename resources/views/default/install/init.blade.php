<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>初始化安装</title>
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
        <div class="card-header">申请</div>
        <div class="card-body">
            <form action="{{ route('apply') }}" method="get" target="_blank">
                <div class="form-group">
                    <label class="form-control-label" for="redirect_uri">redirect_uri </label>
                    <input type="text" class="form-control" id="redirect_uri" name="redirect_uri"
                           value="{{ trim(config('app.url'),'/').'/oauth' }}">
                    <span class="form-text text-danger">如已申请，请直接在下面配置中填写；也可使用 https://olaindex.ningkai.wang 中转。<b>注：此申请仅支持国际通用版OneDrive，世纪互联版请联系作者。</b></span>
                </div>
                <button type="submit" class="btn btn-info">申请</button>
            </form>
        </div>
    </div>
    <div class="card border-light mb-3">
        <div class="card-header">初始化配置</div>
        <div class="card-body">
            <form action="" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="redirect_uri">redirect_uri </label>
                    <input type="text" class="form-control" id="redirect_uri" name="redirect_uri"
                           value="{{ trim(config('app.url'),'/').'/oauth' }}">
                    <span class="form-text text-danger">演示为本地地址，正确回调地址格式: https://you.domain/oauth 必须为 https</span>
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="client_id"><b>client_id</b></label>
                    <input type="text" class="form-control" id="client_id" name="client_id">
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="client_secret"><b>client_secret</b></label>
                    <input type="text" class="form-control" id="client_secret" name="client_secret">
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="account_type">账户类型</label>
                    <select class="custom-select" name="account_type" id="account_type">
                        <option value="">选择账户类型</option>
                        <option value="com" selected>国际版</option>
                        <option value="cn">国内版（世纪互联）</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
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
<script src="https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4/dist/js/bootstrap.min.js"></script>
</body>

</html>
