@extends('mdui.layouts.main')
@section('title', $file['name'])
@section('content')
    <div class="mdui-m-t-5">
        <div class="mdui-card mdui-shadow-3 mdui-p-a-2" style="border-radius: 8px">
            <div class="mdui-card-content">
                <div class="mdui-typo mdui-m-t-2">
                    <div class="mdui-typo-title-opacity">{{ $file['name'] }}</div>
                    <div
                        class="mdui-typo-subheading-opacity">{{ convert_size($file['size']) .' / '.date('Y-m-d H:i:s', strtotime($file['lastModifiedDateTime'])) }}</div>
                </div>
                <div class="mdui-m-t-2" style="min-height: 300px">
                    @include('mdui.components.preview.' . $show,['file' => $file, 'show' => $show])
                </div>
                <div class="mdui-typo mdui-m-t-2">
                    <div class="mdui-textfield">
                        <i class="mdui-icon material-icons">links</i>
                        <input class="mdui-textfield-input" type="text" id="link"
                               value="{{ shorten_url(route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', $path)),'download' => 1])) }}"/>

                    </div>
                    <button
                        data-clipboard-target="#link"
                        class="clipboard mdui-btn mdui-btn-raised mdui-btn-dense mdui-ripple mdui-color-theme-accent mdui-float-right">
                        <i class="mdui-icon material-icons">content_copy</i> 复制
                    </button>

                </div>
            </div>
        </div>
    </div>
    <a href="{{ shorten_url(route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', $path)),'download' => 1])) }}"
       class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"
    ><i class="mdui-icon material-icons">file_download</i></a>
@stop
@push('scripts')
    <script>
        $(function() {
            $('.list-item,.breadcrumb-item').on('click', function(e) {
                if ($(this).attr('data-route')) {
                    window.location.href = $(this).attr('data-route')
                }
                e.stopPropagation()
            })
        })
    </script>
@endpush
