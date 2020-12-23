@extends('default.layouts.main')
@section('title', setting('site_name','OLAINDEX'))
@section('content')
    <div class="row mb-3">
        @if(count($accounts) > 1)
            <div class="col">
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="btnChoiceAccount"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        选择盘符：
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnChoiceAccount">
                        @foreach($accounts as $key => $account)
                            <a class="dropdown-item"
                               href="{{ route('drive',['hash' => $account['hash_id']]) }}">{{ $key + 1 .':'.$account['remark'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
    @includeWhen(!blank($path),'default.components.breadcrumb',['hash' => $hash, 'path' => $path])
    @if (!blank($doc['head']))
        <div class="card border-light mb-3 shadow">
            <div class="card-header"><i class="ri-send-plane-fill"></i> HEAD</div>
            <div class="card-body markdown-body" id="head">
                {!! marked($doc['head']) !!}
            </div>
        </div>
    @endif
    <div class="card border-light mb-3 shadow">
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover table-borderless">
                <caption>
                    {{ array_get($item,'folder.childCount',0) }}
                    个项目
                    {{ convert_size(array_get($item,'size',0)) }}
                </caption>
                <thead class="w-100">
                <tr class="row mx-0">
                    <th class="col-4">
                        文件
                        @if(\App\Helpers\Tool::getOrderByStatus('name'))
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','name,asc') }}"
                               class="text-decoration-none"><i class="ri-arrow-down-s-line"></i> </a>
                        @else
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','name,desc') }}"
                               class="text-decoration-none"><i class="ri-arrow-up-s-line"></i> </a>
                        @endif
                    </th>
                    <th class="col-2">
                        大小
                        @if(\App\Helpers\Tool::getOrderByStatus('size'))
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','size,asc') }}"
                               class="text-decoration-none"><i class="ri-arrow-down-s-line"></i> </a>
                        @else
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','size,desc') }}"
                               class="text-decoration-none"><i class="ri-arrow-up-s-line"></i> </a>
                        @endif
                    </th>
                    <th class="col-3">
                        时间
                        @if(\App\Helpers\Tool::getOrderByStatus('lastModifiedDateTime'))
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','lastModifiedDateTime,asc') }}"
                               class="text-decoration-none"><i class="ri-arrow-down-s-line"></i> </a>
                        @else
                            <a href="{{  \App\Helpers\Tool::buildQueryParams(url()->full(),'sortBy','lastModifiedDateTime,desc') }}"
                               class="text-decoration-none"><i class="ri-arrow-up-s-line"></i> </a>
                        @endif
                    </th>
                    <th class="col-3">操作</th>
                </tr>
                </thead>
                <tbody class="w-100">
                <tr>
                    <td colspan="4">
                        @if(!blank($path))
                            <a class="text-decoration-none"
                               href="{{ route('drive.query', ['hash' => $hash, 'query' => \App\Helpers\Tool::fetchGoBack($path)]) }}">
                                <i class="ri-arrow-go-back-fill"></i> 返回上级
                            </a>
                        @endif
                    </td>
                </tr>
                @if(blank($list))
                    <tr class="text-center">
                        <td colspan="4">
                            Ops! 暂无资源
                        </td>
                    </tr>
                @else
                    @foreach($list as $data)
                        <tr class="list-item row mx-0 align-items-center"
                            data-route="{{ route('drive.query', ['hash' => $hash, 'query' => implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) )]) }}">
                            <td class="col-4"
                                style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                <i class="ri-{{ \App\Helpers\Tool::fetchExtIco($data['ext'] ?? 'file') }}-fill"></i> {{ str_limit($data['name'], 32) }}
                            </td>

                            <td class="col-2">{{ convert_size($data['size']) }}</td>
                            <td class="col-3">{{ date('Y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}</td>
                            <td class="col-3">
                                @if(array_has($data,'folder'))
                                    -
                                @else
                                    <a href="{{ route('drive.query', ['hash' => $hash, 'query' => implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) ),'download' => 1]) }}"
                                       style="text-decoration: none">下载</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {{ $list->appends(['sortBy'=> request()->get('sortBy')])->links('default.components.page') }}
        </div>
    </div>
    @if (!blank($doc['readme']))
        <div class="card border-light mb-3 shadow">
            <div class="card-header"><i class="ri-bookmark-fill"></i> README</div>
            <div class="card-body markdown-body" id="readme">
                {!! marked($doc['readme']) !!}
            </div>
        </div>
    @endif
@stop
@push('scripts')
    <script>
        $(function() {
            $('.list-item').on('click', function(e) {
                if ($(this).attr('data-route')) {
                    window.location.href = $(this).attr('data-route')
                }
                e.stopPropagation()
            })
        })
    </script>
@endpush

