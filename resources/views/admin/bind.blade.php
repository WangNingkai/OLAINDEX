@extends('layouts.admin')
@section('title','绑定设置')
@section('content')
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="email">已绑定账户</label>
            <input type="text" class="form-control" id="email" name="email"
                   value="{{ \App\Helpers\Tool::getBindAccount() }}" disabled>
        </div>
        <button type="submit" class="btn btn-primary">解绑/绑定</button>
    </form>
@stop
