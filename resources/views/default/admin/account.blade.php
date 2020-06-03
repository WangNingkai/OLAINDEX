@php
    /* @var $accounts \App\Models\Account[]*/
@endphp
@extends('default.layouts.main')
@section('title', '账号列表')
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">账号列表</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">类型</th>
                    <th scope="col">备注</th>
                    <th scope="col">状态</th>
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
                        <td>
                            <div class="btn-group" role="group" aria-label="account action">
                                <button type="button" class="btn btn-sm btn-info">操作</button>
                                <div class="btn-group" role="group">
                                    <button id="actionAccount" type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="actionAccount">
                                        <a class="dropdown-item" href="#">查看详情</a>
                                        <a class="dropdown-item" href="#">删除</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
