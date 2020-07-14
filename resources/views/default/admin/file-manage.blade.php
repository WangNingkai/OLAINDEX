@extends('default.layouts.main')
@section('title', '文件管理')
@section('content')
    <div class="card mb-3">
        <div class="card-header">文件管理</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col">
                    <a href="{{ route('admin.account.list') }}"
                       class="btn btn-sm btn-primary"> <i
                            class="ri-arrow-go-back-fill"></i> 返回</a>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col table-responsive">
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
                            <tr onclick="window.location.href='{{ route('admin.file.manage', ['hash' => $hash, 'query' => url_encode(\App\Helpers\Tool::fetchGoBack($path))]) }}'">
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
                                <tr onclick="window.location.href='{{ route('admin.file.manage', ['hash' => $hash, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name']) ))]) }}'">
                                    <td style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                        <i class="ri-{{ \App\Helpers\Tool::fetchExtIco($data['ext'] ?? 'file') }}-fill"></i> {{ str_limit($data['name'], 32) }}
                                    </td>

                                    <td class="d-none d-md-block d-md-none">{{ convert_size($data['size']) }}</td>
                                    <td>{{ date('Y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}</td>
                                    <td>
                                        <a href="#" style="text-decoration: none">删除</a>
                                        @if(!array_has($data,'folder'))
                                            {{--todo:加密隐藏--}}
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
                    {{ $list->links('default.components.page') }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="btnOperate"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            操作此文件夹：
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnOperate">
                            @if (blank($doc['readme']))
                                <a class="dropdown-item"
                                   href="{{ route('admin.file.create',['hash' => $hash, 'query' => $item['id'], 'fileName' => 'README.md']) }}">添加README</a>
                            @else
                                <a class="dropdown-item"
                                   href="{{ route('admin.file.edit', ['hash' => $hash, 'query' => $doc['readme']['id']]) }}">编辑README</a>
                            @endif
                            @if (blank($doc['head']))
                                <a class="dropdown-item"
                                   href="{{ route('admin.file.create',['hash' => $hash, 'query' => $item['id'], 'fileName' => 'HEAD.md']) }}">添加HEAD</a>
                            @else
                                <a class="dropdown-item"
                                   href="{{ route('admin.file.edit', ['hash' => $hash, 'query' => $doc['head']['id']]) }}">编辑HEAD</a>
                            @endif
                                {{--todo:加密隐藏--}}
                            <a class="dropdown-item" href="#">删除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
