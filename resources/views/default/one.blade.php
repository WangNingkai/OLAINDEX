@extends('default.layouts.main')
@section('title','OLAINDEX')
@section('content')
    <div class="btn-group mb-3" role="group" aria-label="choiceAccount">
        <button type="button" class="btn btn-primary btn-sm">选择盘</button>
        <div class="btn-group" role="group">
            <button id="btnChoiceAccount" type="button" class="btn btn-primary btn-sm dropdown-toggle"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnChoiceAccount">
                @foreach($accounts as $key => $account)
                    <a class="dropdown-item"
                       href="{{ route('drive',['hash' => $account['hash_id']]) }}">{{ $key + 1 .':'.$account['remark'] }}</a>
                @endforeach
            </div>
        </div>
    </div>
    @include('default.components.breadcrumb',['hash' => $hash, 'path' => $path])
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
                    <tr onclick="window.location.href='{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::fetchGoBack($path))]) }}'">
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
                        <tr onclick="window.location.href='{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) ))]) }}'">
                            <td>
                                <i class="ri-{{ \App\Helpers\Tool::fetchExtIco($data['ext'] ?? 'file') }}-fill"></i> {{ str_limit($data['name'],32) }}
                            </td>

                            <td class="d-none d-md-block d-md-none">{{ convert_size($data['size']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}</td>
                            <td>
                                @if(array_has($data,'folder'))
                                    -
                                @else
                                    <a href="{{ route('drive.query', ['hash' => $hash, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) )),'download' => 1]) }}" style="text-decoration: none">下载</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td colspan="4">
                        {{ array_get($item,'folder.childCount',0) }}
                        个项目
                    </td>
                </tr>
                </tbody>
            </table>
            {{ $list->links() }}
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

