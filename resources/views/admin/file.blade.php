@extends('layouts.admin')
@section('title','文件上传')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.css">
    <style>
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            background: white;
        }
    </style>
@stop
@section('content')
    <div class="form-group">
        <label class="form-control-label" for="target_directory">上传目录</label>
        <input type="text" class="form-control" id="target_directory" name="target_directory"
               placeholder="在此输入要上传的目录位置（默认 OneDrive 根目录）">
    </div>
    <div class="form-group">
        <form class="dropzone" id="file-dropzone">
        </form>
    </div>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.options.fileDropzone = {
            url: Config.routes.upload_file,
            method: 'post',
            maxFilesize: 4,
            paramName: 'olaindex_file',
            maxFiles: 10,
            // acceptedFiles: 'image/*',
            addRemoveLinks: true,
            init: function () {
                this.on('sending', function (file, xhr, formData) {
                    formData.append('_token', Config._token);
                    formData.append('root', $('#target_directory').val());
                });
            },

            dictDefaultMessage: '拖拽文件至此上传 (最大支持4M)',
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
