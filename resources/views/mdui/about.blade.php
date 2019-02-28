@extends('mdui.layouts.main')
@section('content')
    <div class="mdui-container-fluid">
        <div class="mdui-typo mdui-p-t-3">
            {!! $markdown !!}
        </div>
        @if (\Illuminate\Support\Str::contains(config('app.url'),['localhost','dev.ningkai.wang']))
            <script src="https://utteranc.es/client.js"
                    repo="WangNingkai/OLAINDEX"
                    issue-term="pathname"
                    theme="github-light"
                    crossorigin="anonymous"
                    async>
            </script>
        @endif
    </div>
@stop
