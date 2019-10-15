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
@include('default.widgets.google2fa')
@stop