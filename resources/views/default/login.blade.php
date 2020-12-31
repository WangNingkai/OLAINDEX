
@extends('default.layouts.main')
@section('title','登录')
@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card border-light mb-3 shadow">
                <div class="card-header">
                    <i class="ri-login-circle-fill"></i> 登录
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        @csrf
                        <div class="form-group">
                            <label class="form-control-label" for="name">用户名</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                                   required>
                            @if($errors->has('name')) <span
                                class="form-text text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-control-label" for="password">请输入密码</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            @if($errors->has('password')) <span
                                class="form-text text-danger">{{ $errors->first('password') }}</span>  @endif
                        </div>
                        <button type="submit" class="btn btn-primary">登录</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
