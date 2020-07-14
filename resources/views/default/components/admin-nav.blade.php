<ul class="nav nav-pills card-header-pills">
    <li class="nav-item">
        <a class="nav-link @if( request()->routeIs(['admin.config'])) active @endif" href="{{ route('admin.config') }}"><i class="ri-list-settings-fill"></i> 设置</a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if( request()->routeIs(['admin.account.list'])) active @endif"
           href="{{ route('admin.account.list') }}"><i class="ri-list-ordered"></i> 账号列表</a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if( request()->routeIs(['admin.profile'])) active @endif"
           href="{{ route('admin.profile') }}"><i class="ri-profile-fill"></i> 账户</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{  route('admin.logs') }}"><i class="ri-bug-fill"></i> 日志</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{  route('cache.clear') }}"><i class="ri-delete-bin-fill"></i> 清理缓存</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" target="_blank" href="https://github.com/WangNingkai/OLAINDEX/issues/new/choose"><i class="ri-github-fill"></i> 反馈</a>
    </li>
</ul>
