@extends('layouts.install')
@section('title','初始化配置'))
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">申请</div>
        <div class="card-body">
            <form action="{{ route('apply') }}" method="get" target="_blank">
                <div class="form-group">
                    <label class="form-control-label" for="redirect_uri">redirect_uri </label>
                    <input type="text" class="form-control" id="redirect_uri" name="redirect_uri" value="http://localhost:8000/oauth">
                    <span class="form-text text-danger">如已申请，请直接在下面配置填写</span>
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
                    <input type="text" class="form-control" id="redirect_uri" name="redirect_uri" value="http://localhost:8000/oauth">
                    <span class="form-text text-danger">演示为本地地址，正确回调地址格式: https://you.domain/oauth 必须为 https</span>
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="client_id">client_id</label>
                    <input type="text" class="form-control" id="client_id" name="client_id">
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="client_secret">client_secret</label>
                    <input type="text" class="form-control" id="client_secret" name="client_secret">
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
    </div>

@stop
