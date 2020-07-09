<ul class="nav nav-pills card-header-pills">
    <li class="nav-item">
        <a class="nav-link @if( request()->routeIs(['admin.config'])) active @endif" href="{{ route('admin.config') }}">设置</a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if( request()->routeIs(['admin.account.list'])) active @endif"
           href="{{ route('admin.account.list') }}">账号列表</a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if( request()->routeIs(['admin.profile'])) active @endif"
           href="{{ route('admin.profile') }}">其它</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{  route('cache.clear') }}">清理缓存</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{  route('admin.logs') }}">日志</a>
    </li>
</ul>
