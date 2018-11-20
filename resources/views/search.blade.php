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
                        文件
                    </div>
                    <div class="col d-none d-md-block d-md-none">
                        <span class="pull-right">最后修改时间</span>
                    </div>
                    <div class="col d-none d-md-block d-md-none">
                        <span class="pull-right">大小</span>
                    </div>
                    <div class="col">
                        <span class="pull-right">操作</span>
                    </div>
                </div>
            </div>
            <div class="list-group item-list">
                @foreach($items as $item)
                    <li class="list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col" style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                <a href="{{ route('search.show',$item['id']) }}"
                                   title="{{ $item['name'] }}">
                                    <i class="fa {{\App\Helpers\Tool::getExtIcon($item['ext'])}}"></i> {{ $item['name'] }}
                                </a>
                            </div>
                            <div class="col d-none d-md-block d-md-none">
                                <span
                                    class="pull-right">{{ date('Y-m-d H:i:s',strtotime($item['lastModifiedDateTime'])) }}</span>
                            </div>
                            <div class="col d-none d-md-block d-md-none">
                                <span class="pull-right">{{ \App\Helpers\Tool::convertSize($item['size']) }}</span>
                            </div>
                            <div class="col">
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
            {{ $items->appends(['keywords' => request()->get('keywords')])->links('page') }}
        </div>
    @endif
@stop
