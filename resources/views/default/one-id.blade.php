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
        @auth
            @if (blank($doc['readme']) || blank($doc['head']))
                <div class="col">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="btnChoiceAccount"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            添加说明：
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnChoiceAccount">
                            @if (blank($doc['readme']))
                                <a class="dropdown-item"
                                   href="{{ route('drive.create',['hash' => $hash, 'query' => $item['id'], 'fileName' => 'README.md']) }}">添加README</a>
                            @endif
                            @if (blank($doc['head']))
                                <a class="dropdown-item"
                                   href="{{ route('drive.create',['hash' => $hash, 'query' => $item['id'], 'fileName' => 'HEAD.md']) }}">添加HEAD</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    @if(setting('open_search',0))
        <div class="row mb-3">
            <div class="col">
                <form class="form-inline" action="{{ route('drive.search',['hash' => $hash]) }}" method="get">
                    <label>
                        <input class="form-control  form-control-sm" type="text" name="keyword" placeholder="Search">
                    </label>
                    <button class="btn btn-sm btn-primary" type="submit">搜索</button>
                </form>
            </div>
        </div>
    @endif

    @if (!blank($doc['head']))
        <div class="card border-light mb-3">
            <div class="card-header"><i class="ri-send-plane-fill"></i> HEAD</div>
            <div class="card-body markdown-body" id="head">
                {!! marked($doc['head']) !!}
            </div>
        </div>
    @endif
    <div class="card border-light mb-3">
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover table-borderless">
                <thead>
                <tr>
                    <th scope="col">File</th>
                    <th scope="col" class="d-none d-md-block d-md-none">Size</th>
                    <th scope="col">Date</th>
                    <th scope="col">More</th>
                </tr>
                </thead>
                <tbody>
                @if(!blank($path))
                    <tr onclick="window.location.href='{{ route('drive.query.id', ['hash' => $hash, 'query' => $item['parentReference']['id']]) }}'">
                        <td colspan="4">
                            <i class="ri-arrow-go-back-fill"></i> 返回上一层
                        </td>
                    </tr>
                @endif
                @if(blank($list))
                    <tr>
                        <td colspan="4" class="text-center">
                            Ops! 暂无资源
                        </td>
                    </tr>
                @else
                    @foreach($list as $data)
                        <tr class="list-item"
                            data-route="{{ route('drive.query.id', ['hash' => $hash, 'query' => $data['id']]) }}">
                            <td style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                <i class="ri-{{ \App\Helpers\Tool::fetchExtIco($data['ext'] ?? 'file') }}-fill"></i> {{ str_limit($data['name'], 32) }}
                            </td>

                            <td class="d-none d-md-block d-md-none">{{ convert_size($data['size']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}</td>
                            <td>
                                @if(array_has($data,'folder'))
                                    -
                                @else
                                    <a href="{{ route('drive.query.id', ['hash' => $hash, 'query' => $data['id'],'download' => 1]) }}"
                                       style="text-decoration: none">下载</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td colspan="4">
                        {{ array_get($item,'folder.childCount',0) }}
                        个项目
                        {{ convert_size(array_get($item,'size',0)) }}
                    </td>
                </tr>
                </tbody>
            </table>
            {{ $list->appends(['sortBy'=> request()->get('sortBy')])->links('default.components.page') }}
        </div>
    </div>
    @if (!blank($doc['readme']))
        <div class="card border-light mb-3">
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
                window.location.href = $(this).attr('data-route')
                e.stopPropagation()
            })
        })
    </script>
@endpush

