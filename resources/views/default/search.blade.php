@extends('default.layouts.main')
@section('title','OLAINDEX')
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">搜索</div>
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
                @if(blank($list))
                    <tr>
                        <td colspan="4" class="text-center">
                            Ops! 暂无资源
                        </td>
                    </tr>
                @else
                    @foreach($list as $data)
                        <tr onclick="window.location.href='{{ route('drive.query.id', ['hash' => $hash, 'query' => $data['id']]) }}'">
                            <td style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                <i class="ri-{{ \App\Helpers\Tool::fetchExtIco($data['ext'] ?? 'file') }}-fill"></i> {{ str_limit($data['name'], 32) }}
                            </td>

                            <td class="d-none d-md-block d-md-none">{{ convert_size($data['size']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}</td>
                            <td>
                                <a href="{{ route('drive.query.id', ['hash' => $hash, 'query' => $data['id'],'download' => 1]) }}"
                                   style="text-decoration: none">下载</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {{ $list->links('default.components.page') }}
        </div>
    </div>
@stop

