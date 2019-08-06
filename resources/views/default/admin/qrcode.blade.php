@extends('default.layouts.admin')
@section('title','绑定二步验证')
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
<div class="row">
  <div class="col-xs-6 col-md-3 center-block">
    <a class="thumbnail">
      <img src="{{ $qrcode }}" alt="" class="center-block">
    </a>
  </div>
</div>

<form action="/" method="post">
    Type your code: <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="text" name="code">
    <input type="submit" value="check">
</form>

@stop