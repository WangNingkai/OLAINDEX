@extends('mdui.layouts.main')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/filepond@4.4.9/dist/filepond.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.2.1/dist/filepond-plugin-image-preview.min.css">
    <style>

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
    <script src="https://cdn.jsdelivr.net/npm/filepond@4.4.9/dist/filepond.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.2.1/dist/filepond-plugin-image-preview.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-size@2.1.3/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.4/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script>
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType
        );
        FilePond.setOptions({
            dropOnPage: true,
            dropOnElement: true,
            dropValidation: true,
            server: {
                url: Config.routes.upload_image,
                process: {
                    url: '/',
                    method: 'POST',
                    withCredentials: false,
                    headers: {},
                    timeout: 5000,
                    onload: (response) => {
                        let res = JSON.parse(response);
                        console.log(res);
                        if (res.errno === 200) {
                            $('#showUrl').removeClass('mdui-hidden');
                            $('#urlCode').append('<p>' + res.data.url + '</p>');
                            $('#htmlCode').append('<p>&lt;img src=\'' + res.data.url + '\' alt=\'' + res.data.filename + '\' title=\'' + res.data.filename + '\' /&gt;' + '</p>');
                            $('#bbCode').append('<p>[img]' + res.data.url + '[/img]' + '</p>');
                            $('#markdown').append('<p>![' + res.data.filename + '](' + res.data.url + ')' + '</p>');
                            $('#markdownLinks').append('<p>[![' + res.data.filename + '](' + res.data.url + ')]' + '(' + res.data.url + ')' + '</p>');
                            $('#deleteCode').append('<p>' + res.data.delete + '</p>');
                        }
                        return response.key
                    },
                    onerror: (response) => response.data,
                    ondata: (formData) => {
                        formData.append('_token', Config._token);
                        return formData;
                    }
                },
                revert: null,
                restore: null,
                load: null,
                fetch: null
            },
        });
        const pond = FilePond.create(document.querySelector('input[name=olaindex_img]'), {
            acceptedFileTypes: ['image/*'],
        });
        pond.on('processfile', (error, file) => {
            if (error) {
                console.log('上传出错了');
                return;
            }
            console.log('文件已上传', file);
        });
        pond.on('removefile', (file) => {
            console.log('文件已删除', file);
        });
    </script>
@stop
@section('content')
    <div class="mdui-container-fluid mdui-p-a-2">
        <div class="mdui-row mdui-m-t-3">
            <div class="mdui-typo-headline-opacity">图床</div>
            <br>
            <div class="mdui-typo-title-opacity">您可以尝试文件拖拽或者点击虚线框进行文件上传，单张图片最大支持4MB.</div>
            <br>
            <input type="file" class="filepond" name="olaindex_img" multiple data-max-file-size="4MB"
                   data-max-files="5" data-instant-upload="false"/>
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
