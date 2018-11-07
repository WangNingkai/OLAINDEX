@extends('layouts.main')
@section('title','Home/'.implode('/',$path_array))
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/css/blueimp-gallery-indicator.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/css/blueimp-gallery.min.css">
@stop
@section('content')
    @include('breadcrumb')
    @if (!blank($head))
        <div class="card border-light mb-3">
            <div class="card-header"><i class="fa fa-hashtag"></i> HEAD</div>
            <div class="card-body markdown-body">
                {!! $head !!}
            </div>
        </div>
    @endif
    <div class="card border-light mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    文件
                </div>
                <div class="col d-none d-md-block d-md-none">
                    <span class="pull-right">修改日期</span>
                </div>
                <div class="col">
                    <span class="pull-right">大小</span>
                </div>
                <div class="col">
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
                            @if (!array_key_exists('.password', $origin_items))
                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                   data-target="#lockFolderModal"><i class="fa fa-lock"></i> 加密目录</a>
                            @endif
                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                               data-target="#newFolderModal"><i class="fa fa-plus-circle"></i> 新建目录</a>
                        </div>
                        @if (!array_key_exists('.password', $origin_items))
                            <div class="modal fade" id="lockFolderModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('admin.lock') }}" method="post">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="fa fa-lock"></i> 加密目录</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-danger">确认锁定目录，请输入密码(默认密码 12345678)：</p>
                                                <div class="form-group">
                                                    <input type="password" name="password" class="form-control"
                                                           placeholder="请输入密码" id="lockField" required>
                                                    <input type="hidden" name="path"
                                                           value="{{ encrypt($origin_path) }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">确定</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    取消
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
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
                    @else
                        <span class="pull-right">操作</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="list-group item-list">
            @if(!blank($path_array))
                <li class="list-group-item list-group-item-action"><a
                        href="{{ route('home',\App\Helpers\Tool::handleUrl(\App\Helpers\Tool::getParentUrl($path_array))) }}"><i
                            class="fa fa-level-up"></i> 返回上一层</a></li>
            @endif
            @foreach($items as $item)
                <li class="list-group-item list-group-item-action">
                    <div class="row">
                        <div class="col" style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                            @if(array_has($item,'folder'))
                                <a href="{{ route('home',\App\Helpers\Tool::handleUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                                   title="{{ $item['name'] }}">
                                    <i class="fa fa-folder"></i> {{ $item['name'] }}
                                </a>
                            @else
                                <a href="{{ route('show',\App\Helpers\Tool::handleUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                                   title="{{ $item['name'] }}">
                                    <i class="fa {{ \App\Helpers\Tool::getExtIcon($item['ext'] ?? '') }}"></i> {{ $item['name'] }}
                                </a>
                            @endif
                        </div>
                        <div class="col d-none d-md-block d-md-none">
                            <span
                                class="pull-right">{{ date('Y-m-d H:i:s',strtotime($item['lastModifiedDateTime'])) }}</span>
                        </div>
                        <div class="col">
                            <span
                                class="pull-right">{{ array_has($item,'folder')? '-' : \App\Helpers\Tool::convertSize($item['size']) }}</span>
                        </div>
                        <div class="col">
                            <span class="pull-right">
                                @if(!array_has($item,'folder'))
                                    @if(array_has($item,'image'))
                                        <a href="{{ route('view',\App\Helpers\Tool::handleUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                                           data-fancybox="image-list"><i
                                                class="fa fa-eye" title="查看"></i></a>&nbsp;&nbsp;
                                    @endif
                                    @if(session()->has('LogInfo') && \App\Helpers\Tool::canEdit($item) )
                                        <a href="{{ route('file.update',$item['id']) }}"><i
                                                class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                    @endif
                                    <a href="{{ route('download',\App\Helpers\Tool::handleUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"><i
                                            class="fa fa-download"
                                            title="下载"></i></a>&nbsp;&nbsp;
                                @else
                                    <a href="{{ route('home',\App\Helpers\Tool::handleUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
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
    <div class="text-center">
        {{ $items->links('page') }}
    </div>
    @if ($hasImage)
        <div class="card border-light mb-3">
            <div class="card-header">
                图片列表
            </div>
            <div class="card-body">
                <div id="links">
                    @foreach($items as $item)
                        @if(array_has($item,'image'))
                            <a href="{{ route('view',$origin_path ? $origin_path.'/'.$item['name'] : $item['name']) }}"
                               title="{{ $item['name'] }}" data-gallery="image-list">
                                <img src="{{ route('thumb',['id'=>$item['id'],'size'=>'small']) }}"
                                     alt="{{ $item['name'] }}">
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
            <div class="card-header"><i class="fa fa-book"></i> README</div>
            <div class="card-body markdown-body">
                {!! $readme !!}
            </div>
        </div>
    @endif
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/js/jquery.blueimp-gallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/js/blueimp-gallery-indicator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blueimp-gallery@2/js/blueimp-gallery-fullscreen.min.js"></script>
    @if(session()->has('LogInfo'))
        <script>
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
                        window.open('/item/delete/' + $sign, '_blank');
                    } else if (result.dismiss === swal.DismissReason.cancel) {
                        swal('已取消', '文件安全 :)', 'error');
                    }
                })
            }
        </script>
    @endif
@stop
