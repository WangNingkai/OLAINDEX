@extends('mdui.layouts.main')
@section('css')
    <link href="https://cdn.bootcss.com/fancybox/3.5.2/jquery.fancybox.min.css" rel="stylesheet">
@stop
@section('js')
    <script src="https://cdn.bootcss.com/fancybox/3.5.2/jquery.fancybox.min.js"></script>
@stop
@section('content')

    <div class="mdui-container-fluid">
        <div class="mdui-chip mdui-m-t-2 mdui-m-b-2 mdui-hidden-sm-down">
            <span class="mdui-chip-icon">A</span>
            <span class="mdui-chip-title">{{ $file['name'] }}</span>
        </div>
        <a href="{{ $file['download'] }}" data-fancybox><img
                class="mdui-img-fluid mdui-center mdui-m-t-1" src="{{ $file['thumb'] }}" alt="{{ $file['name'] }}"/></a>
        <div class="mdui-typo">
            <hr/>
        </div>
        <div class="mdui-textfield">
            <label class="mdui-textfield-label" for="downloadUrl">下载地址</label>
            <input class="mdui-textfield-input" type="text" id="downloadUrl"
                   value="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}"/>
        </div>
        <div class="mdui-textfield">
            <label class="mdui-textfield-label" for="htmlUrl">HTML 引用地址</label>
            <input class="mdui-textfield-input" type="text" id="htmlUrl"
                   value="<img src='{{ route('view',\App\Helpers\Tool::getEncodeUrl($origin_path)) }}' />"/>
        </div>
        <div class="mdui-textfield">
            <label class="mdui-textfield-label" for="makdownUrl">Markdown 引用地址</label>
            <input class="mdui-textfield-input" type="text" id="makdownUrl"
                   value="![]({{ route('view',\App\Helpers\Tool::getEncodeUrl($origin_path)) }})"/>
        </div>
    </div>
    <a href="{{ $file['download'] }}" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i
            class="mdui-icon material-icons">file_download</i></a>
@stop
