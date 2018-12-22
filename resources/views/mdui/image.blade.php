@extends('mdui.layouts.main')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5/dist/dropzone.min.css">
    <style>
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            background: white;
        }

        .link-container {
            border: solid 1px #dadada;
            word-wrap: break-word;
            background-color: #f7f7f7;
        }

        .link-container p {
            margin: 5px 0;
        }
    </style>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5/dist/dropzone.min.js"></script>
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
                    $('#showUrl').removeClass('mdui-hidden');
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
    <div class="mdui-container-fluid mdui-p-a-2">
        <div class="mdui-row mdui-m-t-3">
            <div class="mdui-typo-headline-opacity">图床</div>
            <br>
            <div class="mdui-typo-title-opacity">您可以尝试文件拖拽或者点击虚线框进行文件上传，单张图片最大支持4MB.</div>
            <br>
            <form class="dropzone" id="image-dropzone">
            </form>
        </div>
        <div id="showUrl" class="mdui-hidden">
            <div class="mdui-tab" mdui-tab>
                <a class="mdui-ripple" href="#urlCode">URL</a>
                <a class="mdui-ripple" href="#htmlCode">HTML</a>
                <a class="mdui-ripple" href="#bbCode">BBCODE</a>
                <a class="mdui-ripple" href="#markdown">MD</a>
                <a class="mdui-ripple" href="#markdownLinks">MD LINK</a>
                <a class="mdui-ripple" href="#deleteCode">DEL Link</a>
            </div>
            <div class="mdui-p-a-2 mdui-m-t-2 link-container" id="urlCode"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 link-container" id="htmlCode"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 link-container" id="bbCode"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 link-container" id="markdown"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 link-container" id="markdownLinks"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 link-container" id="deleteCode"></div>
        </div>
    </div>
@stop
