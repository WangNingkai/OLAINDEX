@extends('mdui.layouts.admin')
@section('content')
    <div class="mdui-container-fluid mdui-m-y-2">

        <div class="mdui-typo">
            <h1>展示设置
                <small>展示的文件后缀, 以空格分开</small>
            </h1>
        </div>
        <form action="" method="post">
            @csrf
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="image">图片</label>
                <input type="text" class="mdui-textfield-input" id="image" name="image"
                       value="{{ setting('image') }}">
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="audio">音频</label>
                <input type="text" class="mdui-textfield-input" id="audio" name="audio"
                       value="{{ setting('audio') }}">
            </div>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="video">视频</label>
                <input type="text" class="mdui-textfield-input" id="video" name="video"
                       value="{{ setting('video') }}">
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="dash">dash流</label>
                <input type="text" class="mdui-textfield-input" id="dash" name="dash"
                       value="{{ setting('dash') }}">
                <div class="mdui-textfield-helper">仅支持企业、教育版账户</div>
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="doc">文档</label>
                <input type="text" class="mdui-textfield-input" id="doc" name="doc"
                       value="{{ setting('doc') }}">
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="code">代码</label>
                <input type="text" class="mdui-textfield-input" id="code" name="code"
                       value="{{ setting('code') }}">
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="stream">文件流</label>
                <input type="text" class="mdui-textfield-input" id="stream" name="stream"
                       value="{{ setting('stream') }}">
            </div>
            <br>

            <button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" type="submit"><i
                    class="mdui-icon material-icons">check</i> 保存
            </button>
        </form>
    </div>
@stop
