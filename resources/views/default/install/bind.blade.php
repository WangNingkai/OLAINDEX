@extends('default.layouts.common')
@section('title','绑定帐号')
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">绑定帐号
            <small class="text-danger">请确认以下信息</small>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-control-label" for="client_id">client_id </label>
                <input type="text" class="form-control" id="client_id" name="client_id"
                       value="{{ setting('client_id') }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="client_secret">client_secret </label>
                <input type="text" class="form-control" id="client_secret" name="client_secret"
                       value="{{ substr_replace(setting('client_secret'),'*****',3,5)}}"
                       disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="redirect_uri">redirect_uri </label>
                <input type="text" class="form-control" id="redirect_uri" name="redirect_uri"
                       value="{{ setting('redirect_uri') }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="account_type">账号类型 </label>
                <input type="text" class="form-control" id="account_type" name="account_type"
                       value="{{ setting('account_type') }}" disabled>
            </div>
            <form id="bind-form" action="{{ route('bind') }}" method="POST"
                  class="invisible">
                @csrf
            </form>
            <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('bind-form').submit();"
               class="btn btn-info">绑定</a>
            <a href="{{ route('reset') }}" class="btn btn-danger">返回更改</a>
        </div>
    </div>
@stop
