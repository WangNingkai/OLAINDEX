@extends('layouts.admin')
@section('title','基础设置')
@section('content')
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="name">站点名称</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ \App\Helpers\Tool::config('name') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="theme">站点主题</label>
            <select class="custom-select" name="theme" id="theme">
                <option value="simplex"  @if(\App\Helpers\Tool::config('theme') == 'simplex') selected @endif>Simplex</option>
                <option value="materia"  @if(\App\Helpers\Tool::config('theme') == 'materia') selected @endif>Materia</option>
                <option value="minty"  @if(\App\Helpers\Tool::config('theme') == 'minty') selected @endif>Minty</option>
                <option value="flatly"  @if(\App\Helpers\Tool::config('theme') == 'flatly') selected @endif>Flatly</option>
                <option value="cosmo"  @if(\App\Helpers\Tool::config('theme') == 'cosmo') selected @endif>Cosmo</option>
                <option value="lumen"  @if(\App\Helpers\Tool::config('theme') == 'lumen') selected @endif>Lumen</option>
                <option value="lux"  @if(\App\Helpers\Tool::config('theme') == 'lux') selected @endif>Lux</option>
                <option value="darkly"  @if(\App\Helpers\Tool::config('theme') == 'darkly') selected @endif>Darkly</option>
                <option value="solar"  @if(\App\Helpers\Tool::config('theme') == 'solar') selected @endif>Solar</option>
                <option value="slate"  @if(\App\Helpers\Tool::config('theme') == 'slate') selected @endif>Slate</option>
                <option value="superhero"  @if(\App\Helpers\Tool::config('theme') == 'superhero') selected @endif>Superhero</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="root">OneDrive根目录</label>
            <input type="text" class="form-control" id="root" name="root" value="{{ \App\Helpers\Tool::config('root') }}">
        </div>

        <div class="form-group">
            <label class="form-control-label" for="expires">缓存时间(分钟)</label>
            <input type="text" class="form-control" id="expires" name="expires" value="{{ \App\Helpers\Tool::config('expires') }}">
        </div>
        <div class="form-group">
            <label class="form-control-label">开启图床</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting1" name="image_hosting" class="custom-control-input" @if(\App\Helpers\Tool::config('image_hosting') == 1) checked @endif value="1">
                <label class="custom-control-label" for="image_hosting1">开启</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting0" name="image_hosting" class="custom-control-input" @if(\App\Helpers\Tool::config('image_hosting') == 0) checked @endif value="0" >
                <label class="custom-control-label" for="image_hosting0">关闭</label>
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="image_hosting_path">图床地址</label>
            <input type="text" class="form-control" id="image_hosting_path" name="image_hosting_path" value="{{ \App\Helpers\Tool::config('image_hosting_path') }}">
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
