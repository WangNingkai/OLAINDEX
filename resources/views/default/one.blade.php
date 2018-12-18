@extends('default.layouts.main')
@section('title',\App\Helpers\Tool::config('name','OLAINDEX'))
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/css/blueimp-gallery-indicator.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/css/blueimp-gallery.min.css">
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/js/blueimp-helper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/js/blueimp-gallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/js/blueimp-gallery-indicator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/js/jquery.blueimp-gallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/js/blueimp-gallery-fullscreen.min.js"></script>
    <script>
        $(function () {
            blueimp.Gallery(carouselLinks, {
                container: '#image-list',
                carousel: true
            });
        });

        @if(session()->has('LogInfo'))
        function deleteItem($sign) {
            swal({
                title: '确定删除吗？',
                text: "删除后无法恢复",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定删除',
                cancelButtonText: '取消',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    window.open('/file/delete/' + $sign, '_blank');
                } else if (result.dismiss === swal.DismissReason.cancel) {
                    swal('已取消', '文件安全 :)', 'error');
                }
            })
        }

        function getDirect() {
            $("#dl").val('');
            $(".download_url").each(function () {
                let dl = decodeURI($(this).attr("href"));
                let url = dl + "\n";
                let origin = $("#dl").val();
                $("#dl").val(origin + url);
            });
        }
        @endif
    </script>
@stop
@section('content')
    @include('default.breadcrumb')
    @if (!blank($head))
        <div class="card border-light mb-3">
            <div class="card-header"><i class="fa fa-leaf"></i> HEAD</div>
            <div class="card-body markdown-body">
                {!! $head !!}
            </div>
        </div>
    @endif
    <div class="card border-light mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col-8 col-sm-6">
                    文件
                </div>
                <div class="col-sm-2 d-none d-md-block d-md-none">
                    <span class="pull-right">修改日期</span>
                </div>
                <div class="col-sm-2 d-none d-md-block d-md-none">
                    <span class="pull-right">大小</span>
                </div>
                <div class="col-4 col-sm-2">
                    @if (session()->has('LogInfo'))
                        <a class="pull-right dropdown-toggle btn btn-sm btn-primary" href="javascript:void(0)"
                           id="actionDropdownLink"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">操作</a>
                        <div class="dropdown-menu" aria-labelledby="actionDropdownLink">
                            @if (array_key_exists('README.md', $origin_items))
                                <a class="dropdown-item"
                                   href="{{ route('admin.file.update',$origin_items['README.md']['id']) }}"><i
                                        class="fa fa-pencil-square-o"></i> 编辑 README</a>
                            @else
                                <a class="dropdown-item"
                                   href="{{ route('admin.file.create',['name' => 'README', 'path' => encrypt($origin_path)]) }}"><i
                                        class="fa fa-plus-circle"></i> 添加
                                    README</a>
                            @endif
                            @if (array_key_exists('HEAD.md', $origin_items))
                                <a class="dropdown-item"
                                   href="{{ route('admin.file.update',$origin_items['HEAD.md']['id']) }}"><i
                                        class="fa fa-pencil-square-o"></i> 编辑 HEAD</a>

                            @else
                                <a class="dropdown-item"
                                   href="{{ route('admin.file.create',['name' => 'HEAD', 'path' => encrypt($origin_path)]) }}"><i
                                        class="fa fa-plus-circle"></i> 添加
                                    HEAD</a>
                            @endif
                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                               data-target="#newFolderModal"><i class="fa fa-plus-circle"></i> 新建目录</a>
                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                   data-target="#directLinkModal"><i class="fa fa-link"></i> 导出直链</a>
                        </div>
                        <div class="modal fade" id="newFolderModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('admin.folder.create') }}" method="post">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="fa fa-plus-circle"></i> 新建目录</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-danger">请确保目录名的唯一性，如果存在相同名称，服务器会自动选择新的名称。</p>
                                            <p class="text-danger">文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ |。</p>
                                            <div class="form-group">
                                                <input type="text" name="name" class="form-control" placeholder="请输入目录名"
                                                       required>
                                                <input type="hidden" name="path" value="{{ encrypt($origin_path) }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">确定</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="directLinkModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="fa fa-link"></i> 导出直链</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-danger">
                                            链接将在 {{ date('m/d/Y H:i', \App\Helpers\Tool::config('access_token_expires')) }}
                                            后失效</p>
                                        <p><a href="javascript:void(0)"
                                              style="text-decoration: none" data-toggle="tooltip"
                                              data-placement="right" data-clipboard-target="#dl"
                                              class="clipboard">点击复制</a></p>
                                        <label for="dl"><textarea name="urls" id="dl" class="form-control" cols="60"
                                                                  rows="15"></textarea></label>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" onclick="getDirect()" class="btn btn-primary">点击获取
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <span class="pull-right">操作</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="list-group item-list">
            @if(!blank($path_array))
                <li class="list-group-item list-group-item-action"><a
                        href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getParentUrl($path_array))) }}"><i
                            class="fa fa-level-up"></i> 返回上一层</a></li>
            @endif
            @foreach($items as $item)
                <li class="list-group-item list-group-item-action">
                    <div class="row">
                        <div class="col-8 col-sm-6" style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                            @if(array_has($item,'folder'))
                                <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                                   title="{{ $item['name'] }}">
                                    <i class="fa fa-folder"></i> {{ $item['name'] }}
                                </a>
                            @else
                                <a href="{{ route('show',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                                   title="{{ $item['name'] }}">
                                    <i class="fa {{ \App\Helpers\Tool::getExtIcon($item['ext'] ?? '') }}"></i> {{ $item['name'] }}
                                </a>
                            @endif
                        </div>
                        <div class="col-sm-2 d-none d-md-block d-md-none">
                            <span
                                class="pull-right">{{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}</span>
                        </div>
                        <div class="col-sm-2 d-none d-md-block d-md-none">
                            <span
                                class="pull-right">{{ array_has($item,'folder')? '-' : \App\Helpers\Tool::convertSize($item['size']) }}</span>
                        </div>
                        <div class="col-4 col-sm-2">
                            <span class="pull-right">
                                @if(!array_has($item,'folder'))
                                    @if(array_has($item,'image'))
                                        <a href="{{ route('view',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                                           data-fancybox="image-list"><i
                                                class="fa fa-eye" title="查看"></i></a>&nbsp;&nbsp;
                                    @endif
                                    @if(session()->has('LogInfo') && \App\Helpers\Tool::canEdit($item) )
                                        <a href="{{ route('admin.file.update',$item['id']) }}"><i
                                                class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                    @endif
                                    <a class="download_url"
                                       href="{{ route('download',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"><i
                                            class="fa fa-download"
                                            title="下载"></i></a>&nbsp;&nbsp;
                                @else
                                    <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                                       title="{{ $item['name'] }}"><i class="fa fa-folder-open"></i></a>&nbsp;&nbsp;
                                @endif
                                @if (session()->has('LogInfo'))
                                    <a onclick="deleteItem('{{ encrypt($item['id'] . '.' . encrypt($item['eTag'])) }}')"
                                       href="javascript:void(0)"><i class="fa fa-trash"
                                                                    title="删除"></i></a>&nbsp;
                                    &nbsp;
                                @endif
                            </span>
                        </div>
                    </div>
                </li>
            @endforeach
        </div>
    </div>
    <div>
        {{ $items->appends(['limit' => request()->get('limit')])->links('default.page') }}
    </div>
    @if ($hasImage && (int)\App\Helpers\Tool::config('image_view'))
        <div class="card border-light mb-3">
            <div class="card-header">
                看图
            </div>
            <div class="card-body">
                <div id="links">
                    @foreach($items as $item)
                        @if(array_has($item,'image'))
                            <a href="{{ route('view',$origin_path ? $origin_path.'/'.$item['name'] : $item['name']) }}"
                               title="{{ $item['name'] }}" data-gallery="#image-list">
                                <img class="lazy"
                                     data-original="{{ array_get($item,'thumbnails.0.small.url') }}"
                                     src="{{ asset('img/loading.gif') }}"
                                     alt="{{ $item['name'] }}" width="10%" height="10%">
                            </a>
                        @endif
                    @endforeach
                </div>
                <div id="blueimp-gallery" class="blueimp-gallery" data-start-slideshow="true" data-filter=":even">
                    <div class="slides"></div>
                    <h3 class="title"></h3>
                    <a class="prev">‹</a>
                    <a class="next">›</a>
                    <a class="close">×</a>
                    <a class="play-pause"></a>
                    <ol class="indicator"></ol>
                </div>
            </div>
        </div>
    @endif
    @if (!blank($readme))
        <div class="card border-light mb-3">
            <div class="card-header"><i class="fa fa-bookmark"></i> README</div>
            <div class="card-body markdown-body">
                {!! $readme !!}
            </div>
        </div>
    @endif
@stop

