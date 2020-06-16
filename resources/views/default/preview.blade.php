@extends('default.layouts.main')
@section('title','OLAINDEX')
@section('content')
    @include('default.components.breadcrumb',['hash' => $hash, 'path' => $path])
    <div class="card border-light mb-3">
        <div class="card-header">{{ $file['name'] }}</div>
        <div class="card-body">
            @include('default.components.preview.' . $show,['file' => $file, 'show' => $show])
            <br>
            <label class="control-label">复制链接</label>
            <div class="form-group">
                <div class="input-group mb-3">
                    <label for="link1"></label>
                    <input type="text" id="link1" class="form-control"
                           value="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', $path)),'download' => 1]) }}">
                    <div class="input-group-append">
                        <a href="javascript:void(0)" style="text-decoration: none" data-toggle="tooltip"
                           data-placement="right" data-clipboard-target="#link1" class="clipboard"><span
                                class="input-group-text">复制</span></a>
                    </div>
                </div>
            </div>
            <div class="text-center"><a
                    href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', $path)),'download' => 1]) }}"
                    class="btn btn-success"><i class="fa fa-download"></i> 下载</a></div>
        </div>
    </div>
@stop
