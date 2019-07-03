@extends('default.layouts.admin')
@section('title','基础设置')
@section('content')
@includeWhen(!empty(session('message')), 'default.widgets.success')
@includeWhen($errors->isNotEmpty(), 'default.widgets.errors')
<form action="{{ route('admin.basic') }}" method="post">
    @csrf
    <div class="form-group row">
        <label class="form-control-label col-sm-2 col-form-label" for="name">站点名称</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="name" name="name" value="{{ $admin->name }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2 col-form-label" for="theme">站点主题</label>
        <div class="col-sm-10">
            <select class="custom-select" name="theme" id="theme">
                @foreach( \App\Helpers\Constants::SITE_THEME as $name => $theme)
                <option value="{{ $theme }}" @if($admin->theme == $theme)
                    selected
                    @endif>{{ $name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2">是否开启图床</label>
        <div class="col-sm-10">
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting1" name="image_hosting" class="custom-control-input"
                    @if($admin->image_hosting == 'enabled') checked @endif value="enabled">
                <label class="custom-control-label" for="image_hosting1">开启 &nbsp;&nbsp;</label>

            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting0" name="image_hosting" class="custom-control-input"
                    @if($admin->image_hosting == 'disabled') checked @endif value="disabled">
                <label class="custom-control-label" for="image_hosting0">关闭 &nbsp;&nbsp;</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting2" name="image_hosting" class="custom-control-input"
                    @if($admin->image_hosting == 'admin_enabled') checked @endif value="admin_enabled">
                <label class="custom-control-label" for="image_hosting2">仅管理员开启 </label>
            </div>
        </div>
    </div>
    <div class="row">
        <label class="form-control-label col-sm-2">是否将图床设为首页</label>
        <div class="form-group col-sm-10">
            <input type="hidden" name="is_image_home" value="0">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="is_image_home" id="customSwitch1"
                    data-on-text="开启" data-off-text="关闭"
                    @if($admin->is_image_home) checked @endif value="{{ $admin->is_image_home ? '1' : '0'}}">
                <label class="custom-control-label" for="customSwitch1"></label>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2" for="hotlink_protection">防盗链</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="hotlink_protection" name="hotlink_protection"
                value="{{ $admin->hotlink_protection }}">
            <span class="form-text text-danger">留空则不开启。白名单链接以空格分开（此处采用 Http Referer 防盗链机制，如需加强请自行从服务器层面配置）</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2" for="copyright">自定义版权显示</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="copyright" name="copyright" value="{{ $admin->copyright }}">
            <span class="form-text text-danger">留空则不显示。使用markdown格式表示 如：Made by [xxx](https://xxx)</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2" for="statistics">统计代码</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="statistics" name="statistics" value="{{ $admin->statistics }}">
            <span class="form-text text-danger">js统计代码</span>
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
        $('#customSwitch1').on('click', function (e) {
            if (e.toElement.value == 'on' || e.toElement.value == 0) {
                e.toElement.value = 1;
            } else {
                e.toElement.value = 0;
            }
        });
    });

</script>
@endSection