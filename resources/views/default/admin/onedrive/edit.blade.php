@extends('default.layouts.admin')
@section('title','新增 OneDrive')
@section('content')
@includeWhen(!empty(session('message')), 'default.widgets.success')
@includeWhen($errors->isNotEmpty(), 'default.widgets.errors')
<form action="{{ route('admin.onedrive.update', ['onedrive' => $oneDrive->id]) }}" method="POST">
    {{ method_field('PUT') }}
    @csrf
    <div class="form-group row">
        <label for="name" class="col-sm-2 col-form-label">名称</label>
        <div class="col-sm-10">
            <input type="text" name="name" class="form-control" id="name" placeholder="名称..." value="{{ $oneDrive->name }}">
        </div>
    </div>
    <div class="form-group row">
        <label for="root" class="col-sm-2 col-form-label">根目录</label>
        <div class="col-sm-10">
            <input type="text" name="root" class="form-control" id="root" placeholder="根目录..." value="{{ $oneDrive->root }}">
            <span class="form-text text-danger">目录索引起始文件夹地址，文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ | 否则无法索引。</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2">是否开启图床</label>
        <div class="col-sm-10">
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting1" name="settings[image_hosting]" class="custom-control-input"
                    @if (Arr::get($oneDrive->settings, 'image_hosting') == 'enabled') checked @endif value="enabled">
                <label class="custom-control-label" for="image_hosting1">开启 &nbsp;&nbsp;</label>

            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting0" name="settings[image_hosting]" class="custom-control-input"
                    @if (Arr::get($oneDrive->settings, 'image_hosting') == 'disabled') checked @endif value="disabled">
                <label class="custom-control-label" for="image_hosting0">关闭 &nbsp;&nbsp;</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting2" name="settings[image_hosting]" class="custom-control-input"
                    @if (Arr::get($oneDrive->settings, 'image_hosting') == 'admin_enabled') checked @endif value="admin_enabled">
                <label class="custom-control-label" for="image_hosting2">仅管理员开启 </label>
            </div>
        </div>
    </div>
    <div class="row">
        <label class="form-control-label col-sm-2">是否将图床设为首页</label>
        <div class="form-group col-sm-10">
            <input type="hidden" name="settings[image_home]" value="0">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="settings[image_home]" id="customSwitch1"
                    data-on-text="开启" data-off-text="关闭"
                    @if (Arr::get($oneDrive->settings, 'image_home')) checked @endif value="{{ Arr::get($oneDrive->settings, 'image_home') ? '1' : '0'}}">
                <label class="custom-control-label" for="customSwitch1"></label>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="root" class="col-sm-2 col-form-label">图床保存地址</label>
        <div class="col-sm-10">
            <input type="text" name="settings[image_hosting_path]" class="form-control" id="image_hosting_path" placeholder="图床保存地址..." value="{{ Arr::get($oneDrive->settings, 'image_hosting_path') }}">
        </div>
    </div>
    <div class="row">
        <label class="form-control-label col-sm-2">是否设为默认</label>
        <div class="form-group col-sm-10">
            <input type="hidden" name="is_default" value="0">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="is_default" id="is_default"
                    data-on-text="开启" data-off-text="关闭"
                    @if ($oneDrive->is_default) checked @endif value="{{ $oneDrive->is_default ? '1' : '0'}}">
                <label class="custom-control-label" for="is_default"></label>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="encrypt_path">加密</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="encrypt_path" name="encrypt_path" rows="5">{{ Arr::get($oneDrive->settings, 'encrypt_path', '') }}</textarea>
            <span class="form-text text-danger">填写需要加密的文件或文件夹路径，格式如： /path1/xxx/ /path2/xxx/ password1,/path3/xxx/ /path4/xxx/ password2 (以OneDrive根目录为基础)</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2" for="">加密选项</label>
        <div class="col-sm-10">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input"  id="c1" name="settings[encrypt_option][]" value="list" @if (in_array('list', Arr::get($oneDrive->settings, 'encrypt_option', []))) checked @endif>
                <label class="custom-control-label" for="c1">加密目录列表</label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c2" name="settings[encrypt_option][]" value="show" @if (in_array('show', Arr::get($oneDrive->settings, 'encrypt_option', []))) checked @endif>
                <label class="custom-control-label" for="c2">加密文件预览</label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c3" name="settings[encrypt_option][]" value="download" @if (in_array('download', Arr::get($oneDrive->settings, 'encrypt_option', []))) checked @endif>
                <label class="custom-control-label" for="c3">加密文件下载</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c4" name="settings[encrypt_option][]" value="view" @if (in_array('view', Arr::get($oneDrive->settings, 'encrypt_option', []))) checked @endif>
                <label class="custom-control-label" for="c4">加密图片查看页</label>
            </div>
            <span class="form-text text-danger">选择需要加密强度，默认加密列表</span>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <p class="pull-right text-danger">展示的文件后缀, 以空格分开</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="image">图片</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="image" name="image" value="{{ Arr::get($oneDrive->settings, 'image') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="video">视频</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="video" name="video" value="{{ Arr::get($oneDrive->settings, 'video') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="dash">Dash视频</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="dash" name="dash" value="{{ Arr::get($oneDrive->settings, 'dash') }}">
            <span class="form-text text-danger">不支持个人版账户</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="audio">音频</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="audio" name="audio" value="{{ Arr::get($oneDrive->settings, 'audio') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="doc">文档</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="doc" name="doc" value="{{ Arr::get($oneDrive->settings, 'doc') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="code">代码</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="code" name="code" value="{{ Arr::get($oneDrive->settings, 'code') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="stream">文件流</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="stream" name="stream" value="{{ Arr::get($oneDrive->settings, 'stream') }}">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </div>
</form>
@stop

@section('js')
<script type="text/javascript">
    $(function() {
        $('input[type="checkbox"]').on('click', function (e) {
            if (e.toElement.value == 'on' || e.toElement.value == 0) {
                e.toElement.value = 1;
            } else {
                e.toElement.value = 0;
            }
        });
    });
</script>
@endSection