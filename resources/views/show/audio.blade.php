@extends('layouts.main')
@section('title',$file['name'])
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aplayer@1.10.1/dist/APlayer.min.css">
@stop
@section('content')
    @include('breadcrumb')
    <div class="card border-light mb-3">
        <div class="card-header">
            {{ $file['name'] }}
        </div>
        <div class="card-body">
            <div class="text-center"><a href="{{ route('download',urlencode($origin_path)) }}" class="btn btn-success"><i
                        class="fa fa-download"></i> 下载</a></div>
            <hr>
            <div class="text-center">
                <div id="aplayer"></div>
            </div>
            <hr>
            <label class="control-label">下载链接</label>
            <div class="form-group">
                <div class="input-group mb-3">
                    <input type="text" id="link1" class="form-control" aria-label="Amount (to the nearest dollar)"
                           value="{{ route('download',urlencode($origin_path)) }}">
                    <div class="input-group-append">
                        <a href="javascript:void(0)" style="text-decoration: none" data-toggle="tooltip"
                           data-placement="right" data-clipboard-target="#link1" class="clipboard"><span
                                class="input-group-text">复制</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/aplayer@1.10.1/dist/APlayer.min.js"></script>
    <script>
        const ap = new APlayer({
            container: document.getElementById("aplayer"),
            audio: [{
                name: "{{ $file['name'] }}",
                artist: 'unknown',
                url: "{{ route('download',urlencode($origin_path)) }}",
                cover: "https://i.loli.net/2018/10/28/5bd571ce90e33.png"
            }]
        });
    </script>
@stop
