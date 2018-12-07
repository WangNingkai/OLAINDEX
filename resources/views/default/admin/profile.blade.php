@extends('default.layouts.admin')
@section('title','密码设置')
@section('content')
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="old_password">旧密码</label>
            <input type="password" class="form-control" id="old_password" name="old_password" required>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="password">新密码</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="password_confirm">重复新密码</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
