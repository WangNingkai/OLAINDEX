@extends('default.layouts.main')
@section('title', $file['name'])
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dplayer/1.25.0/DPlayer.min.css">
    {{--  <link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">  --}}
@stop
@section('js')
    {{--  <script src="https://unpkg.com/video.js/dist/video.min.js"></script>  --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dplayer/1.25.0/DPlayer.min.js"></script>
    <script>
        $(function () {
            {{--  var options = {};

            var player = videojs('video-player', options, function onPlayerReady() {
            videojs.log('Your player is ready!');

                // In this context, `this` is the player that was created by Video.js.
                this.play();

                // How about an event listener?
                this.on('ended', function() {
                    videojs.log('Awww...over so soon?!');
                });
            });  --}}
            const dp = new DPlayer({
                container: document.getElementById('video-player'),
                lang: 'zh-cn',
                video: {
                    url: "{!! $file['download'] !!}",
                    pic: "{!! $file['thumb'] !!}",
                    type: 'auto'
                },
                {{--  subtitle: {
                    fontSize: '25px',
                    bottom: '10%',
                    color: '#b7daff'
                },
                content: [
                    {
                        text: '字幕',
                        click: (player) => {
                            console.log(player);
                        }
                    }
                ],  --}}
                autoplay: true
            });
            // 防止出现401 token过期
            dp.on('error', function () {
                console.log('获取资源错误，开始重新加载！');
                let last = dp.video.currentTime;
                dp.video.src = "{!! $file['download'] !!}";
                dp.video.load();
                dp.video.currentTime = last;
                dp.play();
            });
            // 如果是播放状态 & 没有播放完 每25分钟重载视频防止卡死
            setInterval(function () {
                if (!dp.video.paused && !dp.video.ended) {
                    console.log('开始重新加载！');
                    let last = dp.video.currentTime;
                    dp.video.src = "{!! $file['download'] !!}";
                    dp.video.load();
                    dp.video.currentTime = last;
                    dp.play();
                }
            }, 1000 * 60 * 25)
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
                {{--  <video
                    id="video-player"
                    class="video-js"
                    controls
                    preload="auto"
                    poster="//vjs.zencdn.net/v/oceans.png"
                    data-setup='{}'>
                <source src="{!! $file['download'] !!}" type="{{ $file['file']['mimeType'] }}"></source>
                <p class="vjs-no-js">
                    To view this video please enable JavaScript, and consider upgrading to a
                    web browser that
                    <a href="https://videojs.com/html5-video-support/" target="_blank">
                    supports HTML5 video
                    </a>
                </p>
                </video>  --}}
                <div id="video-player"></div>
                <br>
                <p class="text-danger">如无法播放或格式不受支持，推荐使用 <a href="https://pan.lanzou.com/b112173" target="_blank">potplayer</a>
                    播放器在线播放
                </p>
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

