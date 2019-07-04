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