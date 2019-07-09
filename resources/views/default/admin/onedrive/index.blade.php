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
                    <form action="{{ route('admin.onedrive.unbind', ['onedrive' => $oneDrive->id]) }}" method="POST" style="display: none;">
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
                            <a class="dropdown-item" href="{{ route('admin.onedrive.clear', ['onedrive' => $oneDrive->id]) }}">清理</a>
                            <a class="dropdown-item" href="{{ route('admin.onedrive.refresh', ['onedrive' => $oneDrive->id]) }}">刷新</a>
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
            swal({
                title: '确定删除吗？',
                text: "删除后无法恢复",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定删除',
                cancelButtonText: '取消',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
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
                } else if (result.dismiss === swal.DismissReason.cancel) {
                    swal('已取消', '', 'error');
                }
            })
        });

        $(".btn-unbind").click(function (e) {
            e.preventDefault();
            var $this = this;
            swal({
                title: '确定解绑吗？',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定解绑',
                cancelButtonText: '取消',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $($this).next().submit();
                } else if (result.dismiss === swal.DismissReason.cancel) {
                    swal('已取消', '', 'info');
                }
            })
        })
    });
</script>
@endSection