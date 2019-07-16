@extends('default.layouts.main')
@section('title',$file['name'])
@section('content')
    @include('default.breadcrumb')
    <div class="card border-light mb-3">
        <div class="card-header">
            {{ $file['name'] }}
        </div>
        <div class="card-body">
            <div class="text-center"><a href="{{ $file['download'] }}" data-fancybox="image-list"><img
                        src="{{ $file['thumb'] }}" alt="{{ $file['name'] }}" class="img-fluid"></a></div>
            <br>
            <div class="text-center">
                <a href="{{ route('download', [
                        'query'    => Tool::getEncodeUrl($origin_path)
                        'onedrive' => app('onedrive')->id
                    ]) }}" class="btn btn-success">
                    <i class="fa fa-download"></i> 下载
                </a>
                &nbsp;&nbsp;
                <a href="{{ route('view', [
                        'query'    => Tool::getEncodeUrl($origin_path)
                        'onedrive' => app('onedrive')->id
                    ]) }}" data-fancybox="image-list"
                   class="btn btn-info"><i class="fa fa-eye"></i>
                    点击查看原图</a>
            </div>
            <br>
            <label class="control-label">引用链接</label>
            <div class="form-group">
                <div class="input-group mb-3">
                    <input type="text" id="link1" class="form-control"
                           value="{{ route('view',[
                                'query'    => Tool::getEncodeUrl($origin_path)
                                'onedrive' => app('onedrive')->id
                            ]) }}">
                    <div class="input-group-append">
                        <a href="javascript:void(0)" style="text-decoration: none" data-toggle="tooltip"
                           data-placement="right" data-clipboard-target="#link1" class="clipboard"><span
                                class="input-group-text">复制</span></a>
                    </div>
                </div>
            </div>
            <label class="control-label">Markdown链接</label>
            <div class="form-group">
                <div class="input-group mb-3">
                    <input type="text" id="link2" class="form-control"
                           value="![]({{ route('view', [
                                'query'    => Tool::getEncodeUrl($origin_path)
                                'onedrive' => app('onedrive')->id
                            ]) }})">
                    <div class="input-group-append">
                        <a href="javascript:void(0)" style="text-decoration: none" data-toggle="tooltip"
                           data-placement="right" data-clipboard-target="#link2" class="clipboard"><span
                                class="input-group-text">复制</span></a>
                    </div>
                </div>
            </div>
            <label class="control-label">HTML链接</label>
            <div class="form-group">
                <div class="input-group mb-3">
                    <input type="text" id="link3" class="form-control"
                           value="<img src='{{ route('view', [
                                'query'    => Tool::getEncodeUrl($origin_path)
                                'onedrive' => app('onedrive')->id
                            ]) }}' />">
                    <div class="input-group-append">
                        <a href="javascript:void(0)" style="text-decoration: none" data-toggle="tooltip"
                           data-placement="right" data-clipboard-target="#link3" class="clipboard"><span
                                class="input-group-text">复制</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
