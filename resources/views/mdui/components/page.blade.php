@if ($paginator->hasPages())
    <div class="mdui-btn-group mdui-m-t-3 mdui-m-b-3">
        @if ($paginator->onFirstPage())
            <a href="javascript:void(0)" class="mdui-btn mdui-text-color-theme-text" disabled><i class="mdui-icon material-icons">chevron_left</i>上一页</a>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="mdui-btn mdui-text-color-theme-text"><i class="mdui-icon material-icons">chevron_left</i>上一页</a>
        @endif


        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="mdui-btn mdui-text-color-theme-text">下一页<i
                    class="mdui-icon material-icons">chevron_right</i></a>
        @else
            <a href="javascript:void(0)" class="mdui-btn mdui-text-color-theme-text" disabled>下一页<i class="mdui-icon material-icons">chevron_right</i></a>
        @endif
    </div>
@endif
