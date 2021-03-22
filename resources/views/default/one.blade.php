@extends('default.layouts.main')
@section('title', setting('site_name','OLAINDEX'))
@section('content')
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
        <div class="card-header d-flex align-items-center">
            @if(count($accounts) > 1)
                <div class="dropdown mb-0 mr-2 my-1">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="btnChoiceAccount"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        选择盘符：
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnChoiceAccount">
                        @foreach($accounts as $key => $account)
                            <a class="dropdown-item"
                               href="{{ route('drive.query',['hash' => $account['hash_id']]) }}">{{ $key + 1 .':'.$account['remark'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#links-container">
                导出直链
            </button>

            @if(setting('open_search', 0))
                <form class="form-inline mb-0 mr-2 my-1">
                    <label class="mb-0 mr-2 my-1">
                        <input class="form-control form-control-sm" type="text" name="keywords"
                               placeholder="搜索" value="{{ $keywords }}">
                    </label>
                    <button class="btn btn-primary btn-sm mr-2 my-1" type="submit">搜索</button>
                </form>
            @endif
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover  table-borderless">
                <caption>
                    {{ array_get($item,'folder.childCount',0) }}
                    个项目
                    {{ convert_size(array_get($item,'size',0)) }}
                </caption>
                <thead class="w-100">
                <tr class="row mx-0">
                    <th class="col-5">
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
                    <th class="col-2">操作</th>
                </tr>
                </thead>
                <tbody class="w-100">
                @if(!blank($path))
                    <tr class="row mx-0">
                        <td colspan="4">
                            <a class="text-decoration-none"
                               href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::fetchGoBack($path))]) }}">
                                <i class="ri-arrow-go-back-fill"></i> 返回上级
                            </a>
                        </td>
                    </tr>
                @endif
                @if(blank($list))
                    <tr class="row mx-0 text-center">
                        <td colspan="4">
                            Ops! 暂无资源
                        </td>
                    </tr>
                @else
                    @foreach($list as $data)
                        <tr class="list-item row mx-0 align-items-center"
                            data-route="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) ))]) }}">
                            <td class="col-5"
                                style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                <a title="{{ $data['name'] }}"
                                   href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name'])))]) }}"
                                   class="text-decoration-none stretched-link">
                                    <i class="ri-{{ \App\Helpers\Tool::fetchExtIco($data['ext'] ?? 'file') }}-fill"></i>
                                    {{ $data['name'] }}
                                </a>
                            </td>

                            <td class="col-2">{{ convert_size($data['size']) }}</td>
                            <td class="col-3">{{ date('Y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}</td>
                            <td class="col-2">
                                @if(array_has($data,'folder'))
                                    -
                                @else
                                    <a title="{{ $data['name'] }}"
                                       href="{{ shorten_url(route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) )),'download' => 1])) }}"
                                       class="btn btn-sm btn-primary download mr-2 my-1">下载</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {{ $list->appends(['sortBy'=> request()->get('sortBy'),'keywords' => request()->get('keywords'),'hash' => request()->get('hash')])->links('default.components.page') }}
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
    <div class="modal fade" id="links-container" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">导出直链</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>导出当前页面文件下载地址</p>
                    <p>
                        <a class="clipboard btn btn-primary btn-sm" href="javascript:void(0)"
                           data-toggle="tooltip"
                           data-placement="right" data-clipboard-target="#dl"
                        >复制全部</a>
                        <a class="btn btn-info btn-sm" href="javascript:void(0)" onclick="exportLinks()">下载</a>
                    </p>
                    <label for="dl">
                        <textarea name="urls" id="dl" class="form-control" cols="60"
                                  rows="15"></textarea>
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="fetchLinks()">点击获取</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@stop
@push('scripts')
    <script>
        const preLoad = () => {
            axios.post('/drive/preload/', {
                hash: "{{ $hash }}",
                query: "{{ implode('/', $path) }}",
            })
                .then(function(response) {
                    let data = response.data
                    if (data.error !== '') {
                        console.error(data.error)
                    }
                })
                .catch(function(error) {
                    console.error(error)
                })
        }

        function fetchLinks() {
            $('#dl').val('')
            $('.download').each(function() {
                let dn = $(this).attr('title')
                let dl = decodeURI($(this).attr('href'))
                let url = dn + ' ' + dl + '\n'
                let origin = $('#dl').val()
                $('#dl').val(origin + url)
            })
        }

        function exportLinks() {
            let data = $('#dl').val()
            exportRaw(data, 'urls.txt')
        }

        function exportRaw(data, name) {
            let urlObject = window.URL || window.webkitURL || window
            let export_blob = new Blob([data])
            let save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a')
            save_link.href = urlObject.createObjectURL(export_blob)
            save_link.download = name
            save_link.click()
        }

        $(function() {
            preLoad()
            $('.list-item').on('click', function(e) {
                if ($(this).attr('data-route')) {
                    window.location.href = $(this).attr('data-route')
                }
                e.stopPropagation()
            })
        })
    </script>
@endpush

