@extends('default.layouts.main')
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
                        <span class="pull-right">操作</span>
                    </div>
                </div>
            </div>
            <div class="list-group item-list">
                @foreach($items as $item)
                    <li class="list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col-8 col-sm-6"
                                 style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                <a href="{{ route('search.show',$item['id']) }}"
                                   title="{{ $item['name'] }}">
                                    <i class="fa {{\App\Utils\Tool::getExtIcon($item['ext'] ?? '')}}"></i> {{ $item['name'] }}
                                </a>
                            </div>
                            <div class="col-sm-2 d-none d-md-block d-md-none">
                                <span
                                    class="pull-right">{{ date('M d H:i',strtotime($item['lastModifiedDateTime'])) }}</span>
                            </div>
                            <div class="col-sm-2 d-none d-md-block d-md-none">
                                <span class="pull-right">{{ \App\Utils\Tool::convertSize($item['size']) }}</span>
                            </div>
                            <div class="col-4 col-sm-2">
                                <span class="pull-right">
                                    <a href="{{ route('search.show',$item['id']) }}"><i
                                            class="fa fa-info-circle"
                                            title="详情"></i></a>&nbsp;&nbsp;
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </div>
        </div>
        <div class="text-center">
            {{ $items->appends(['keywords' => request()->get('keywords'),'limit' => request()->get('limit')])->links('default.page') }}
        </div>
    @endif
@stop
