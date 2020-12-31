@extends('admin.layouts.main')
@section('title', '初始化安装')
@section('content')
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    OLAINDEX
                </div>
                <h2 class="page-title">
                    初始化安装
                </h2>
            </div>
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">申请</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('apply') }}" method="get" target="_blank">
                        <div class="form-group mb-3">
                            <label class="form-label" for="redirectUri">redirect_uri </label>
                            <input type="text" class="form-control" id="redirectUri" name="redirectUri"
                                   value="{{ trim(config('app.url'),'/').'/callback' }}">
                            <span
                                class="form-hint text-danger">如已申请，请直接在下面配置中填写；也可使用 https://olaindex.github.io/oauth.html 中转。<b>注：此申请流程仅支持国际版OneDrive，世纪互联版需单独申请。</b></span>
                        </div>
                        <button type="submit" class="btn btn-primary">申请</button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">初始化配置</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label" for="redirectUri">redirect_uri </label>
                            <input type="text" class="form-control" id="redirectUri" name="redirectUri"
                                   value="{{ trim(config('app.url'),'/').'/callback' }}">
                            <span class="form-hint text-danger">确保回调地址格式为此形式 http(s)://you.domain/callback，使用中转域名无需https协议（注意：如果通过CDN开启HTTPS而非配置SSL证书，部分回调CDN会跳转http地址，从而导致申请失败） </span>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="clientId"><b>client_id</b></label>
                            <input type="text" class="form-control" id="clientId" name="clientId">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="clientSecret"><b>client_secret</b></label>
                            <input type="text" class="form-control" id="clientSecret" name="clientSecret">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="accountType">账户类型</label>
                            <div>
                                <select class="form-select" name="accountType" id="accountType">
                                    <option value="">选择账户类型</option>
                                    <option value="COM" selected>国际版</option>
                                    <option value="CN">国内版（世纪互联）</option>
                                </select>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
