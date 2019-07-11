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