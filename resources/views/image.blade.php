@extends('layouts.main')
@section('title','图床')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/webuploader@0.1.8/dist/webuploader.min.css">
    <link rel="stylesheet" href="{{ asset('css/wu.css') }}">
@stop
@section('content')
    <div class="card border-light mb-3">
        <div class="card-body">
            <div class="page-container">
                <h3>图床</h3>
                <p>您可以尝试文件拖拽，使用截图工具，然后激活窗口后粘贴，或者点击添加图片按钮.</p>
                <div id="uploader" class="wu-example">
                    <div class="queueList">
                        <div id="dndArea" class="placeholder">
                            <div id="filePicker"></div>
                            <p>或将照片拖到这里，单次最多可选10张，最大单张图片支持4M</p>
                        </div>
                    </div>
                    <div class="statusBar" style="display:none;">
                        <div class="progress">
                            <span class="text">0%</span>
                            <span class="percentage"></span>
                        </div>
                        <div class="info"></div>
                        <div class="btns">
                            <div id="filePicker2"></div>
                            <div class="uploadBtn">开始上传</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="showUrl" style="display: none;">
        <ul id="navTab" class="nav nav-tabs">
            <li class="nav-item active">
                <a class="nav-link" data-toggle="tab" href="#urlPanel">URL</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#htmlPanel">HTML</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#bbPanel">bbCode</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#markdownPanel">Markdown</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#markdownLinkPanel">Markdown with Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#deletePanel">Delete Link</a>
            </li>
        </ul>
        <div id="navTabContent" class="tab-content">
            <div class="tab-pane fade in active show" id="urlPanel">
                <pre style="margin-top: 5px;"><code id="urlCode"></code></pre>
            </div>
            <div class="tab-pane fade" id="htmlPanel">
                <pre style="margin-top: 5px;"><code id="htmlCode"></code></pre>
            </div>
            <div class="tab-pane fade" id="bbPanel">
                <pre style="margin-top: 5px;"><code id="bbCode"></code></pre>
            </div>
            <div class="tab-pane fade" id="markdownPanel">
                <pre style="margin-top: 5px;"><code id="markdown"></code></pre>
            </div>
            <div class="tab-pane fade" id="markdownLinkPanel">
                <pre style="margin-top: 5px;"><code id="markdownLinks"></code></pre>
            </div>
            <div class="tab-pane fade" id="deletePanel">
                <pre style="margin-top: 5px;"><code id="deleteCode"></code></pre>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/webuploader@0.1.8/dist/webuploader.min.js"></script>
    <script src="{{ asset('js/wu.js') }}"></script>
@stop
