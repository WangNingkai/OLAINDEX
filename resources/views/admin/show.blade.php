@extends('layouts.admin')
@section('title','显示设置')
@section('content')
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="image">图片</label>
            <input type="text" class="form-control" id="image" name="image" value="{{ \App\Helpers\Tool::config('image') }}">
            <small class="form-text text-danger">要显示的图片后缀</small>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="video">视频</label>
            <input type="text" class="form-control" id="video" name="video" value="{{ \App\Helpers\Tool::config('video') }}">
            <small class="form-text text-danger">要显示的视频后缀</small>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="audio">音频</label>
            <input type="text" class="form-control" id="audio" name="audio" value="{{ \App\Helpers\Tool::config('audio') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="doc">文档</label>
            <input type="text" class="form-control" id="doc" name="doc" value="{{ \App\Helpers\Tool::config('doc') }}">
            <small class="form-text text-danger">要显示的文档后缀</small>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="code">代码</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ \App\Helpers\Tool::config('code') }}">
            <small class="form-text text-danger">要显示的代码后缀</small>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="stream">文本</label>
            <input type="text" class="form-control" id="stream" name="stream" value="{{ \App\Helpers\Tool::config('stream') }}">
            <small class="form-text text-danger">要显示的文本后缀</small>
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
