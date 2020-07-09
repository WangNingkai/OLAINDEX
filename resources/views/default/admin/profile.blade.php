@extends('default.layouts.main')
@section('title', '账户')
@section('content')
    <div class="card mb-3">
        <div class="card-header">
            @include('default.components.admin-nav')
        </div>
        <div class="card-body">
            <form action="" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="name"><b>用户名</b></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}">
                </div>
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
