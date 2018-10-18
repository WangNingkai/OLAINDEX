@extends('layouts.main')
@section('title',$file['name'])
@section('css')
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/combine/npm/prismjs@1/themes/prism-okaidia.min.css,npm/prismjs@1/plugins/toolbar/prism-toolbar.min.css,npm/prismjs@1/plugins/previewers/prism-previewers.min.css,npm/prismjs@1/plugins/command-line/prism-command-line.min.css">
@stop
@section('content')
    @include('breadcrumb')
    <div class="card border-light mb-3">
        <div class="card-header">
            {{ $file['name'] }}
        </div>
        <div class="card-body">
            <div class="text-center"><a href="{{ $file['path'] }}" class="btn btn-success"><i
                        class="fa fa-download"></i> 下载</a></div>
            <hr>
            <div>
                <pre>
                    <code class="language-markup">{{ $file['content'] }}</code>
                </pre>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script
        src="https://cdn.jsdelivr.net/combine/npm/prismjs@1,npm/prismjs@1/components/prism-markup-templating.min.js,npm/prismjs@1/components/prism-markup.min.js,npm/prismjs@1/components/prism-css.min.js,npm/prismjs@1/components/prism-clike.min.js,npm/prismjs@1/components/prism-javascript.min.js,npm/prismjs@1/components/prism-docker.min.js,npm/prismjs@1/components/prism-git.min.js,npm/prismjs@1/components/prism-json.min.js,npm/prismjs@1/components/prism-less.min.js,npm/prismjs@1/components/prism-markdown.min.js,npm/prismjs@1/components/prism-nginx.min.js,npm/prismjs@1/components/prism-php.min.js,npm/prismjs@1/components/prism-php-extras.min.js,npm/prismjs@1/components/prism-sass.min.js,npm/prismjs@1/components/prism-sql.min.js,npm/prismjs@1/components/prism-yaml.min.js,npm/prismjs@1/components/prism-bash.min.js,npm/prismjs@1/components/prism-ini.min.js,npm/prismjs@1/plugins/toolbar/prism-toolbar.min.js,npm/prismjs@1/plugins/previewers/prism-previewers.min.js,npm/prismjs@1/plugins/autoloader/prism-autoloader.min.js,npm/prismjs@1/plugins/command-line/prism-command-line.min.js,npm/prismjs@1/plugins/normalize-whitespace/prism-normalize-whitespace.min.js,npm/prismjs@1/plugins/keep-markup/prism-keep-markup.min.js,npm/prismjs@1/plugins/show-language/prism-show-language.min.js,npm/prismjs@1/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js"></script>
@stop
