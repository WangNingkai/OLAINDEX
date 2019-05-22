@extends('default.layouts.admin')
@section('title', '密码设置')
@section('content')
    <form action="{{ route('admin.profile.post') }}" method="post">
        @csrf
        <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="old_password">旧密码</label>
            <input type="password" class="form-control{{ $errors->has('old_password') ? ' is-invalid' : '' }}" id="old_password" name="old_password" required>
            @if ($errors->has('old_password'))
            <div class="invalid-feedback">{{ $errors->first('old_password') }}</div>
            @endif
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="password">新密码</label>
            <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" name="password" required>
            @if ($errors->has('password'))
            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
            @endif
        </div>
        <div class="form-group{{ $errors->has('password_confirm') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="password_confirm">重复新密码</label>
            <input type="password" class="form-control{{ $errors->has('password_confirm') ? ' is-invalid' : '' }}" id="password_confirm" name="password_confirm" required>
            @if ($errors->has('password_confirm'))
            <div class="invalid-feedback">{{ $errors->first('password_confirm') }}</div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
