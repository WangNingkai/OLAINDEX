@extends('default.layouts.main')
@section('title','文件夹密码')
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">
            此文件夹或文件受到保护，您需要提供访问密码才能查看
        </div>
        <div class="card-body">
            <form action="{{ route('password', ['onedrive' => app('onedrive')->id]) }}" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="password">请输入密码</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <input type="hidden" name="encryptKey" value="{{ encrypt($encryptKey) }}">
                    <input type="hidden" name="route" value="{{ encrypt($route) }}">
                    <input type="hidden" name="realPath" value="{{ encrypt($realPath) }}">
                </div>
                <button type="submit" class="btn btn-primary">确认</button>
            </form>
        </div>
    </div>
@stop
