@extends('admin.layouts.main')
@section('title', '文件管理')
@section('content')
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    README.md
                </div>
                <h2 class="page-title">
                    文件管理
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  <span class="d-none d-sm-inline">
                    <a href="{{ route('manage.query',['account_id' =>$account_id])  }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1"/>
                                        </svg>
                      返回
                    </a>
                  </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">编辑README.md</h3>
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
        </div>
    </div>
@stop
@push('stylesheet')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.13.0/dist/easymde.min.css">
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
    <script src="https://cdn.staticfile.org/font-awesome/5.15.1/js/all.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/easymde@2.13.0/dist/easymde.min.js"></script>
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
