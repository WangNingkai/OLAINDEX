@extends('layouts.main')
@section('title',$file['name'])
@section('css')

@stop
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">
            {{ $file['name'] }}
        </div>
        <div class="card-body">
            <div class="text-center"><a href="{{ $file['path'] }}" class="btn btn-success"><i class="fa fa-download"></i> 下载</a></div>
            <hr>
            <div class="text-center">
                <video  width="100%" height="100%" preload controls poster="{{ $file['thumb']['large']['url'] }}">
                    <source src="{{ $file['downloadUrl'] }}" type="video/mp4">
                </video>
            </div>
            <hr>
            <div class="form-group">
                <label class="control-label">下载链接</label>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <input type="text" id="link1" class="form-control" aria-label="Amount (to the nearest dollar)" value="{{ $file['path'] }}">
                        <div class="input-group-append">
                            <a href="javascript:void(0)" style="text-decoration: none" data-toggle="tooltip"
                               data-placement="right" data-clipboard-target="#link1" class="clipboard"><span class="input-group-text">复制</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
@stop
