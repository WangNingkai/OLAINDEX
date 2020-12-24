@extends('default.layouts.main')
@section('title', '文件编辑')
@section('content')
    <div class="card border-light mb-3 shadow">
        <div class="card-header">
            文件编辑
        </div>
        <div class="card-body">
            <form action="" method="post">
                @csrf
                <label for="mde"></label>
                <input type="hidden" name="file_id" value="{{ $file_id }}">
                <input type="hidden" name="account_id" value="{{ $account_id }}">
                <input type="hidden" name="parent_id" value="{{ $parent_id }}">
                <input type="hidden" name="redirect" value="{{ $redirect }}">
                <textarea id="mde" name="content" class="invisible">{{  $content }}</textarea>
                <button type="submit" id="submit_btn" class="btn btn-primary">提交</button>
            </form>
        </div>
    </div>
@stop
@push('stylesheet')
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    <style>
        .editor-preview img, .editor-preview-side img {
            box-sizing: border-box;
            max-width: 100%;
            max-height: 100%;
            box-shadow: 0 0 5px rgba(0, 0, 0, .15);
            vertical-align: middle
        }
    </style>
@endpush
@push('scripts')
    <script defer src="https://use.fontawesome.com/releases/v5.4.1/js/all.js"
            integrity="sha384-L469/ELG4Bg9sDQbl0hvjMq8pOcqFgkSpwhwnslzvVVGpDjYJ6wJJyYjvG3u8XW7"
            crossorigin="anonymous"></script>
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <script>
        $(function() {
            const easyMDE = new EasyMDE({
                element: $('#mde')[0],
                autoDownloadFontAwesome: false,
                autofocus: true,
                autosave: {
                    enabled: false,
                },
                blockStyles: {
                    bold: '__',
                    italic: '_',
                },
                forceSync: true,
                indentWithTabs: false,
                insertTexts: {
                    horizontalRule: ['', '\n\n-----\n\n'],
                    image: ['![](http://', ')'],
                    link: ['[', '](http://)'],
                    table: ['',
                        '\n\n| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text | Text | Text |\n\n',
                    ],
                },
                minHeight: '480px',
                parsingConfig: {
                    allowAtxHeaderWithoutSpace: true,
                    strikethrough: true,
                    underscoresBreakWords: true,
                },
                placeholder: '在此输入内容...',
                renderingConfig: {
                    singleLineBreaks: true,
                    codeSyntaxHighlighting: false,
                },
                spellChecker: false,
                status: ['autosave', 'lines', 'words', 'cursor'],
                styleSelectedText: true,
                syncSideBySidePreviewScroll: true,
                tabSize: 4,
                toolbar: [
                    'bold', 'italic', 'strikethrough', 'heading', '|', 'quote', 'code', 'table',
                    'horizontal-rule', 'unordered-list', 'ordered-list', '|',
                    'link', 'image', '|', 'side-by-side', 'fullscreen', '|',
                    {
                        name: 'guide',
                        action: function customFunction() {
                            let win = window.open(
                                'https://github.com/riku/Markdown-Syntax-CN/blob/master/syntax.md',
                                '_blank')
                            if (win) {
                                win.focus()
                            } else {
                                alert('Please allow popups for this website')
                            }
                        },
                        className: 'fa fa-info-circle',
                        title: 'Markdown 语法！',
                    },
                    {
                        name: 'publish',
                        action: function customFunction(editor) {
                            $('#submit_btn').click()
                            editor.clearAutosavedValue()
                        },
                        className: 'fa fa-paper-plane',
                        title: '提交',
                    },
                ],
                toolbarTips: true,
            })
            easyMDE.codemirror.setSize('auto', '640px')
            easyMDE.codemirror.on('optionChange', (item) => {
                let fullscreen = item.getOption('fullScreen')
                if (fullscreen) {
                    $('.editor-toolbar,.fullscreen,.CodeMirror-fullscreen,.editor-statusbar,.editor-preview').css('z-index', '9999')
                }
            })
        })
    </script>
@endpush
