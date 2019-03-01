@extends('default.layouts.main')
@section('title','图床')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/dropzone/5.5.1/min/dropzone.min.css"/>
    <style>
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            background: white;
        }

        .link-container {
            margin-top: 15px;
            padding: 10px;
            border: solid 1px #dadada;
            word-wrap: break-word;
            background-color: #f7f7f7;
        }
        .link-container p{
            margin: 5px 0;
        }
    </style>
@stop
@section('js')
    <script src="https://cdnjs.loli.net/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script>
        Dropzone.options.imageDropzone = {
            url: Config.routes.upload_image,
            method: 'post',
            maxFilesize: 4,
            paramName: 'olaindex_img',
            maxFiles: 10,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            init: function () {
                this.on('sending', function (file, xhr, formData) {
                    formData.append('_token', Config._token);
                });
                this.on('success', function (file, response) {
                    $('#showUrl').removeClass('invisible');
                    $('#urlCode').append('<p>' + response.data.url + '</p>');
                    $('#htmlCode').append('<p>&lt;img src=\'' + response.data.url + '\' alt=\'' + response.data.filename + '\' title=\'' + response.data.filename + '\' /&gt;' + '</p>');
                    $('#bbCode').append('<p>[img]' + response.data.url + '[/img]' + '</p>');
                    $('#markdown').append('<p>![' + response.data.filename + '](' + response.data.url + ')' + '</p>');
                    $('#markdownLinks').append('<p>[![' + response.data.filename + '](' + response.data.url + ')]' + '(' + response.data.url + ')' + '</p>');
                    $('#deleteCode').append('<p>' + response.data.delete + '</p>')
                });
            },

            dictDefaultMessage: '拖拽文件至此上传',
            dictFallbackMessage: '浏览器不支持拖拽上传',
            dictFileTooBig: '文件过大(@{{filesize}}MiB)，请重试',
            dictInvalidFileType: '文件类型不支持',
            dictResponseError: '上传错误 @{{statusCode}}',
            dictCancelUpload: '取消上传',
            dictUploadCanceled: '上传已取消',
            dictCancelUploadConfirmation: '确定取消上传吗?',
            dictRemoveFile: '移除此文件',
            dictRemoveFileConfirmation: '确定移除此文件吗',
            dictMaxFilesExceeded: '已达到最大上传数.',
        };
    </script>
@stop
@section('content')
    <div class="card border-light mb-3">
        <div class="card-body">
            <div class="page-container">
                <h4>图床</h4>
                <p>您可以尝试文件拖拽或者点击虚线框进行文件上传，单张图片最大支持4MB.</p>
                <form class="dropzone" id="image-dropzone">
                </form>
            </div>
        </div>
    </div>
    <div id="showUrl" class="invisible">
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
                <div class="link-container" id="urlCode"></div>
            </div>
            <div class="tab-pane fade" id="htmlPanel">
                <div class="link-container" id="htmlCode"></div>
            </div>
            <div class="tab-pane fade" id="bbPanel">
                <div class="link-container" id="bbCode"></div>
            </div>
            <div class="tab-pane fade" id="markdownPanel">
                <div class="link-container" id="markdown"></div>
            </div>
            <div class="tab-pane fade" id="markdownLinkPanel">
                <div class="link-container" id="markdownLinks"></div>
            </div>
            <div class="tab-pane fade" id="deletePanel">
                <div class="link-container" id="deleteCode"></div>
            </div>
        </div>
    </div>
@stop

