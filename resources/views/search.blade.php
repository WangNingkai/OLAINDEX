@extends('layouts.main')
@section('title','搜索：'.request()->get('keywords'))
@section('content')
    @if (blank($items))
        <div class="card border-light mb-3">
            <div class="card-body">
                <p class="text-danger">搜索结果为空</p>
            </div>
        </div>
    @else
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
                    <div class="col d-none d-md-block d-md-none">
                        <span class="pull-right">Action</span>
                    </div>
                </div>
            </div>
            <div class="list-group item-list">
                @foreach($items as $item)
                    <li class="list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('show',\App\Helpers\Tool::handleUrl(id2path($item['id']))) }}"
                                   title="{{ $item['name'] }}">
                                    <i class="fa {{\App\Helpers\Tool::getExtIcon($item['ext'])}}"></i> {{ \App\Helpers\Tool::subStr($item['name'],0,20) }}
                                </a>
                            </div>
                            <div class="col d-none d-md-block d-md-none">
                                <span
                                    class="pull-right">{{ date('Y-m-d H:i:s',strtotime($item['lastModifiedDateTime'])) }}</span>
                            </div>
                            <div class="col d-none d-md-block d-md-none">
                                <span class="pull-right">{{ \App\Helpers\Tool::convertSize($item['size']) }}</span>
                            </div>
                            <div class="col d-none d-md-block d-md-none">
                                <span class="pull-right">
                                    <a href="{{ route('download',\App\Helpers\Tool::handleUrl(id2path($item['id']))) }}"><i
                                            class="fa fa-download"
                                            title="下载"></i></a>&nbsp;&nbsp;
                                    <a href="javascript:void(0)"
                                       data-clipboard-text="{{ route('download',\App\Helpers\Tool::handleUrl(id2path($item['id']))) }}"
                                       data-clipboard-text="{{ route('download',\App\Helpers\Tool::handleUrl(id2path($item['id']))) }}"
                                       class="clipboard"
                                       title="已复制" data-toggle="tooltip"
                                       data-placement="right"><i class="fa fa-clipboard"></i></a>&nbsp;&nbsp;
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </div>
        </div>
        <div class="text-center">
            {{ $items->appends(['keywords' => request()->get('keywords')])->links('page') }}
        </div>
    @endif
@stop
