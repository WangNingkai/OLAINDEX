@extends('default.layouts.admin')
@section('title', auth('admin')->user()->is_tfa ? '解绑二步验证' : '绑定二步验证')
@section('css')
<style>
    .center-block {
        display: block;
        margin: 0 auto;
    }
</style>
@stop
@section('content')
@includeWhen(!empty(session('message')), 'default.widgets.success')
@includeWhen($errors->isNotEmpty(), 'default.widgets.errors')
@if (!auth('admin')->user()->is_tfa)
<div class="row">
    <div class="center-block">
        <a class="thumbnail">
            <img src="{{ $qrcode }}" alt="" class="center-block">
        </a>
    </div>
</div>  
@endif
<div class="row">
    <div class="col-sm-6 center-block">
        <form action="{{ !auth('admin')->user()->is_tfa ? route('admin.google2fa.bind') : route('admin.google2fa.unbind') }}" method="POST">
            @csrf
            @if (!auth('admin')->user()->is_tfa)
            <input type="hidden" name="tfa_secret" value="{{ $secret }}" />
            @endif 
            <div class="form-group row">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">二步验证码: </span>
                    </div>
                    <input type="text" class="form-control" name="code" aria-label="Amount (to the nearest dollar)">
                    <div class="input-group-append">
                        <input type="submit" value="提交" class="input-group-text">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop