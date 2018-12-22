@extends('mdui.layouts.main')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/plyr@3/dist/plyr.min.css">
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/plyr@3/dist/plyr.min.js"></script>
    <script>
        const player = new Plyr('#player', {
            iconUrl: "https://cdn.jsdelivr.net/npm/plyr@3/dist/plyr.svg",
        });
    </script>
@stop
@section('content')

    <div class="mdui-container-fluid">
        <div class="mdui-typo mdui-m-y-2">
            <div class="mdui-typo-subheading-opacity">{{ $file['name'] }}</div>
        </div>
        <div class="mdui-typo mdui-shadow-3 mudi-center" id="audio-player">
            <audio id="player" crossorigin controls title="{{ $file['name'] }}">
                <source src="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}" type="audio/mp3">
                <source src="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}" type="audio/ogg">
            </audio>
        </div>
        <br>
        <div class="mdui-textfield">
            <label class="mdui-textfield-label" for="downloadUrl">下载地址</label>
            <input class="mdui-textfield-input" type="text" id="downloadUrl"
                   value="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}"/>
        </div>
    </div>
    <a href="{{ $file['download'] }}" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i
            class="mdui-icon material-icons">file_download</i></a>
@stop
