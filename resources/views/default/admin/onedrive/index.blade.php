@extends('default.layouts.admin')
@section('title','OneDrive列表')
@section('content')
@includeWhen(!empty(session('message')), 'default.widgets.success')
@includeWhen($errors->isNotEmpty(), 'default.widgets.errors')
<table class="table table-hover">
    <thead class="thead-light">
        <tr>
            <th scope="col">#</th>
            <th scope="col">名称</th>
            <th scope="col">根目录</th>
            <th scope="col">app版本</th>
            <th scope="col">是否默认</th>
            <th scope="col">是否绑定</th>
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
            <td>{{ $oneDrive->is_binded ? '是' : '否' }}</td>
            <td>
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <a href="{{ route('admin.onedrive.edit', ['onedrive' => $oneDrive->id]) }}" class="btn btn-primary">编辑</a>
                    <button type="button" data-id="{{ $oneDrive->id }}" class="btn btn-primary btn-delete">删除</button>
                @if ($oneDrive->is_binded)
                    <button type="button" class="btn btn-primary btn-unbind">解绑</button>
                    <form id="onedrive-unbind-form" action="{{ route('admin.onedrive.unbind', ['onedrive' => $oneDrive->id]) }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('admin.onedrive.bind', ['onedrive' => $oneDrive->id]) }}" class="btn btn-primary">绑定</a>                    
                @endif
                    <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            缓存
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <a class="dropdown-item" data-id="{{ $oneDrive->id }}" href="#">清理</a>
                            <a class="dropdown-item" data-id="{{ $oneDrive->id }}" href="#">刷新</a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
    @else
    <td colspan="7" class="text-center">暂无数据</td>
    @endif
</table>
<div class="float-right">
    <a href="{{ route('admin.onedrive.create') }}" class="btn btn-primary">新增</a>
</div>
@stop
@section('js')
<script>
    $(function () {
        $(".btn-delete").click(function (e) {
            var $this = this;
            var confirmResult = confirm('是否确定删除!')
            if (confirmResult) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "DELETE",
                    url: "{{ url()->current()  . '/' }}" + $this.dataset.id,
                    success: function () {
                        console.log($($this).parents("tr").remove());
                    }
                });
            }
        });

        $(".btn-unbind").click(function (e) {
            e.preventDefault();
            var confirmUnbindResult = confirm('是否确定解除绑定!');

            if (confirmUnbindResult) {
                document.getElementById('onedrive-unbind-form').submit();
            }
        })
    });
</script>
@endSection