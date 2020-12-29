@extends('mdui.layouts.main')
@section('title', setting('site_name','OLAINDEX'))
@section('content')
    <div class="mdui-row mdui-shadow-3 mdui-p-a-1 mdui-m-y-3" style="border-radius: 8px">
        <ul class="mdui-list">
            <li class="mdui-list-item mdui-ripple">
                <div class="mdui-row mdui-col-xs-12">
                    <div class="mdui-col-xs-12 mdui-col-sm-7">
                        文件
                        @if(\App\Helpers\Tool::getOrderByStatus('name'))
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','name,asc') }}">
                                <i class="mdui-icon material-icons ">expand_more</i>
                            </a>
                        @else
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','name,desc') }}">
                                <i class="mdui-icon material-icons ">expand_less</i>
                            </a>
                        @endif
                    </div>
                    <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">
                        修改时间
                        @if(\App\Helpers\Tool::getOrderByStatus('lastModifiedDateTime'))
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','lastModifiedDateTime,asc') }}">
                                <i class="mdui-icon material-icons ">expand_more</i>
                            </a>
                        @else
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','lastModifiedDateTime,desc') }}">
                                <i class="mdui-icon material-icons ">expand_less</i>
                            </a>
                        @endif

                    </div>
                    <div class="mdui-col-sm-2 mdui-hidden-sm-down mdui-text-right">
                        大小
                        @if(\App\Helpers\Tool::getOrderByStatus('size'))
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','size,asc') }}">
                                <i class="mdui-icon material-icons ">expand_more</i>
                            </a>
                        @else
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','size,desc') }}">
                                <i class="mdui-icon material-icons ">expand_less</i>
                            </a>
                        @endif
                    </div>
                </div>
            </li>

            <li class="mdui-list-item mdui-ripple">
                <div class="mdui-col-sm-12">
                    <form action="" method="get">
                        <div class="mdui-textfield">
                            <i class="mdui-icon material-icons">search</i>

                            <input class="mdui-textfield-input" type="text" id="keywords"
                                   name="keywords" placeholder="搜索目录资源"/>
                        </div>
                    </form>
                </div>
            </li>
            @if(!blank($path))
                <li class="mdui-list-item mdui-ripple">
                    <div class="mdui-col-sm-12">
                        <a href="{{ route('drive.query', ['hash' => $hash, 'query' => \App\Helpers\Tool::fetchGoBack($path)]) }}">
                            <i class="mdui-icon material-icons">arrow_back</i>
                            返回上级
                        </a>
                    </div>
                </li>
            @endif
            @if(blank($list))
                <li class="mdui-list-item mdui-ripple">
                    <div class="mdui-col-sm-12">
                        <i class="mdui-icon material-icons">info</i> 没有更多数据呦
                    </div>
                </li>
            @else
                @foreach($list as $data)
                    <li class="mdui-list-item mdui-ripple"
                        data-route="{{ route('drive.query', ['hash' => $hash, 'query' => implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) )]) }}">
                        <div class="mdui-row mdui-col-sm-12">
                            <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                @if (array_has($data,'folder') )
                                    <a
                                        data-name="{{ $data['name'] }}"
                                        href="{{ route('drive.query', ['hash' => $hash, 'query' => implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) )]) }}"
                                        aria-label="Folder"
                                    >
                                        <i class="mdui-icon material-icons">folder_open</i>
                                        <span> {{ $data['name'] }}</span>
                                    </a>
                                @else
                                    <a
                                        data-name="{{ $data['name'] }}"
                                        href="{{ route('drive.query', ['hash' => $hash, 'query' => implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) )]) }}"
                                        aria-label="File"
                                    >
                                        <i class="mdui-icon material-icons"> insert_drive_file </i>
                                        <span> {{ $data['name'] }}</span>
                                    </a>
                                @endif

                            </div>
                            <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">
                                {{ date('Y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}
                            </div>
                            <div class="mdui-col-sm-2 mdui-hidden-sm-down mdui-text-right">
                                {{ convert_size($data['size']) }}
                            </div>
                        </div>
                        @if (!array_has($data,'folder') )
                            <a class="mdui-btn mdui-ripple mdui-btn-icon mdui-hidden-sm-down download"
                               title="下载"
                               aria-label="Download"
                               href="javascript:void(0)"
                               data-route="{{ route('drive.query', ['hash' => $hash, 'query' => implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) ),'download' => 1]) }}"
                               target="_blank">
                                <i class="mdui-icon material-icons">file_download</i>
                            </a>
                        @endif
                    </li>
                @endforeach
                <li class="mdui-list-item mdui-ripple">
                    <div class="mdui-col-sm-12 mdui-typo-body-1-opacity">
                        {{ array_get($item,'folder.childCount',0) }}
                        个项目
                        {{ convert_size(array_get($item,'size',0)) }}
                    </div>

                </li>
            @endif
        </ul>
        {{ $list->appends(['sortBy'=> request()->get('sortBy'),'keywords' => request()->get('keywords')])->links('mdui.components.page') }}
    </div>
    @if (!blank($doc['readme']))
        <div class="mdui-card mdui-m-y-2 mdui-shadow-0">
            <div class="mdui-card-header">
                <div class="mdui-chip">
                    <span class="mdui-chip-icon"> <i class="mdui-icon material-icons">lightbulb_outline</i></span>
                    <span class="mdui-chip-title">README</span>
                </div>
            </div>
            <div class="mdui-card-content markdown-body mdui-text-color-theme-text">
                {!! marked($doc['readme']) !!}
            </div>
        </div>
    @endif
@stop
@push('scripts')
    <script>
        $(function() {
            $('.mdui-list-item,.download').on('click', function(e) {
                if ($(this).attr('data-route')) {
                    window.location.href = $(this).attr('data-route')
                }
                e.stopPropagation()
            })
        })
    </script>
@endpush
