@push('stylesheet')
    <link rel="stylesheet" href="https://cdn.staticfile.org/aplayer/1.10.1/APlayer.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.staticfile.org/aplayer/1.10.1/APlayer.min.js"></script>
    <script>
        $(function() {
            const ap = new APlayer({
                container: document.getElementById('audio-player'),
                audio: [{
                    name: '{{ $file['name'] }}',
                    artist: '{{ $file['name'] }}',
                    url: "{!! $file['download'] !!}",
                    cover: 'cover.jpg',
                }],
            })
            // 防止出现401 token过期
            ap.on('error', function() {
                console.log('获取资源错误，开始重新加载！')
                let last = dp.audio.currentTime
                ap.audio.src = "{!! $file['download'] !!}"
                ap.audio.load()
                ap.audio.currentTime = last
                ap.play()
            })
            // 如果是播放状态 & 没有播放完 每25分钟重载视频防止卡死
            setInterval(function() {
                if (!ap.audio.paused && !ap.audio.ended) {
                    console.log('开始重新加载！')
                    let last = ap.audio.currentTime
                    ap.audio.src = "{!! $file['download'] !!}"
                    ap.audio.load()
                    ap.audio.currentTime = last
                    ap.play()
                }
            }, 1000 * 60 * 25)
        })
    </script>
@endpush
<div class="mdui-center">
    <div id="audio-player"></div>
</div>
