@extends('mdui.layouts.main')
@section('title',$file['name'])
@section('css')
    <link rel="stylesheet" href="https://cdn.staticfile.org/aplayer/1.10.1/APlayer.min.css">
@stop
@section('js')
    <script src="https://cdn.staticfile.org/aplayer/1.10.1/APlayer.min.js"></script>
    <script>
        $(function () {
            const ap = new APlayer({
                container: document.getElementById('audio-player'),
                audio: [{
                    name: '{{ $file['name'] }}',
                    artist: '{{ $file['name'] }}',
                    url: "{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}",
                    cover: 'cover.jpg'
                }]
            });
            // 防止出现401 token过期
            ap.on('error', function () {
                console.log('获取资源错误，开始重新加载！');
                let last = dp.audio.currentTime;
                ap.audio.src = "{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}";
                ap.audio.load();
                ap.audio.currentTime = last;
                ap.play();
            });
            // 如果是播放状态 & 没有播放完 每25分钟重载视频防止卡死
            setInterval(function () {
                if (!ap.audio.paused && !ap.audio.ended) {
                    console.log('开始重新加载！');
                    let last = ap.audio.currentTime;
                    ap.audio.src = "{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}";
                    ap.audio.load();
                    ap.audio.currentTime = last;
                    ap.play();
                }
            }, 1000 * 60 * 25)
        });
    </script>
@stop
@section('content')

    <div class="mdui-container-fluid">
        <div class="blank-div"></div>
        <div class="mdui-typo mdui-m-y-2">
            <div class="mdui-typo-subheading-opacity">{{ $file['name'] }}</div>
        </div>
        <div class="mdui-typo mdui-shadow-3 mudi-center" id="audio-player"></div>
        <br>
        <div class="mdui-textfield">
            <label class="mdui-textfield-label" for="downloadUrl">下载地址</label>
            <input class="mdui-textfield-input" type="text" id="downloadUrl"
                   value="{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}"/>
        </div>
    </div>
    <a href="{{ $file['download'] }}" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i
            class="mdui-icon material-icons">file_download</i></a>
@stop
