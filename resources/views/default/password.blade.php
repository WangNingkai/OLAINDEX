@extends('default.layouts.main')
@section('title','文件夹密码')
@section('js')
    <script src="https://cdn.staticfile.org/marked/0.6.2/marked.min.js"></script>
    <script>
        $(function(){
            @if (!blank(setting('encrypt_tip')))
            document.getElementById('head').innerHTML = marked(`{!! setting('encrypt_tip') !!}`);
            @endif
        })
    </script>
@stop
@section('content')
    @if (!blank(setting('encrypt_tip')))
    <div class="card border-light mb-3">
        <div class="card-header"><i class="fa fa-leaf"></i> HEAD</div>
        <div class="card-body markdown-body" id="head">
        </div>
    </div>
    @endif
    <div class="card border-light mb-3">
        <div class="card-header">
            此文件夹或文件受到保护，您需要提供访问密码才能查看
        </div>
        <div class="card-body">
            <form action="{{ route('password') }}" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="password">请输入密码</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <input type="hidden" name="encryptKey" value="{{ encrypt($encryptKey) }}">
                    <input type="hidden" name="route" value="{{ encrypt($route) }}">
                    <input type="hidden" name="requestPath" value="{{ encrypt($requestPath) }}">
                </div>
                <button type="submit" class="btn btn-primary">确认</button>
            </form>
        </div>
    </div>
@stop
