@extends('admin.layouts.main')
@section('title', '短链管理')
@section('content')
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    列表
                </div>
                <h2 class="page-title">
                    短链管理
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <span class="d-none d-sm-inline">
                        <a href="javascript:void(0);" class="btn btn-danger delete-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"
                                                                                      fill="none"/><line x1="4" y1="7"
                                                                                                         x2="20"
                                                                                                         y2="7"/><line
                                    x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/><path
                                    d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path
                                    d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                            清空
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                        <tr>
                            <th class="row">
                                No.
                            </th>
                            <th>源链</th>
                            <th>短链</th>
                            <th>添加时间</th>
                            <th class="text-end">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(blank($urls))
                            <tr>
                                <td colspan="5" class="text-center">
                                    Ops! 暂无资源
                                </td>
                            </tr>
                        @else
                            @foreach($urls as $url)
                                <tr>
                                    <th>{{ $url->id }}</th>
                                    <td><a href="{{ $url->original_url }}"
                                           title="{{ $url->original_url }}">{{ str_limit($url->original_url,64) }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('short',[ 'code' => $url->short_code ]) }}">{{ route('short',[ 'code' => $url->short_code ]) }}</a>
                                    </td>
                                    <td>{{ $url->created_at }}</td>
                                    <td class="text-end" data-id="{{ $url->id  }}">
                                        <a href="javascript:void(0);" class="btn btn-danger delete">
                                            删除
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $urls->links('admin.components.page') }}
                </div>
            </div>
        </div>
    </div>
@stop
@push('scripts')
    <script>
        $(function() {
            $('.delete').on('click', function(e) {
                let _id = $(this).parent().attr('data-id')
                Swal.fire({
                    title: '确定删除吗?',
                    text: '删除后无法恢复!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                }).then((result) => {
                    if (result.value) {
                        axios.post('/admin/url/delete/' + _id)
                            .then(function(response) {
                                let data = response.data
                                if (data.error === '') {
                                    Swal.fire({
                                        title: '操作成功',
                                        text: '删除成功',
                                        icon: 'success',
                                    }).then(() => {
                                        window.location.reload()
                                    })
                                }
                            })
                            .catch(function(error) {
                                console.log(error)
                            })
                    }
                })

            })
            $('.delete-all').on('click', function(e) {
                Swal.fire({
                    title: '确定清空吗?',
                    text: '清空后无法恢复!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                }).then((result) => {
                    if (result.value) {
                        axios.post('/admin/url/empty')
                            .then(function(response) {
                                let data = response.data
                                if (data.error === '') {
                                    Swal.fire({
                                        title: '操作成功',
                                        text: '清空成功',
                                        icon: 'success',
                                    }).then(() => {
                                        window.location.reload()
                                    })
                                }
                            })
                            .catch(function(error) {
                                console.log(error)
                            })
                    }
                })

            })
        })
    </script>
@endpush
