@extends('mdui.layouts.main')
@section('title', setting('site_name','OLAINDEX'))
@section('content')
    <div class="mdui-container mdui-m-y-5">
        <div class="breadcrumb mdui-p-x-2">
            <div class="mdui-chip" mdui-menu="{target: '#choose_user', subMenuTrigger: 'hover'}"
                 mdui-tooltip="{content: '切换账号'}">
                <span class="mdui-chip-icon">
                    <i class="mdui-icon material-icons">account_circle</i>
                </span>
                <span class="mdui-chip-title">主账号</span>
            </div>
            <ul id="choose_user" class="mdui-menu">
                @foreach($accounts as $key => $account)
                    <li class="mdui-menu-item">
                        <a class="mdui-ripple"
                           href="{{ route('drive.query',['hash' => $account['hash_id']]) }}">{{ $key + 1 .':'.$account['remark'] }}</a>
                    </li>
                @endforeach
            </ul>
            <span class="breadcrumb-item" data-route="{{ route('drive.query', ['hash' => $hash]) }}">
                <i class="mdui-icon material-icons mdui-icon-dark mdui-m-a-0">chevron_right</i>
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

        </div>
        <div class="mdui-card mdui-shadow-0">
            <div class="mdui-card-content">
                <ul class="mdui-list">
                    <li class="mdui-list-item">
                        <div class="mdui-list-item-content">
                            <div class="mdui-row">
                                <div class="mdui-col">
                                    <form action="" method="get">
                                        <div class="mdui-textfield mdui-textfield-expandable">
                                            <div class="mdui-textfield-icon mdui-btn mdui-btn-icon"><i
                                                    class="mdui-icon material-icons">search</i></div
                                            >
                                            <input class="mdui-textfield-input" type="text" id="keywords"
                                                   name="keywords"
                                                   placeholder="搜索目录资源"/>
                                            <div class="mdui-textfield-close mdui-btn mdui-btn-icon"><i
                                                    class="mdui-icon material-icons">close</i></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                    @if(!blank($path))
                        <li class="mdui-list-item">
                            <div class="mdui-list-item-content">
                                <a
                                    href="{{ route('drive.query', ['hash' => $hash, 'query' => \App\Helpers\Tool::fetchGoBack($path)]) }}">
                                    <i class="mdui-icon material-icons">arrow_back</i>
                                    返回上级
                                </a>
                            </div>
                        </li>
                    @endif
                    @if(blank($list))
                        <li class="mdui-list-item">
                            <div class="mdui-list-item-content">
                                <div class="mdui-list-item-title">
                                    <i class="mdui-icon material-icons">info</i> 没有更多数据呦
                                </div>
                            </div>
                        </li>
                    @else
                        @foreach($list as $data)
                            <li
                                class="list-item mdui-list-item mdui-ripple"
                                data-route="{{ route('drive.query', ['hash' => $hash, 'query' => implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) )]) }}"
                            >
                                <div class="mdui-list-item-content">
                                    <div class="mdui-list-item-title">
                                        <i class="mdui-icon material-icons">
                                            {{ array_has($data,'folder') ? 'folder_open' : 'insert_drive_file' }}
                                        </i>
                                        {{ $data['name'] }}
                                        @if (array_has($data,'file') )
                                            <a class="mdui-btn mdui-btn-icon mdui-float-right"
                                               mdui-tooltip="{content: '下载'}"
                                               href="{{ route('drive.query', ['hash' => $hash, 'query' => implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) ),'download' => 1]) }}">
                                                <i class="mdui-icon material-icons">file_download</i>
                                            </a>
                                        @endif
                                        <div class="mdui-list-item-text mdui-list-item-one-line">
                                            {{ convert_size($data['size']) }}
                                            /
                                            {{ date('Y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                        <li class="mdui-list-item mdui-ripple mdui-typo-body-1-opacity">
                            {{ array_get($item,'folder.childCount',0) }}
                            个项目
                            {{ convert_size(array_get($item,'size',0)) }}
                        </li>
                    @endif
                </ul>
                {{ $list->appends(['sortBy'=> request()->get('sortBy'),'keywords' => request()->get('keywords')])->links('mdui.components.page') }}
            </div>
        </div>
        @if (!blank($doc['readme']))
            <div class="mdui-card">
                <div class="mdui-card-header">
                    <div class="mdui-chip">
                        <span class="mdui-chip-icon"> <i class="mdui-icon material-icons">lightbulb_outline</i></span>
                        <span class="mdui-chip-title">README</span>
                    </div>
                </div>
                <div class="mdui-card-content markdown-body">
                    {!! marked($doc['readme']) !!}
                </div>
            </div>
        @endif
    </div>
    <a
        id="scrolltop"
        class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"
        onclick="toTop()"
    ><i class="mdui-icon material-icons">keyboard_arrow_up</i></a
    >
@stop
@push('scripts')
    <script>
        const toTop = () => {
            document.querySelector('#top').scrollIntoView({ behavior: 'smooth', block: 'start' })
        }
        window.addEventListener('scroll', () => {
            if ((document.body.scrollTop > 100 || document.documentElement.scrollTop > 100)) {
                if ($('#scrolltop').hasClass('mdui-fab-hide')) {
                    $('#scrolltop').removeClass('mdui-fab-hide')
                }
            } else {
                if (!$('#scrolltop').hasClass('mdui-fab-hide')) {
                    $('#scrolltop').addClass('mdui-fab-hide')
                }
            }
        })
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
