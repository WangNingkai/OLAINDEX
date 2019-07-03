@extends('default.layouts.admin')
@section('title','新增 OneDrive')
@section('content')
<form action="{{ route('admin.onedrive.update') }}" method="PUT">
    {{method_field('PUT')}}
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
    <div class="row">
        <label class="form-control-label col-sm-2">是否设为默认</label>
        <div class="form-group col-sm-10">
            <input type="hidden" name="is_default" value="0">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="is_default" id="customSwitch1"
                    data-on-text="开启" data-off-text="关闭"
                    @if($oneDrive->is_default) checked @endif value="{{ $oneDrive->is_default ? '1' : '0'}}">
                <label class="custom-control-label" for="customSwitch1"></label>
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