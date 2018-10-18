@extends('layouts.admin')
@section('title','文件上传')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/webuploader@0.1.8/dist/webuploader.min.css">
@stop
@section('content')
    <div class="form-group">
        <label class="form-control-label" for="target_directory">上传目录</label>
        <input type="text" class="form-control" id="target_directory" name="target_directory">
    </div>
    <div id="uploader">
        <div id="fileList" class="uploader-list"></div>
        <div class="btns">
            <div id="picker">选择文件</div>
            <button id="ctlBtn" class="btn btn-default">开始上传</button>
        </div>
    </div>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/webuploader@0.1.8/dist/webuploader.min.js"></script>
    <script>
        // 文件上传
        jQuery(function () {
            var $ = jQuery,
                $list = $('#fileList'),

                $btn = $('#ctlBtn'),

                state = 'pending',

                uploader;

            uploader = WebUploader.create({

                fileVal: 'olaindex_file',

                resize: false,

                swf: 'https://cdn.jsdelivr.net/npm/webuploader@0.1.8/dist/Uploader.swf',

                chunked: true,

                formData: {
                    '_token': Config._token,
                },

                server: Config.routes.upload_file,

                pick: '#picker',

                fileNumLimit: 10,

                fileSizeLimit: 40 * 1024 * 1024,

                fileSingleSizeLimit: 4 * 1024 * 1024
            });
            // 当有文件添加进来的时候
            uploader.on('fileQueued', function (file) {
                $list.append('<div id="' + file.id + '" class="item">' +
                    '<h4 class="info">' + file.name + '</h4>' +
                    '<p class="state">等待上传...</p>' +
                    '</div>');
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                var $li = $('#' + file.id),
                    $percent = $li.find('.progress .progress-bar');

                // 避免重复创建
                if (!$percent.length) {
                    $percent = $('<div class="progress progress-striped active">' +
                        '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                        '</div>' +
                        '</div>').appendTo($li).find('.progress-bar');
                }

                $li.find('p.state').text('上传中');

                $percent.css('width', percentage * 100 + '%');
            });

            uploader.on('uploadSuccess', function (file) {
                $('#' + file.id).find('p.state').text('已上传');
            });

            uploader.on('uploadError', function (file) {
                $('#' + file.id).find('p.state').text('上传出错');
            });

            uploader.on('uploadComplete', function (file) {
                $('#' + file.id).find('.progress').fadeOut();
            });

            uploader.on('all', function (type) {
                if (type === 'startUpload') {
                    state = 'uploading';
                } else if (type === 'stopUpload') {
                    state = 'paused';
                } else if (type === 'uploadFinished') {
                    state = 'done';
                }

                if (state === 'uploading') {
                    $btn.text('暂停上传');
                } else {
                    $btn.text('开始上传');
                }
            });

            $btn.on('click', function () {
                if (state === 'uploading') {
                    uploader.stop();
                } else {
                    // 指定上传目录
                    uploader.options.formData.root = $('#target_directory').val();
                    uploader.upload();
                }
            });
        });
    </script>
@stop
