@extends('default.layouts.main')
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
    @include('default.breadcrumb')
    <div class="card border-light mb-3">
        <div class="card-header">
            {{ $file['name'] }}
        </div>
        <div class="card-body">
            <div class="blank-div"></div>
            <div class="text-center">
                <div id="audio-player"></div>
            </div>
            <br>
            <div class="text-center"><a href="{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}"
                                        class="btn btn-success"><i
                        class="fa fa-download"></i> 下载</a></div>
            <br>
            <label class="control-label">下载链接</label>
            <div class="form-group">
                <div class="input-group mb-3">
                    <input type="text" id="link1" class="form-control"
                           value="{{ route('download',\App\Utils\Tool::encodeUrl($originPath)) }}">
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

