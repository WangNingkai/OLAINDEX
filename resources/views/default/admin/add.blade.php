@extends('default.layouts.admin')
@section('title', request()->get('name'))
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/inscrybmde@1.11.6/dist/inscrybmde.min.css">
    <style>
        .editor-preview img, .editor-preview-side img {
            box-sizing: border-box;
            max-width: 100%;
            max-height: 100%;
            box-shadow: 0 0 5px rgba(0, 0, 0, .15);
            vertical-align: middle
        }
    </style>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/inscrybmde@1.11.6/dist/inscrybmde.min.js"></script>
    <script>
        $(function () {
            const mdeditor = new InscrybMDE({
                autoDownloadFontAwesome: false,
                autofocus: true,
                autosave: {
                    enabled: false,
                    uniqueId: "content:{{ request()->get('name') }}",
                    delay: 1000,
                },
                blockStyles: {
                    bold: "__",
                    italic: "_"
                },
                element: $("#mde")[0],
                forceSync: true,
                indentWithTabs: false,
                insertTexts: {
                    horizontalRule: ["", "\n\n-----\n\n"],
                    image: ["![](http://", ")"],
                    link: ["[", "](http://)"],
                    table: ["",
                        "\n\n| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text | Text | Text |\n\n"
                    ],
                },
                minHeight: "480px",
                parsingConfig: {
                    allowAtxHeaderWithoutSpace: true,
                    strikethrough: true,
                    underscoresBreakWords: true,
                },
                placeholder: "在此输入内容...",
                renderingConfig: {
                    singleLineBreaks: true,
                    codeSyntaxHighlighting: false,
                },
                spellChecker: false,
                status: ["autosave", "lines", "words", "cursor"],
                styleSelectedText: true,
                syncSideBySidePreviewScroll: true,
                tabSize: 4,
                toolbar: [
                    "bold", "italic", "strikethrough", "heading", "|", "quote", "code", "table",
                    "horizontal-rule", "unordered-list", "ordered-list", "|",
                    "link", "image", "|", "side-by-side", 'fullscreen', "|",
                    {
                        name: "guide",
                        action: function customFunction() {
                            let win = window.open(
                                'https://github.com/riku/Markdown-Syntax-CN/blob/master/syntax.md',
                                '_blank');
                            if (win) {
                                win.focus();
                            } else {
                                alert('Please allow popups for this website');
                            }
                        },
                        className: "fa fa-info-circle",
                        title: "Markdown 语法！",
                    },
                    {
                        name: "publish",
                        action: function customFunction(editor) {
                            $('#submit_btn').click();
                            editor.clearAutosavedValue();
                        },
                        className: "fa fa-paper-plane",
                        title: "提交",
                    }
                ],
                toolbarTips: true,
            });
            mdeditor.codemirror.setSize('auto', '640px');
            mdeditor.codemirror.on('optionChange', (item) => {
                let fullscreen = item.getOption('fullScreen');
                if (fullscreen)
                    $(".editor-toolbar,.fullscreen,.CodeMirror-fullscreen").css('z-index', '9998');
            });
        });
    </script>
@stop
@section('content')
    <form action="" method="post">
        @csrf
        <textarea name="content" id="mde" class="invisible"></textarea>
        <button type="submit" id="submit_btn" class="btn btn-primary">提交</button>
    </form>
@stop

