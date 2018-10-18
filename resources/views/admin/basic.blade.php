@extends('layouts.admin')
@section('title','基础设置')
@section('content')
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="name">站点名称</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="{{ \App\Helpers\Tool::config('name') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="theme">站点主题</label>
            <select class="custom-select" name="theme" id="theme">
                @foreach( \App\Helpers\Constants::THEME as $name => $theme)
                    <option value="{{ $theme }}"
                            @if(\App\Helpers\Tool::config('theme') == $theme) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="root">OneDrive根目录</label>
            <input type="text" class="form-control" id="root" name="root"
                   value="{{ \App\Helpers\Tool::config('root') }}">
            <span class="form-text text-danger">目录索引起始文件夹地址，有效文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ |。</span>
        </div>

        <div class="form-group">
            <label class="form-control-label" for="expires">缓存时间(分钟)</label>
            <input type="text" class="form-control" id="expires" name="expires"
                   value="{{ \App\Helpers\Tool::config('expires') }}">
            <span class="form-text text-danger">建议小于10分钟，否则会导致超时</span>
        </div>
        <div class="form-group">
            <label class="form-control-label">开启图床</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting1" name="image_hosting" class="custom-control-input"
                       @if(\App\Helpers\Tool::config('image_hosting') == 1) checked @endif value="1">
                <label class="custom-control-label" for="image_hosting1">开启</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting0" name="image_hosting" class="custom-control-input"
                       @if(\App\Helpers\Tool::config('image_hosting') == 0) checked @endif value="0">
                <label class="custom-control-label" for="image_hosting0">关闭</label>
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="image_hosting_path">图床地址</label>
            <input type="text" class="form-control" id="image_hosting_path" name="image_hosting_path"
                   value="{{ \App\Helpers\Tool::config('image_hosting_path') }}">
            <span class="form-text text-danger">有效文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ |。</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="hotlink_protection">防盗链</label>
            <input type="text" class="form-control" id="hotlink_protection" name="hotlink_protection"
                   value="{{ \App\Helpers\Tool::config('hotlink_protection') }}">
            <span class="form-text text-danger">留空则不开启。链接空格隔开</span>
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
