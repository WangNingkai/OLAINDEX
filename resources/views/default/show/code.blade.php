@extends('default.layouts.main')
@section('title',$file['name'])
@section('css')
    <link href="https://cdn.bootcss.com/prism/1.15.0/plugins/command-line/prism-command-line.min.css" rel="stylesheet">
@stop
@section('js')
    <script src="https://cdn.bootcss.com/prism/1.15.0/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js"></script>
@stop
@section('content')
    @include('default.widgets.breadcrumb')
    <div class="card border-light mb-3">
        <div class="card-header">
            {{ $file['name'] }}
        </div>
        <div class="card-body">
            <div class="text-center">
                <a href="{{ route('download', [
                        'query' => Tool::getEncodeUrl($origin_path),
                        'onedrive' => app('onedrive')->id
                    ]) }}" class="btn btn-success">
                    <i class="fa fa-download"></i> 下载
                </a>
            </div>
            <br>
            <div class="text-center">
                <pre>
                    <code class="language-{{ $file['ext'] === 'sh' ? 'bash' : $file['ext']}}">{{ $file['content'] }}</code>
                </pre>
            </div>
        </div>
    </div>
@stop

