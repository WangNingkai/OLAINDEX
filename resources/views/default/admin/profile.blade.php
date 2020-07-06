@extends('default.layouts.main')
@section('title', '其它')
@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.config') }}">设置</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.account.list') }}">账号列表</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.profile') }}">其它</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{  route('admin.logs') }}">日志</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <form action="" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="old_password"><b>原密码</b></label>
                    <input type="password" class="form-control" id="old_password" name="old_password">
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="password"><b>新密码</b></label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="password_confirmation"><b>确认密码</b></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
                <button type="submit" class="btn btn-primary">提交</button>
            </form>
        </div>
    </div>
@stop
