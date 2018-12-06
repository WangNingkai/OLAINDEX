@extends('layouts.main-ext')
@section('breadcrumb')
    @include('breadcrumb-ext',['switch' => true])
@stop
@section('content')
    <div class="mdui-container-fluid">

        @if (!blank($head))
            <div class="mdui-typo" style="padding: 20px;">
                {!! $head !!}
            </div>
        @endif

        <div class="mdui-row list-view" style=" display: none;">
            <ul class="mdui-list">
                <li class="mdui-list-item th">
                    <div class="mdui-col-xs-12 mdui-col-sm-7">文件</div>
                    <div class="mdui-col-sm-3 mdui-text-right">修改时间</div>
                    <div class="mdui-col-sm-2 mdui-text-right">大小</div>
                </li>
                @if(!blank($path_array))
                    <li class="mdui-list-item mdui-ripple">
                        <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getParentUrl($path_array))) }}">
                            <div class="mdui-col-xs-12 mdui-col-sm-7">
                                <i class="mdui-icon material-icons">arrow_upward</i>
                                返回上一层
                            </div>
                            <div class="mdui-col-sm-3 mdui-text-right"></div>
                            <div class="mdui-col-sm-2 mdui-text-right"></div>
                        </a>
                    </li>
                @endif

                @foreach($items as $item)
                    @if(array_has($item,'folder'))
                        <li class="mdui-list-item mdui-ripple">
                            <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}">
                                <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                    <i class="mdui-icon material-icons">folder_open</i>
                                    {{ $item['name'] }}
                                </div>
                                <div
                                    class="mdui-col-sm-3 mdui-text-right">{{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}</div>
                                <div
                                    class="mdui-col-sm-2 mdui-text-right">{{ array_has($item,'folder')? '-' : \App\Helpers\Tool::convertSize($item['size']) }}</div>
                            </a>
                        </li>
                    @else
                        <li class="mdui-list-item file mdui-ripple">
                            <a href="{{ route('show',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                               target="_blank">
                                <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                    <i class="mdui-icon material-icons">{{ \App\Helpers\Tool::fileIcon($item['ext']) }}</i>
                                    {{ $item['name'] }}
                                </div>
                                <div
                                    class="mdui-col-sm-3 mdui-text-right">{{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}</div>
                                <div
                                    class="mdui-col-sm-2 mdui-text-right">{{ array_has($item,'folder')? '-' : \App\Helpers\Tool::convertSize($item['size']) }}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div style="margin-top: 20px; display: none;"
             class="thumb-view mdui-row-xs-1 mdui-row-sm-4 mdui-row-md-5 mdui-row-lg-6 mdui-row-xl-7 mdui-grid-list">
            @if(!blank($path_array))
                <div class="mdui-col">
                    <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getParentUrl($path_array))) }}">
                        <div class="col-icon">
                            <img src="https://i.loli.net/2018/12/06/5c08bdb97d83f.png" style="height: 80%;" alt="">
                        </div>
                        <div class="col-detail" style="text-align: center">
                            <div class="col-title">
                                ...
                            </div>
                            <br/>
                            <div class="col-date">
                                返回上一层
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @foreach($items as $item)
                @if(array_has($item,'folder'))
                    <div class="mdui-col">
                        <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}">
                            <div class="col-icon">
                                <img
                                    src="https://i.loli.net/2018/12/06/5c08bd74d8070.png"
                                    alt="">
                            </div>
                            <div class="col-detail" style="text-align: center">
                                <div class="col-title">
                                    {{ $item['name'] }}
                                </div>
                                <br/>
                                <div class="col-date">
                                    {{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}
                                </div>
                            </div>
                        </a>
                    </div>
                @else
                    <div class="mdui-col file">
                        <a target="_blank"
                           href="{{ route('show',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}">
                            <div class="col-icon">
                                @if(in_array($item['ext'],['bmp','jpg','jpeg','png','gif']))
                                    <img class="lazy"
                                         data-original="{{ route('thumb',['id'=>$item['id'],'size'=>'small']) }}"
                                         src="https://i.loli.net/2018/12/04/5c0625755d6ce.gif" alt="">
                                @else
                                    <img style="height: 80%;"
                                         src="https://i.loli.net/2018/12/06/5c08bc7027dbc.png" alt="">
                                @endif
                            </div>
                            <div class="col-detail" style="text-align: center">
                                <div class="col-title" title="{{ $item['name'] }}">
                                    {{ $item['name'] }}
                                </div>
                                <br/>
                                <div class="col-date">
                                    {{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
        {{ $items->appends(['limit' => request()->get('limit')])->links('page-ext') }}

        @if (!blank($readme))
            <div class="mdui-typo mdui-shadow-3" style="padding: 20px;margin: 20px;">
                <div class="mdui-chip">
                    <span class="mdui-chip-icon"><i class="mdui-icon material-icons">face</i></span>
                    <span class="mdui-chip-title">README.md</span>
                </div>
                {!! $readme !!}
            </div>
        @endif

        <div class="mdui-fab-wrapper" mdui-fab>
            <button class="mdui-fab mdui-ripple mdui-color-theme-accent">
                <i class="mdui-icon material-icons">add</i>
                <i class="mdui-icon mdui-fab-opened material-icons">close</i>
            </button>
            <div class="mdui-fab-dial">
                <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-purple"><i
                        class="mdui-icon material-icons">lock</i></button>
                <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-red"><i class="mdui-icon material-icons">create_new_folder</i>
                </button>
                <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-green"><i class="mdui-icon material-icons">library_add</i>
                </button>
                <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-blue"><i class="mdui-icon material-icons">list</i>
                </button>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="https://cdn.bootcss.com/store.js/1.3.20/store.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery_lazyload/1.9.7/jquery.lazyload.min.js"></script>
    <script>
        $(function () {
            @if (session()->has('alertMessage'))
            mdui.snackbar({
                message: '{{ session()->pull('alertMessage') }}',
                position: 'right-top'
            });
                @endif
            let display_type = store.get('display_type');
            if (display_type !== 'table') {
                $('.list-view').hide();
                $('.thumb-view').show();
                $('img.lazy').lazyload();
                $('#display-type-chk').attr('checked', true);
            } else {
                $('.list-view').show();
                $('.thumb-view').hide();
            }
            $('.display-type').on('click', function () {
                if (display_type !== 'table') {
                    store.set('display_type', 'table');
                } else {
                    store.set('display_type', 'list');
                }
                window.location.reload();
            });
            $('img.lazy').lazyload();
        });
    </script>
@stop

