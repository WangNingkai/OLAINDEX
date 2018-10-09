@extends('layouts.main')
@section('title','搜索'.request()->get('q'))
@section('content')
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
            </div>
        </div>
        <div class="list-group">
            @foreach($items as $item)
                <li class="list-group-item list-group-item-action" style="border:0;">
                    <div class="row">
                        <div class="col">
                            @if(isset($item['folder']))
                                <a href="#" title="{{ $item['name'] }}">
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
                    </div>
                </li>
            @endforeach
        </div>
    </div>
@stop
