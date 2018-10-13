@extends('layouts.main')
@section('title','Root/'.implode('/',$pathArr))
@section('content')
    @include('breadcrumb')
    @if (!blank($head))
        <div class="card border-light mb-3">
            <div class="card-header">HEAD</div>
            <div class="card-body markdown-body">
                {!! $head !!}
            </div>
        </div>
    @endif
    <div class="card border-light mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    File
                </div>
                <div class="col d-none d-md-block d-md-none">
                    <span class="pull-right">LastModifiedDateTime</span>
                </div>
                <div class="col d-none d-md-block d-md-none">
                    <span class="pull-right">Size</span>
                </div>
                <div class="col">
                    <span class="pull-right">Action</span>
                </div>
            </div>
        </div>
        <div class="list-group item-list">
            @if(!blank($pathArr))
                <li class="list-group-item list-group-item-action"><a href="{{ route('list',\App\Helpers\Tool::getParentUrl($pathArr)) }}"><i class="fa fa-arrow-left"></i> 返回上一层</a></li>
            @endif
            @foreach($items as $item)
                <li class="list-group-item list-group-item-action">
                    <div class="row">
                        <div class="col">
                            @if(isset($item['folder']))
                                <a href="{{ route('list',$path ? $path.'|'.$item['name'] : $item['name']) }}" title="{{ $item['name'] }}">
                                    <i class="fa fa-folder"></i> {{ \App\Helpers\Tool::subStr($item['name'],0,20) }}
                                </a>
                            @else
                                <a href="{{ route('item',$item['id']) }}" title="{{ $item['name'] }}">
                                    <i class="fa {{\App\Helpers\Tool::getExtIcon($item['ext'])}}"></i>  {{ \App\Helpers\Tool::subStr($item['name'],0,20) }}
                                </a>
                            @endif
                        </div>
                        <div class="col d-none d-md-block d-md-none">
                            <span class="pull-right">{{ date('Y-m-d H:i:s',strtotime($item['lastModifiedDateTime'])) }}</span>
                        </div>
                        <div class="col d-none d-md-block d-md-none">
                            <span class="pull-right">{{ \App\Helpers\Tool::convertSize($item['size']) }}</span>
                        </div>
                        <div class="col">
                            <span class="pull-right">
                                @if(isset($item['folder']))
                                    <a href="javascript:void(0)" data-clipboard-text="{{ route('list',$path ? $path.'|'.$item['name'] : $item['name']) }}" class="clipboard" title="已复制" data-toggle="tooltip"
                                       data-placement="right" ><i class="fa fa-clipboard"></i></a>&nbsp;&nbsp;
                                @else
                                    @if(isset($item['image']))
                                        <a href="{{ route('origin.view',$item['id']) }}" data-fancybox="image-list"><i class="fa fa-eye" title="查看"></i></a>&nbsp;&nbsp;
                                    @endif
                                    <a href="{{ route('download',$item['id']) }}"><i class="fa fa-download" title="下载"></i></a>&nbsp;&nbsp;
                                    <a href="javascript:void(0)" data-clipboard-text="{{ route('download',$item['id']) }}" class="clipboard" title="已复制" data-toggle="tooltip"
                                       data-placement="right" ><i class="fa fa-clipboard"></i></a>&nbsp;&nbsp;
                                @endif
                                @if (session()->has('LogInfo') && in_array($item['name'],['.password','README.md','HEAD.md']))
                                    <a onclick="javascript:return confirm('确定删除吗')"  href="{{ route('delete',encrypt($item['id'] . '.' . encrypt($item['eTag']))) }}" target="_blank"><i class="fa fa-trash" title="删除" data-toggle="modal" data-target="#deleteFileModal"></i></a>&nbsp;&nbsp;

                                @endif
                            </span>
                        </div>
                    </div>
                </li>
            @endforeach
        </div>
    </div>
    @if (!blank($readme))
        <div class="card border-light mb-3">
            <div class="card-header">README</div>
            <div class="card-body markdown-body">
                {!! $readme !!}
            </div>
        </div>
    @endif
    @if (session()->has('LogInfo'))

        <ul id="menu" class="mfb-component--bl mfb-zoomin" data-mfb-toggle="click">
            <li class="mfb-component__wrap">
                <a href="#" class="mfb-component__button--main">
                    <i class="mfb-component__main-icon--resting ion-plus-round"></i>
                    <i class="mfb-component__main-icon--active ion-close-round"></i>
                </a>
                <ul class="mfb-component__list">
                    @if (!array_key_exists('.password', $items))
                        <li>
                            <a href="#" data-mfb-label="加密目录" class="mfb-component__button--child" data-toggle="modal" data-target="#loclFolderModal">
                                <i class="mfb-component__child-icon ion-locked"></i>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="#" data-mfb-label="添加/编辑 head" class="mfb-component__button--child">
                            <i class="mfb-component__child-icon ion-edit"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" data-mfb-label="添加/编辑 readme" class="mfb-component__button--child">
                            <i class="mfb-component__child-icon ion-compose"></i>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        @if (!array_key_exists('.password', $items))
            <div class="modal fade" id="loclFolderModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('lock') }}" method="post">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">加密目录</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p class="text-danger">确认锁定目录，请输入密码(默认密码 12345678)：</p>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="请输入密码" id="lockField" required>
                                    <input type="hidden" name="path" value="{{ encrypt($path) }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">确定</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
@stop
