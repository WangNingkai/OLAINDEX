@extends('default.layouts.admin')
@section('title','显示设置')
@section('content')
    <p class="form-text text-danger">文件展示类型（扩展名）以空格分开</p>
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="image">图片</label>
            <input type="text" class="form-control" id="image" name="image"
                   value="{{ setting('image') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="video">视频</label>
            <input type="text" class="form-control" id="video" name="video"
                   value="{{ setting('video') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="dash">Dash视频</label>
            <input type="text" class="form-control" id="dash" name="dash"
                   value="{{ setting('dash') }}">
            <span class="form-text text-danger">不支持个人版账户</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="audio">音频</label>
            <input type="text" class="form-control" id="audio" name="audio"
                   value="{{ setting('audio') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="doc">文档</label>
            <input type="text" class="form-control" id="doc" name="doc" value="{{ setting('doc') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="code">代码</label>
            <input type="text" class="form-control" id="code" name="code"
                   value="{{ setting('code') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="stream">文件流</label>
            <input type="text" class="form-control" id="stream" name="stream"
                   value="{{ setting('stream') }}">
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
