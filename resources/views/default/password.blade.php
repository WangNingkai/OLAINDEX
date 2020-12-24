@extends('default.layouts.main')
@section('title','文件夹密码')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="ri-information-fill"></i> {!! setting('encrypt_tip','此文件夹或文件受到保护，您需要提供访问密码才能查看') !!}
                </div>
                <div class="card-body">
                    <form action="{{ route('drive.decrypt') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label class="form-control-label" for="password"><i class="ri-lock-fill"></i> 输入密码 </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <input type="hidden" name="hash" value="{{ $hash }}">
                            <input type="hidden" name="query" value="{{ $item['name'] }}">
                            <input type="hidden" name="redirect" value="{{ $redirect }}">
                        </div>
                        <button type="submit" class="btn btn-primary">确认</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
