@extends('default.layouts.main')
@section('title','OLAINDEX')
@section('content')
    <div class="btn-group mb-3" role="group" aria-label="choiceAccount">
        <button type="button" class="btn btn-primary btn-sm">网盘列表</button>
        <div class="btn-group" role="group">
            <button id="btnChoiceAccount" type="button" class="btn btn-primary btn-sm dropdown-toggle"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnChoiceAccount">
                @foreach($accounts as $account)
                    <a class="dropdown-item"
                       href="{{ route('drive',['hash' => $account['hash_id']]) }}">{{ $account['remark'] }}</a>
                @endforeach
            </div>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="d-none d-md-block d-md-none">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('drive.query', ['hash' => $hash]) }}"><i
                        class="ri-home-fill"></i> Home</a></li>
            @if(!blank($path))
                @if (count($path) < 5)
                    @foreach ($path as $key => $value)
                        @if(end($path) === $value && $key === (count($path) - 1))
                            <li class="breadcrumb-item active">{{ str_limit($value,20)  }}</li>
                        @else
                            @if (!blank($value))
                                <li class="breadcrumb-item ">
                                    <a href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::combineBreadcrumb($key + 1, $path))]) }}">
                                        {{  str_limit($value,20) }}
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endforeach
                @else
                    <li class="breadcrumb-item active"> ...</li>
                    @foreach ($path as $key => $value)
                        @if(end($path) === $value && $key === (count($path) - 1))
                            <li class="breadcrumb-item active">{{  str_limit($value,20)  }}</li>
                        @else
                            @if (!blank($value) && $key === (count($path) - 2))
                                <li class="breadcrumb-item ">
                                    <a href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::combineBreadcrumb($key + 1, $path))]) }}">
                                        {{  str_limit($value,20) }}
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endforeach
                @endif
            @endif
        </ol>
    </nav>
    <div class="card border-light mb-3">
        <div class="card-header">{{ $file['name'] }}</div>
        <div class="card-body">
            @include('default.components.preview.' . $show,['file' => $file])
            <br>
            <label class="control-label">复制链接</label>
            <div class="form-group">
                <div class="input-group mb-3">
                    <label for="link1"></label>
                    <input type="text" id="link1" class="form-control"
                           value="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', $path))]) }}">
                    <div class="input-group-append">
                        <a href="javascript:void(0)" style="text-decoration: none" data-toggle="tooltip"
                           data-placement="right" data-clipboard-target="#link1" class="clipboard"><span
                                class="input-group-text">复制</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
