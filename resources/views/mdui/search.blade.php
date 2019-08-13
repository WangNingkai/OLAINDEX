@extends('mdui.layouts.main')
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/store@2/dist/store.everything.min.js"></script>
    <script src="https://cdn.staticfile.org/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
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
@section('breadcrumb')
    <label class="mdui-switch" style="position: absolute;right: 0">
        <img src="https://i.loli.net/2018/12/04/5c05f0c25aebd.png"
             style="width: 18px;position: relative;top: 5px;right: 5px;" alt="切换">
        <input class="display-type" id="display-type-chk" type="checkbox"/>
        <i class="mdui-switch-icon"></i>
    </label>
@stop
@section('content')
    <div class="mdui-container-fluid">
        <form action="{{ route('search') }}">
            <div class="mdui-row mdui-m-t-3 mdui-m-b-3">
                <div class="mdui-col-xs-12 mdui-col-sm-1 mdui-col-md-2"></div>
                <div class="mdui-textfield mdui-textfield-floating-label mdui-col-xs-12 mdui-col-sm-10 mdui-col-md-8">
                    <label class="mdui-textfield-label" for="search_field">搜索</label>
                    <input class="mdui-textfield-input" id="search_field" name="keywords" type="text"
                           value="{{ request()->get('keywords') }}"/>
                </div>
                <div class="mdui-col-xs-12 mdui-col-sm-1 mdui-col-md-2"></div>
            </div>
        </form>

        @if(!blank($items))
            <div class="mdui-row list-view mdui-hidden">
                <ul class="mdui-list">
                    <li class="mdui-list-item th">
                        <div class="mdui-col-xs-12 mdui-col-sm-7">文件</div>
                        <div class="mdui-col-sm-3 mdui-text-right">修改时间</div>
                        <div class="mdui-col-sm-2 mdui-text-right">大小</div>
                    </li>

                    @foreach($items as $item)
                        <li class="mdui-list-item file mdui-ripple">
                            <a href="{{ route('search.show',$item['id']) }}"
                               target="_blank">
                                <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                    <i class="mdui-icon material-icons">{{ \App\Utils\Extension::getFileIcon($item['ext']) }}</i>
                                    {{ $item['name'] }}
                                </div>
                                <div
                                    class="mdui-col-sm-3 mdui-text-right">{{ date('M d H:i',strtotime($item['lastModifiedDateTime'])) }}</div>
                                <div
                                    class="mdui-col-sm-2 mdui-text-right">{{ \Illuminate\Support\Arr::has($item,'folder')? '-' : \App\Utils\Tool::convertSize($item['size']) }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div style="margin-top: 20px;"
                 class="thumb-view mdui-row-xs-3 mdui-row-sm-4 mdui-row-md-5 mdui-row-lg-6 mdui-row-xl-7 mdui-grid-list mdui-hidden">
                @foreach($items as $item)
                    <div class="mdui-col file">
                        <a target="_blank"
                           href="{{ route('search.show',$item['id']) }}">
                            <div class="col-icon">
                                @if(in_array($item['ext']??'',['bmp','jpg','jpeg','png','gif']))
                                    <img class="lazy"
                                         data-original="{{ route('thumb',['id'=>$item['id'],'size'=>'small']) }}"
                                         src="https://i.loli.net/2018/12/04/5c0625755d6ce.gif" alt="">
                                @else
                                    <img style="height: 80%;"
                                         src="https://i.loli.net/2018/12/07/5c09d6920dedb.png" alt="">
                                @endif
                            </div>
                            <div class="col-detail" style="text-align: center">
                                <div class="col-title" title="{{ $item['name'] }}">
                                    {{ $item['name'] }}
                                </div>
                                <br/>
                                <div class="col-date">
                                    {{ date('M d H:i',strtotime($item['lastModifiedDateTime'])) }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            {{ $items->appends(['keywords' => request()->get('keywords'),'limit' => request()->get('limit')])->links('mdui.page') }}
        @endif

    </div>
@stop

