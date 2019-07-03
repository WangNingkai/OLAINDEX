@extends('default.layouts.admin')
@section('title','OneDrive列表')
@section('content')
<table class="table table-hover">
    <thead class="thead-light">
        <tr>
            <th scope="col">#</th>
            <th scope="col">名称</th>
            <th scope="col">根目录</th>
            <th scope="col">app版本</th>
            <th scope="col">是否默认</th>
            <th scope="col">操作</th>
        </tr>
    </thead>    
    @if ($oneDrives->isNotEmpty())
        <tbody>
        @foreach ($oneDrives as $oneDrive)
        <tr>
            <th scope="row">{{ $oneDrive->id }}</th>
            <td>{{ $oneDrive->name }}</td>
            <td>{{ $oneDrive->root }}</td>
            <td>{{ $oneDrive->app_version }}</td>
            <td>{{ $oneDrive->is_default ? '是' : '否' }}</td>
            <td>
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <button type="button" class="btn btn-primary">编辑</button>
                    <button type="button" class="btn btn-primary">删除</button>

                    <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            缓存
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <a class="dropdown-item" href="#">清理</a>
                            <a class="dropdown-item" href="#">刷新</a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
    @else
        <td colspan="6" class="text-center">暂无数据</td>
    @endif
</table>
<div class="float-right">
    <a href="{{ route('admin.onedrive.create') }}" class="btn btn-primary">新增</a>
</div>
@stop