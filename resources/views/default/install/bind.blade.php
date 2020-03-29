@extends('default.layouts.main')
@section('title','绑定帐号')
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">绑定帐号
            <small class="text-danger">请确认以下信息</small>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-control-label" for="clientId">client_id </label>
                <input type="text" class="form-control" id="clientId" name="clientId"
                       value="{{ $clientId }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="clientSecret">client_secret </label>
                <input type="text" class="form-control" id="clientSecret" name="clientSecret"
                       value="{{ substr_replace($clientSecret, '*****', 3, 5)}}"
                       disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="redirectUri">redirect_uri </label>
                <input type="text" class="form-control" id="redirectUri" name="redirectUri"
                       value="{{ $redirectUri }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="accountType">账号类型 </label>
                <input type="text" class="form-control" id="accountType" name="accountType"
                       value="{{ $accountType }}" disabled>
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
