@extends('layouts.main')
@section('title','文件夹密码')
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">
            请输入文件夹密码
        </div>
        <div class="card-body">
            <form action="{{ route('password') }}" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="password">请输入密码</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <input type="hidden" name="pass_id" value="{{ encrypt($pass_id) }}">
                    <input type="hidden" name="path" value="{{ encrypt($path) }}">
                </div>
                <button type="submit" class="btn btn-primary">确认</button>
            </form>
        </div>
    </div>
@stop
