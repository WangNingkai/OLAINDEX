@push('stylesheet')
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/combine/npm/prismjs@1.20.0/themes/prism-tomorrow.min.css,npm/prismjs@1.20.0/plugins/toolbar/prism-toolbar.min.css,npm/prismjs@1.20.0/plugins/previewers/prism-previewers.min.css,npm/prismjs@1.20.0/plugins/command-line/prism-command-line.min.css">
@endpush
@push('scripts')
    <script
        src="https://cdn.jsdelivr.net/combine/npm/prismjs@1.20.0,npm/prismjs@1.20.0/components/prism-markup-templating.min.js,npm/prismjs@1.20.0/components/prism-markup.min.js,npm/prismjs@1.20.0/components/prism-css.min.js,npm/prismjs@1.20.0/components/prism-clike.min.js,npm/prismjs@1.20.0/components/prism-javascript.min.js,npm/prismjs@1.20.0/components/prism-docker.min.js,npm/prismjs@1.20.0/components/prism-git.min.js,npm/prismjs@1.20.0/components/prism-json.min.js,npm/prismjs@1.20.0/components/prism-less.min.js,npm/prismjs@1.20.0/components/prism-markdown.min.js,npm/prismjs@1.20.0/components/prism-nginx.min.js,npm/prismjs@1.20.0/components/prism-php.min.js,npm/prismjs@1.20.0/components/prism-php-extras.min.js,npm/prismjs@1.20.0/components/prism-sass.min.js,npm/prismjs@1.20.0/components/prism-sql.min.js,npm/prismjs@1.20.0/components/prism-yaml.min.js,npm/prismjs@1.20.0/components/prism-bash.min.js,npm/prismjs@1.20.0/components/prism-ini.min.js,npm/prismjs@1.20.0/plugins/toolbar/prism-toolbar.min.js,npm/prismjs@1.20.0/plugins/previewers/prism-previewers.min.js,npm/prismjs@1.20.0/plugins/autoloader/prism-autoloader.min.js,npm/prismjs@1.20.0/plugins/command-line/prism-command-line.min.js,npm/prismjs@1.20.0/plugins/normalize-whitespace/prism-normalize-whitespace.min.js,npm/prismjs@1.20.0/plugins/keep-markup/prism-keep-markup.min.js,npm/prismjs@1.20.0/plugins/show-language/prism-show-language.min.js,npm/prismjs@1.20.0/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js"></script>
@endpush
<div class="mdui-center">
<pre>
    <code class="language-{{ $file['ext'] === 'sh' ? 'bash' : $file['ext']}}">{{ $file['content'] }}</code>
</pre>
</div>
