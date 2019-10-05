@if ($paginator->hasPages())
    <div class="mdui-btn-group mdui-m-t-3 mdui-m-b-3">
        @if ($paginator->onFirstPage())
            <a href="javascript:void(0)" class="mdui-btn" disabled><i class="mdui-icon material-icons">chevron_left</i></a>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="mdui-btn"><i class="mdui-icon material-icons">chevron_left</i></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <a href="javascript:void(0)" class="mdui-btn mdui-btn-active">{{ $element }}</a>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a href="javascript:void(0)" class="mdui-btn mdui-btn-active">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="mdui-btn mdui-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="mdui-btn"><i
                    class="mdui-icon material-icons">chevron_right</i></a>
        @else
            <a href="javascript:void(0)" class="mdui-btn" disabled><i class="mdui-icon material-icons">chevron_right</i></a>
        @endif
    </div>
@endif
