@extends('default.layouts.admin')
@section('title','二步验证')
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
@include('default.widgets.google2fa')
@stop