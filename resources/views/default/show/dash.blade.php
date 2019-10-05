@extends('default.layouts.main')
@section('title',$file['name'])
@section('css')
    <link rel="stylesheet" href="https://cdn.staticfile.org/dplayer/1.25.0/DPlayer.min.css">
@stop
@section('js')
    <script src="https://cdn.staticfile.org/dashjs/2.9.3/dash.all.min.js"></script>
    <script src="https://cdn.staticfile.org/dplayer/1.25.0/DPlayer.min.js"></script>
    <script>
        const dp = new DPlayer({
            container: document.getElementById('dash-player'),
            lang: 'zh-cn',
            video: {
                url: '{!! $file["dash"] !!}',
                pic: "{!! $file['thumb'] !!}",
                type: 'dash'
            }
        });
        // 防止出现401 token过期
        dp.on('error', function () {
            console.log('获取资源错误，开始重新加载！');
            let last = dp.video.currentTime;
            dp.video.src = "{!! $file['dash'] !!}";
            dp.video.load();
            dp.video.currentTime = last;
            dp.play();
        });
        // 如果是播放状态 & 没有播放完 每25分钟重载视频防止卡死
        setInterval(function () {
            if (!dp.video.paused && !dp.video.ended) {
                console.log('开始重新加载！');
                let last = dp.video.currentTime;
                dp.video.src = "{!! $file['dash'] !!}";
                dp.video.load();
                dp.video.currentTime = last;
                dp.play();
            }
        }, 1000 * 60 * 25)
    </script>
@stop
@section('content')
    @include('default.breadcrumb')
    <div class="card border-light mb-3">
        <div class="card-header">{{ $file['name'] }}</div>
        <div class="card-body">
            <div class="text-center">
                <div id="dash-player"></div>
                <br>
                <div class="text-center">
                    <a href="{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}" class="btn btn-success">
                        <i class="fa fa-download"></i>下载</a>
                </div>
                <br>
                <p class="text-danger">如无法播放或格式不受支持，推荐使用 <a href="https://pan.lanzou.com/b112173" target="_blank">potplayer</a>
                    播放器在线播放
                </p>
                <label class="control-label">下载链接</label>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <input type="text" id="link1" class="form-control"
                               value="{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}">
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
