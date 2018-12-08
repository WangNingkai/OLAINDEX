@extends('mdui.layouts.main')
@section('js')
    <script src="https://cdn.bootcss.com/store.js/1.3.20/store.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery_lazyload/1.9.7/jquery.lazyload.min.js"></script>
    <script>
        function getDirect() {
            $("#dl").val('');
            $(".dl_url").each(function () {
                let dl = decodeURI($(this).attr("href"));
                let url = dl + "\n";
                let origin = $("#dl").val();
                $("#dl").val(origin + url);
            });
        }

        $(function () {
            let display_type = store.get('display_type');
            if (typeof (display_type) == "undefined" || display_type === null) {
                display_type = "table";
            }
            if (display_type === 'table') {
                $('.thumb-view').removeClass('mdui-hidden');
                $('img.lazy').lazyload();
                $('#display-type-chk').attr('checked', true);
            } else {
                $('.list-view').removeClass('mdui-hidden');
                $('#display-type-chk').attr('checked', false);
            }

            $('.display-type').on('change', function () {
                if (display_type !== 'table') {
                    store.set('display_type', 'table');
                } else {
                    store.set('display_type', 'list');
                }
                window.location.reload();
            });
        });
    </script>
@stop
@section('content')
    <div class="mdui-container-fluid">
        @if (!blank($head))
            <div class="mdui-typo mdui-p-t-3">
                {!! $head !!}
            </div>
        @endif

        <div class="mdui-row list-view mdui-hidden">
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
                                <i class="mdui-icon material-icons">subdirectory_arrow_left</i>
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
                    @if(array_has($item,'file'))
                        <a class="dl_url mdui-hidden"
                           href="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"></a>
                    @endif
                @endforeach
            </ul>
        </div>

        <div
            class="mdui-m-t-3 thumb-view mdui-row-xs-3 mdui-row-sm-4 mdui-row-md-5 mdui-row-lg-6 mdui-row-xl-7 mdui-grid-list mdui-hidden">
            @if(!blank($path_array))
                <div class="mdui-col">
                    <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getParentUrl($path_array))) }}">
                        <div class="col-icon">
                            <img src="https://i.loli.net/2018/12/07/5c09d7355ea27.png" alt="">
                        </div>
                        <div class="col-detail mdui-text-center">
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
                                    src=" https://i.loli.net/2018/12/07/5c09d6920f8ac.png"
                                    alt="">
                            </div>
                            <div class="col-detail mdui-text-center">
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
                                    <img src="https://i.loli.net/2018/12/07/5c09d6920dedb.png" alt="">
                                @endif
                            </div>
                            <div class="col-detail mdui-text-center">
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

        {{ $items->appends(['limit' => request()->get('limit')])->links('mdui.page') }}

        @if (!blank($readme))
            <div class="mdui-typo mdui-shadow-3 mdui-p-a-2 mdui-m-a-2">
                <div class="mdui-chip mdui-m-a-2">
                    <span class="mdui-chip-icon"><i class="mdui-icon material-icons">face</i></span>
                    <span class="mdui-chip-title">README.md</span>
                </div>
                {!! $readme !!}
            </div>
        @endif

        @if (session()->has('LogInfo'))
            <div class="mdui-fab-wrapper" mdui-fab>
                <button class="mdui-fab mdui-ripple mdui-color-theme-accent">
                    <i class="mdui-icon material-icons">add</i>
                    <i class="mdui-icon mdui-fab-opened material-icons">close</i>
                </button>
                <div class="mdui-fab-dial">
                    <a class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-green"
                       href="@if (array_key_exists('HEAD.md', $origin_items))
                       {{ route('admin.file.update',$origin_items['HEAD.md']['id']) }}
                       @else
                       {{ route('admin.file.create',['name' => 'HEAD', 'path' => encrypt($origin_path)]) }}
                       @endif"
                       target="_blank"><i class="mdui-icon material-icons">bookmark</i></a>
                    <a class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-green"
                       href="@if (array_key_exists('README.md', $origin_items))
                       {{ route('admin.file.update',$origin_items['README.md']['id']) }}
                       @else
                       {{ route('admin.file.create',['name' => 'README', 'path' => encrypt($origin_path)]) }}
                       @endif"
                       target="_blank"><i class="mdui-icon material-icons">face</i></a>
                    @if (!array_key_exists('.password', $origin_items))
                        <a href="javascript:void(0)" class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-purple"><i
                                class="mdui-icon material-icons" mdui-dialog="{target: '#lockFolder'}">lock</i></a>
                    @endif
                    <a href="javascript:void(0)" class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-red"><i
                            class="mdui-icon material-icons" mdui-dialog="{target: '#newFolder'}">create_new_folder</i>
                    </a>
                    <a href="javascript:void(0)" class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-blue"><i
                            class="mdui-icon material-icons" mdui-dialog="{target: '#exportDirect'}">list</i>
                    </a>
                </div>
            </div>
            @if (!array_key_exists('.password', $origin_items))
                <div class="mdui-dialog" id="lockFolder">
                    <form action="{{ route('admin.lock') }}" method="post">
                        @csrf
                        <div class="mdui-dialog-content">
                            <div class="mdui-dialog-title">加密目录</div>
                            <p class="mdui-text-color-red">确认锁定目录，请输入密码(默认密码 12345678)：</p>
                            <div class="mdui-textfield mdui-textfield-floating-label">
                                <i class="mdui-icon material-icons">lock</i>
                                <label class="mdui-textfield-label" for="lockField">请输入密码</label>
                                <input name="password" class="mdui-textfield-input" type="password" id="lockField"
                                       required/>
                                <input type="hidden" name="path"
                                       value="{{ encrypt($origin_path) }}">
                            </div>
                        </div>
                        <div class="mdui-dialog-actions">
                            <button class="mdui-btn mdui-ripple" mdui-dialog-close>取消</button>
                            <button class="mdui-btn mdui-ripple" mdui-dialog-submit>确认</button>
                        </div>
                    </form>
                </div>
            @endif
            <div class="mdui-dialog" id="newFolder">
                <form action="{{ route('admin.folder.create') }}" method="post">
                    @csrf
                    <div class="mdui-dialog-content">
                        <div class="mdui-dialog-title">新建目录</div>
                        <p class="mdui-text-color-red">请确保目录名的唯一性，如果存在相同名称，服务器会自动选择新的名称。</p>
                        <p class="mdui-text-color-red">文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ |。</p>
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <i class="mdui-icon material-icons">create_new_folder</i>
                            <label class="mdui-textfield-label" for="folderName">请输入目录名称</label>
                            <input name="name" class="mdui-textfield-input" type="text" id="folderName"
                                   required/>
                            <input type="hidden" name="path" value="{{ encrypt($origin_path) }}">
                        </div>
                    </div>
                    <div class="mdui-dialog-actions">
                        <button class="mdui-btn mdui-ripple" mdui-dialog-close>取消</button>
                        <button class="mdui-btn mdui-ripple" mdui-dialog-submit>确认</button>
                    </div>
                </form>
            </div>

            <div class="mdui-dialog" id="exportDirect">

                <div class="mdui-dialog-content">
                    <div class="mdui-dialog-title">导出直链</div>
                    <p class="mdui-text-color-red">
                        链接将在 {{ date('m/d/Y H:i', \App\Helpers\Tool::config('access_token_expires')) }}
                        后失效</p>
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label" for="dl">链接</label>
                        <textarea name="urls" id="dl" class="mdui-textfield-input" rows="3"></textarea>
                    </div>
                </div>
                <div class="mdui-dialog-actions">
                    <button class="mdui-btn mdui-ripple" mdui-dialog-close>取消</button>
                    <button class="mdui-btn mdui-ripple" onclick="getDirect()">点击获取</button>
                </div>
            </div>

        @endif

    </div>
@stop

