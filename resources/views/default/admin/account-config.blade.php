@extends('default.layouts.main')
@section('title', '账号配置')
@section('content')
    <div class="card mb-3">
        <div class="card-header">账号配置</div>
        <div class="card-body">
            <form action="" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="root"><b>显示根目录</b></label>
                    <input type="text" class="form-control" id="root" name="root"
                           value="{{ array_get($config,'root','') }}">
                </div>
                <button type="submit" class="btn btn-primary">提交</button>
            </form>
        </div>
    </div>
@stop
