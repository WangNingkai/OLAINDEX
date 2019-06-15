@extends('mdui.layouts.main')
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

    <div class="mdui-container-fluid">
        <div class="mdui-typo mdui-m-y-2">
            <div class="mdui-typo-subheading-opacity">{{ $file['name'] }}</div>
        </div>
        <div class="mudi-center" id="dash-player"></div>
        <br>
        <p class="text-danger">如无法播放或格式不受支持，推荐使用 <a href="https://pan.lanzou.com/b112173" target="_blank">potplayer</a>
            播放器在线播放</p>
        <div class="mdui-textfield">
            <label class="mdui-textfield-label" for="downloadUrl">下载地址</label>
            <input class="mdui-textfield-input" type="text" id="downloadUrl"
                   value="{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}"/>
        </div>
    </div>
    <a href="{{ $file['download'] }}" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i
            class="mdui-icon material-icons">file_download</i></a>
@stop
