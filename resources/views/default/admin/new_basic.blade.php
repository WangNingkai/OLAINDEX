@extends('default.layouts.admin')
@section('title','基础设置')
@section('content')
@includeWhen($errors->isNotEmpty(), 'default.widgets.errors')
<form action="{{ route('admin.basic') }}" method="post" id="test-form">
    @csrf
    <div class="form-group">
        <label class="form-control-label" for="name">站点名称</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $admin->name }}">
    </div>
    <div class="form-group">
        <label class="form-control-label" for="theme">站点主题</label>
        <select class="custom-select" name="theme" id="theme">
            @foreach( \App\Helpers\Constants::SITE_THEME as $name => $theme)
            <option value="{{ $theme }}" @if($admin->theme == $theme)
                selected
                @endif>{{ $name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="form-control-label">是否开启图床</label>
        <div class="custom-control custom-radio">
            <input type="radio" id="image_hosting1" name="image_hosting" class="custom-control-input"
                @if($admin->image_hosting == 'enabled') checked @endif value="enabled">
            <label class="custom-control-label" for="image_hosting1">开启</label>
        </div>
        <div class="custom-control custom-radio">
            <input type="radio" id="image_hosting0" name="image_hosting" class="custom-control-input"
                @if($admin->image_hosting == 'disabled') checked @endif value="disabled">
            <label class="custom-control-label" for="image_hosting0">关闭</label>
        </div>
        <div class="custom-control custom-radio">
            <input type="radio" id="image_hosting2" name="image_hosting" class="custom-control-input"
                @if($admin->image_hosting == 'admin_enabled') checked @endif value="admin_enabled">
            <label class="custom-control-label" for="image_hosting2">仅管理员开启</label>
        </div>
    </div>
    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" name="is_image_home" id="customSwitch1"
                data-on-text="开启" data-off-text="关闭"
                @if($admin->is_image_home) checked @endif value="{{ $admin->is_image_home ? '1' : '0'}}">
            <label class="custom-control-label" for="customSwitch1">是否将图床设为首页</label>
        </div>
    </div>
    <div class="form-group">
        <label class="form-control-label" for="hotlink_protection">防盗链</label>
        <input type="text" class="form-control" id="hotlink_protection" name="hotlink_protection"
            value="{{ $admin->hotlink_protection }}">
        <span class="form-text text-danger">留空则不开启。白名单链接以空格分开（此处采用 Http Referer 防盗链机制，如需加强请自行从服务器层面配置）</span>
    </div>
    <div class="form-group">
        <label class="form-control-label" for="copyright">自定义版权显示</label>
        <input type="text" class="form-control" id="copyright" name="copyright" value="{{ $admin->copyright }}">
        <span class="form-text text-danger">留空则不显示。使用markdown格式表示 如：Made by [xxx](https://xxx)</span>
    </div>
    <div class="form-group">
        <label class="form-control-label" for="statistics">统计代码</label>
        <input type="text" class="form-control" id="statistics" name="statistics" value="{{ $admin->statistics }}">
        <span class="form-text text-danger">js统计代码</span>
    </div>
    <button type="submit" class="btn btn-primary">提交</button>
</form>
@stop
@section('js')
<script type="text/javascript">
    $(function() {
        $('#customSwitch1').on('click', function (e) {
            console.log(e.toElement.value);
            if (e.toElement.value == 'on' || e.toElement.value == 0) {
                e.toElement.value = 1
            } else {
                e.toElement.value = 0
            }
            console.log($("#test-form"));
        });
    });

</script>
@endSection