@extends('mdui.layouts.main')
@section('title', $file['name'])
@section('content')
    <div class="mdui-container mdui-m-t-5">
        <span class="breadcrumb-item" data-route="{{ route('drive.query', ['hash' => $hash]) }}">
            <span class="mdui-chip">
                <span class="mdui-chip-title">/</span>
            </span>
        </span>
        @if (count($path) < 6)
            @foreach($path as $key => $value)
                @if(end($path) === $value && $key === (count($path) - 1))
                    <span class="breadcrumb-item">
                            <i class="mdui-icon material-icons mdui-icon-dark mdui-m-a-0">chevron_right</i>
                            <span class="mdui-chip">
                              <span class="mdui-chip-title">{{ $value }}</span>
                            </span>
                        </span>
                @else
                    @if (!blank($value))
                        <span class="breadcrumb-item"
                              data-route="{{ route('drive.query', ['hash' => $hash,'query' => \App\Helpers\Tool::combineBreadcrumb($key + 1, $path)]) }}">
                            <i class="mdui-icon material-icons mdui-icon-dark mdui-m-a-0">chevron_right</i>
                            <span class="mdui-chip">
                              <span class="mdui-chip-title">{{ $value }}</span>
                            </span>
                        </span>
                    @endif
                @endif
            @endforeach
        @else
            <span class="breadcrumb-item">
                <i class="mdui-icon material-icons mdui-icon-dark mdui-m-a-0">chevron_right</i>
                <span class="mdui-chip">
                    <span class="mdui-chip-title">...</span>
                </span>
            </span>
            @foreach($path as $key => $value)
                @if(end($path) === $value && $key === (count($path) - 1))
                    <span class="breadcrumb-item">
                        <i class="mdui-icon material-icons mdui-icon-dark mdui-m-a-0">chevron_right</i>
                        <span class="mdui-chip">
                            <span class="mdui-chip-title">{{ $value }}</span>
                        </span>
                    </span>
                @else
                    @if (!blank($value) && $key === (count($path) - 2))
                        <span class="breadcrumb-item"
                              data-route="{{ route('drive.query', ['hash' => $hash,'query' => \App\Helpers\Tool::combineBreadcrumb($key + 1, $path)]) }}">
                            <i class="mdui-icon material-icons mdui-icon-dark mdui-m-a-0">chevron_right</i>
                            <span class="mdui-chip">
                              <span class="mdui-chip-title">{{ $value }}</span>
                            </span>
                        </span>
                    @endif
                @endif
            @endforeach
        @endif
        <div class="mdui-card mdui-shadow-0">
            <div class="mdui-card-content">
                <div class="mdui-typo mdui-m-t-2">
                    <div class="mdui-typo-title-opacity">{{ $file['name'] }}</div>
                    <div
                        class="mdui-typo-subheading-opacity">{{ convert_size($file['size']) .' / '.date('Y-m-d H:i:s', strtotime($file['lastModifiedDateTime'])) }}</div>
                </div>
            </div>
            <div class="mdui-card-media mdui-p-a-2">
                @include('mdui.components.preview.' . $show,['file' => $file, 'show' => $show])
            </div>
            <a href="{{ shorten_url(route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', $path)),'download' => 1])) }}"
               class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"
            ><i class="mdui-icon material-icons">file_download</i></a>
        </div>

    </div>
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
