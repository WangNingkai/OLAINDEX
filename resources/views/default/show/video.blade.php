@extends('default.layouts.main')
@section('title',$file['name'])
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/plyr@3/dist/plyr.min.css">
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/plyr@3/dist/plyr.min.js"></script>
    <script>
        const player = new Plyr('#player', {
            iconUrl: "https://cdn.jsdelivr.net/npm/plyr@3/dist/plyr.svg",
        });
    </script>
@stop
@section('content')
    @include('default.breadcrumb')
    <div class="card border-light mb-3">
        <div class="card-header">{{ $file['name'] }}</div>
        <div class="card-body">
            <div class="text-center">
                <a href="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}" class="btn btn-success">
                    <i class="fa fa-download"></i>下载</a>
            </div>
            <br>
            <div class="text-center">
                <div id="video-player">
                    <video crossorigin playsinline controls poster="{!! $file['thumb'] !!}" id="player">
                        <source src="{!! $file['download'] !!}" type="video/mp4">
                    </video>
                </div>
                <br>
                <label class="control-label">下载链接</label>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <input type="text" id="link1" class="form-control"
                               value="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}">
                        <div class="input-group-append">
                            <a href="javascript:void(0)" style="text-decoration: none" data-toggle="tooltip"
                               data-placement="right" data-clipboard-target="#link1" class="clipboard">
                                <span class="input-group-text">复制</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

