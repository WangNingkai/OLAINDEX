@extends('mdui.layouts.main')
@section('title', '图床')
@section('content')
    <div class="mdui-m-t-5">
        <div class="mdui-row mdui-m-t-3">
            <div class="mdui-typo-title-opacity">图床</div>
            <br>
            <div class="mdui-typo-body-2-opacity">您可以尝试文件拖拽或者点击虚线框进行文件上传，单张图片最大支持4MB.</div>
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
            </div>
            <div class="mdui-p-a-2 mdui-m-t-2 mdui-shadow-3" id="urlCode"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 mdui-shadow-3" id="htmlCode"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 mdui-shadow-3" id="bbCode"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 mdui-shadow-3" id="markdown"></div>
            <div class="mdui-p-a-2 mdui-m-t-2 mdui-shadow-3" id="markdownLinks"></div>
        </div>
    </div>
@stop
@push('stylesheet')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/filepond@4.23.1/dist/filepond.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.4/dist/filepond-plugin-image-preview.min.css">
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
                        if (res.code === 0) {
                            $('#showUrl').removeClass('mdui-hidden')
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
