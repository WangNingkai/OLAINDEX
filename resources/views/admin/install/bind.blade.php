@extends('admin.layouts.main')
@section('title', '账号绑定')
@section('content')
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    OLAINDEX
                </div>
                <h2 class="page-title">
                    账号绑定
                </h2>
            </div>
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">账号绑定</h3>
                    <small class="text-danger">请确认以下信息</small>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="form-label" for="clientId">client_id </label>
                        <input type="text" class="form-control" id="clientId" name="clientId"
                               value="{{ $clientId }}" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="clientSecret">client_secret </label>
                        <input type="text" class="form-control" id="clientSecret" name="clientSecret"
                               value="{{ substr_replace($clientSecret, '*****', 3, 5)}}"
                               readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="redirectUri">redirect_uri </label>
                        <input type="text" class="form-control" id="redirectUri" name="redirectUri"
                               value="{{ $redirectUri }}" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="accountType">账号类型 </label>
                        <input type="text" class="form-control" id="accountType" name="accountType"
                               value="{{ $accountType }}" readonly>
                    </div>
                    <form id="bind-form" action="{{ route('bind') }}" method="POST"
                          class="invisible">
                        <input type="hidden" name="clientId" value="{{ $clientId }}">
                        <input type="hidden" name="clientSecret" value="{{ $clientSecret }}">
                        <input type="hidden" name="redirectUri" value="{{ $redirectUri }}">
                        <input type="hidden" name="accountType" value="{{ $accountType }}">
                        @csrf
                    </form>
                    <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('bind-form').submit();"
                       class="btn btn-info">绑定</a>
                    <a href="{{ route('reset') }}" class="btn btn-danger">返回更改</a>
                </div>
            </div>
        </div>
    </div>
@stop
