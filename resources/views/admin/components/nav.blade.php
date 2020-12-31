<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <li class="nav-item @if( request()->routeIs(['admin']))active @endif">
                        <a class="nav-link" href="{{ route('admin') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="icon" width="24" height="24"
                                         viewBox="0 0 24 24" stroke-width="2"
                                         stroke="currentColor" fill="none"
                                         stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <polyline points="5 12 3 12 12 3 21 12 19 12"/>
                                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>
                                    </svg>
                                </span>
                            <span class="nav-link-title"> 首页</span>
                        </a>
                    </li>

                    <li class="nav-item @if( request()->routeIs(['admin.config']))active @endif">
                        <a class="nav-link" href="{{ route('admin.config') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="icon" width="24" height="24"
                                         viewBox="0 0 24 24" stroke-width="2"
                                         stroke="currentColor" fill="none"
                                         stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"/>
                                        <line x1="12" y1="12" x2="20" y2="7.5"/>
                                        <line x1="12" y1="12" x2="12" y2="21"/>
                                        <line x1="12" y1="12" x2="4" y2="7.5"/>
                                        <line x1="16" y1="5.25" x2="8" y2="9.75"/>
                                    </svg>
                                </span>
                            <span class="nav-link-title">设置</span>
                        </a>
                    </li>

                    <li class="nav-item @if( request()->routeIs(['admin.account.list','admin.account.config','admin.account.manage']))active @endif">
                        <a class="nav-link" href="{{ route('admin.account.list') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="9" y1="6" x2="20" y2="6"/>
                                        <line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/>
                                        <line x1="5" y1="6" x2="5" y2="6.01"/><line x1="5" y1="12" x2="5" y2="12.01"/>
                                        <line x1="5" y1="18" x2="5" y2="18.01"/>
                                    </svg>
                                </span>
                            <span class="nav-link-title">账号管理</span>
                        </a>
                    </li>

                    <li class="nav-item @if( request()->routeIs(['admin.url.list',]))active @endif">
                        <a class="nav-link" href="{{ route('admin.url.list') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round"><path stroke="none"
                                                                                             d="M0 0h24v24H0z"
                                                                                             fill="none"/><path
                                           d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5"/><path
                                           d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5"/></svg>
                                </span>
                            <span class="nav-link-title">短链管理</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{  route('cache.clear') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"><path
                                        stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline
                                        points="9 11 12 14 20 6"/><path
                                        d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"/></svg>
                            </span>
                            <span class="nav-link-title">清理缓存</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{  route('admin.logs') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"><path
                                        stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path
                                        d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><line
                                        x1="9"
                                        y1="9"
                                        x2="10"
                                        y2="9"/><line
                                        x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                            </span>
                            <span class="nav-link-title">日志</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
