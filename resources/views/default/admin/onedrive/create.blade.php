@extends('default.layouts.admin')
@section('title','新增 OneDrive')
@section('content')
<form action="{{ route('admin.onedrive.store') }}" method="POST">
    @csrf
    <div class="form-group row">
        <label for="name" class="col-sm-2 col-form-label">名称</label>
        <div class="col-sm-10">
            <input type="text" name="name" class="form-control" id="name" placeholder="名称...">
        </div>
    </div>
    <div class="form-group row">
        <label for="root" class="col-sm-2 col-form-label">根目录</label>
        <div class="col-sm-10">
            <input type="text" name="root" class="form-control" id="root" placeholder="根目录...">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">创建</button>
        </div>
    </div>
</form>
@stop