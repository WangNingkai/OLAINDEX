@php
    /* @var $accounts \App\Models\Account[]|\Illuminate\Pagination\Paginator*/
@endphp
@extends('default.layouts.main')
@section('title', '账号列表')
@section('content')
    <div class="card mb-3">
        <div class="card-header">账号列表</div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-borderless">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">类型</th>
                    <th scope="col">状态</th>
                    <th scope="col">备注</th>
                    <th scope="col">上次更新</th>
                    <th scope="col">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($accounts as $account)
                    <tr>
                        <th scope="row">{{ $account->id }}</th>
                        <td>{{ $account->accountType }}</td>
                        <td>{!! $account->status ? '<span style="color:green">正常</span>':'<span style="color:red">禁用</span>' !!}</td>
                        <td>{{ $account->remark }}</td>
                        <td>{{ $account->updated_at }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button id="actionAccount" type="button" class="btn btn-primary btn-sm dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">操作
                                </button>
                                <div class="dropdown-menu" aria-labelledby="actionAccount"
                                     data-id="{{ $account->id }}">
                                    <a class="dropdown-item text-primary view_account"
                                       href="javascript:void(0)">查看详情</a>
                                    <a class="dropdown-item text-danger delete_account"
                                       href="javascript:void(0)">删除</a>
                                    <a class="dropdown-item"
                                       href="{{ route('admin.account.config',['id' => $account->id])  }}">设置</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $accounts->links()  }}
        </div>
    </div>
@stop
@section('js')
    @parent
    <script>
        $(function() {
            $('.view_account').on('click', function(e) {
                let account_id = $(this).parent().attr('data-id')
                axios.get('/admin/account/' + account_id)
                    .then(function(response) {
                        console.log(response)
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
                    .then(function() {
                        // always executed
                    })
            })
        })
    </script>
@stop
