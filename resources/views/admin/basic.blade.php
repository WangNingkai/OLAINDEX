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
                <option value="materia"  @if(\App\Helpers\Tool::config('theme') == 'materia') selected @endif>Materia</option>
                <option value="minty"  @if(\App\Helpers\Tool::config('theme') == 'minty') selected @endif>Minty</option>
                <option value="flatly"  @if(\App\Helpers\Tool::config('theme') == 'flatly') selected @endif>Flatly</option>
                <option value="cosmo"  @if(\App\Helpers\Tool::config('theme') == 'cosmo') selected @endif>Cosmo</option>
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
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
