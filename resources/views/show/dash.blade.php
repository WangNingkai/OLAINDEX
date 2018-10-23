@extends('layouts.main')
@section('title',$file['name'])
@section('css')
    <link class="dplayer-css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.css">
    <style>
        .dplayer-video-wrap .dplayer-video {
            width: 100%;
            height: 640px;
        }
    </style>
@stop
@section('content')
    @include('breadcrumb')
    <div class="card border-light mb-3">
        <div class="card-header">{{ $file['name'] }}</div>
        <div class="card-body">
            <div class="text-center">
                <a href="{{ $file['download'] }}" class="btn btn-success">
                    <i class="fa fa-download"></i>下载</a>
            </div>
            <hr>
            <div class="text-center">
                <div id="dplayer"></div>
                <hr>
                <label class="control-label">下载链接</label>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <input type="text" id="link1" class="form-control" aria-label="Amount (to the nearest dollar)"
                               value="{{ $file['download'] }}">
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
    </div>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/dashjs/dist/dash.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.js"></script>
    <script>
        const dp = new DPlayer({
            container: document.getElementById("dplayer"),  // 播放器容器元素
            autoplay: false,                                // 视频自动播放
            theme: "#b7daff",                              // 主题色
            loop: true,                                     // 视频循环播放
            lang: "zh-cn",                                  // 播放器语言设置
            screenshot: false,                              // 开启截图
            hotkey: true, 	                                // 开启热键
            preload: "auto",                                // 开启预加载
            volume: 0.7,                                  // 默认音量
            // mutex: true,                                    // 互斥，阻止多个播放器同时播放
            video: {
                url: "{!! $file['dash'] !!}",                 // 视频地址
                pic: "{!! $file['thumb'] !!}",                // 视频封面
                type: "dash"
            }
        });
    </script>
@stop
