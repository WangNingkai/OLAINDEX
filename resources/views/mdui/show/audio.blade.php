@extends('mdui.layouts.main')
@section('css')
    <link href="https://cdn.bootcss.com/aplayer/1.10.1/APlayer.min.css" rel="stylesheet">
@stop
@section('js')
    <script src="https://cdn.bootcss.com/aplayer/1.10.1/APlayer.min.js"></script>
    <script>
        const ap = new APlayer({
            container: document.getElementById("aplayer"),
            audio: [{
                name: "{{ $file['name'] }}",
                artist: 'unknown',
                url: "{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}",
                // cover: "https://i.loli.net/2018/10/28/5bd571ce90e33.png"
                cover: "https://i.loli.net/2018/12/07/5c0a12a6b6906.png"
            }]
        });
    </script>
@stop
@section('content')

    <div class="mdui-container-fluid">
        {{--<br>--}}
        <div class="mdui-chip mdui-m-t-2 mdui-m-b-1 mdui-hidden-sm-down">
            <span class="mdui-chip-icon">A</span>
            <span class="mdui-chip-title">{{ $file['name'] }}</span>
        </div>
        <div class="mudi-center mdui-m-t-1" id="video-player">
            <div id="aplayer"></div>
        </div>
        <div class="mdui-typo">
            <hr/>
        </div>
        <div class="mdui-textfield">
            <label class="mdui-textfield-label" for="downloadUrl">下载地址</label>
            <input class="mdui-textfield-input" type="text" id="downloadUrl"
                   value="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}"/>
        </div>
    </div>
    <a href="{{ $file['download'] }}" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i
            class="mdui-icon material-icons">file_download</i></a>
@stop
