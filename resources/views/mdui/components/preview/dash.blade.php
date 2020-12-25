@push('stylesheet')
    <link rel="stylesheet" href="https://cdn.staticfile.org/dplayer/1.25.0/DPlayer.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.staticfile.org/dashjs/3.1.1/dash.all.min.js"></script>
    <script src="https://cdn.staticfile.org/dplayer/1.25.1/DPlayer.min.js"></script>
    <script>
        const dp = new DPlayer({
            container: document.getElementById('dash-player'),
            lang: 'zh-cn',
            video: {
                url: '{!! $file["dash"] !!}',
                pic: "{!! $file['thumb'] !!}",
                type: 'dash',
            },
        })
        // 防止出现401 token过期
        dp.on('error', function() {
            console.log('获取资源错误，开始重新加载！')
            let last = dp.video.currentTime
            dp.video.src = "{!! $file['dash'] !!}"
            dp.video.load()
            dp.video.currentTime = last
            dp.play()
        })
        // 如果是播放状态 & 没有播放完 每25分钟重载视频防止卡死
        setInterval(function() {
            if (!dp.video.paused && !dp.video.ended) {
                console.log('开始重新加载！')
                let last = dp.video.currentTime
                dp.video.src = "{!! $file['dash'] !!}"
                dp.video.load()
                dp.video.currentTime = last
                dp.play()
            }
        }, 1000 * 60 * 25)
    </script>
@endpush
<div class="mdui-center">
    <div id="dash-player"></div>
    <p class="text-danger">如无法播放或格式不受支持，推荐使用 PotPlayer 播放器在线播放</p>
</div>
