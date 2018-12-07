@extends('default.layouts.main')
@section('title','提醒')
@section('content')
    <div class="jumbotron">
        <h1 class="display-4">OLAINDEX</h1>
        <p class="lead">Another OneDrive Directory Index.</p>
        <hr class="my-4">
        <p class="lead">
            <a class="btn btn-primary btn-lg" href="{{ route('home') }}" role="button">返回首页</a>
        </p>
    </div>
@stop
