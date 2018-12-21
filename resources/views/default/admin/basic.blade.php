@extends('default.layouts.admin')
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
                @foreach( \App\Helpers\Constants::SITE_THEME as $name => $theme)
                    <option value="{{ $theme }}"
                            @if(\App\Helpers\Tool::config('theme') === $theme) selected @endif>{{ $name }}</option>
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
            <span class="form-text text-danger">建议小于60分钟，否则会导致响应失败</span>
        </div>

        <div class="form-group">
            <label for="encrypt_path">加密路径</label>
            <textarea class="form-control" id="encrypt_path" name="encrypt_path"
                      rows="5">{{ \App\Helpers\Tool::config('encrypt_path','') }}</textarea>
            <span class="form-text text-danger">格式如： /path1/xxx/ /path2/xxx/ password1,/path3/xxx/ /path4/xxx/ password2 (以OneDrive根目录为基础)</span>
        </div>
        <div class="form-group">
            <label for="">加密选项</label>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input"  id="c1" name="encrypt_option[]" value="list" @if(in_array('list',\App\Helpers\Tool::config('encrypt_option',[]))) checked @endif>
                <label class="custom-control-label" for="c1">加密目录列表</label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c2" name="encrypt_option[]" value="show" @if(in_array('show',\App\Helpers\Tool::config('encrypt_option',[]))) checked @endif>
                <label class="custom-control-label" for="c2">加密文件预览</label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c3" name="encrypt_option[]" value="download" @if(in_array('download',\App\Helpers\Tool::config('encrypt_option',[]))) checked @endif>
                <label class="custom-control-label" for="c3">加密文件下载</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c4" name="encrypt_option[]" value="view" @if(in_array('view',\App\Helpers\Tool::config('encrypt_option',[]))) checked @endif>
                <label class="custom-control-label" for="c4">加密图片查看页</label>
            </div>

        </div>
        <div class="form-group">
            <label class="form-control-label">开启看图模式</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_view1" name="image_view" class="custom-control-input"
                       @if(\App\Helpers\Tool::config('image_view',0) == 1) checked @endif value="1">
                <label class="custom-control-label" for="image_view1">开启</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_view0" name="image_view" class="custom-control-input"
                       @if(\App\Helpers\Tool::config('image_view',0) == 0) checked @endif value="0">
                <label class="custom-control-label" for="image_view0">关闭</label>
            </div>
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
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting2" name="image_hosting" class="custom-control-input"
                       @if(\App\Helpers\Tool::config('image_hosting') == 2) checked @endif value="2">
                <label class="custom-control-label" for="image_hosting2">仅管理员开启</label>
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-label">图床设为首页</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_home1" name="image_home" class="custom-control-input"
                       @if(\App\Helpers\Tool::config('image_home',0) == 1) checked @endif value="1">
                <label class="custom-control-label" for="image_home1">开启</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_home0" name="image_home" class="custom-control-input"
                       @if(\App\Helpers\Tool::config('image_home',0) == 0) checked @endif value="0">
                <label class="custom-control-label" for="image_home0">关闭</label>
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
        <div class="form-group">
            <label class="form-control-label" for="copyright">自定义版权显示</label>
            <input type="text" class="form-control" id="copyright" name="copyright"
                   value="{{ \App\Helpers\Tool::config('copyright','') }}">
            <span class="form-text text-danger">留空则不显示。markdown格式书写 如：Made by [xxx](https://xxx)</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="statistics">统计代码</label>
            <input type="text" class="form-control" id="statistics" name="statistics"
                   value="{{ \App\Helpers\Tool::config('statistics','') }}">
            <span class="form-text text-danger">js统计代码</span>
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
