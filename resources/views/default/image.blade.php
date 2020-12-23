@extends('default.layouts.main')
@section('title','图床')
@section('content')
    <div class="card border-light mb-3 shadow">
        <div class="card-body">
            <div class="page-container">
                <div class="h4">图床</div>
                <p>您可以尝试文件拖拽或者点击虚线框进行文件上传，单张图片最大支持4MB.</p>
                <input type="file" class="filepond" name="olaindex_img" multiple data-max-file-size="4MB"
                       data-max-files="5" data-instant-upload="false"/>

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
        </div>
    </div>
@stop
@push('stylesheet')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/filepond@4.23.1/dist/filepond.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.4/dist/filepond-plugin-image-preview.min.css">
    <style>
        .link-container {
            margin-top: 15px;
            padding: 10px;
            border: solid 1px #dadada;
            word-wrap: break-word;
            background-color: #f7f7f7;
        }

        .link-container p {
            margin: 5px 0;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/filepond@4.23.1/dist/filepond.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.4/dist/filepond-plugin-image-preview.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-size@2.2.1/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.5/dist/filepond-plugin-file-validate-type.min.js"></script>

    <script>
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType,
        )
        FilePond.setOptions({
            labelIdle: '拖放文件，或者 <span class="filepond--label-action"> 浏览 </span>',
            dropOnPage: true,
            dropOnElement: true,
            dropValidation: true,
            server: {
                url: App.routes.upload_image,
                process: {
                    url: '/',
                    method: 'POST',
                    withCredentials: false,
                    headers: {},
                    timeout: 5000,
                    onload: (response) => {
                        let res = JSON.parse(response)
                        console.log(res)
                        if (res.code === 0) {
                            $('#showUrl').removeClass('invisible')
                            $('#urlCode').append('<p>' + res.data.url + '</p>')
                            $('#htmlCode').append('<p>&lt;img src=\'' + res.data.url + '\' alt=\'' + res.data.filename + '\' title=\'' + res.data.filename + '\' /&gt;' + '</p>')
                            $('#bbCode').append('<p>[img]' + res.data.url + '[/img]' + '</p>')
                            $('#markdown').append('<p>![' + res.data.filename + '](' + res.data.url + ')' + '</p>')
                            $('#markdownLinks').append('<p>[![' + res.data.filename + '](' + res.data.url + ')]' + '(' + res.data.url + ')' + '</p>')
                        }
                        return response.key
                    },
                    onerror: (response) => response.data,
                    ondata: (formData) => {
                        formData.append('_token', App._token)
                        return formData
                    },
                },
                revert: null,
                restore: null,
                load: null,
                fetch: null,
            },
        })
        const pond = FilePond.create(document.querySelector('input[name=olaindex_img]'), {
            acceptedFileTypes: ['image/*'],
        })
        pond.on('processfile', (error, file) => {
            if (error) {
                console.log('上传出错了')
                return
            }
            console.log('文件已上传', file)
        })
        pond.on('removefile', (file) => {
            console.log('文件已删除', file)
        })
    </script>
@endpush
