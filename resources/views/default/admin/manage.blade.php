@extends('default.layouts.main')
@section('title', '文件管理')
@section('content')
    <nav aria-label="breadcrumb" class="mb-3 d-none d-md-block d-md-none">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.query', ['account_id' => $account_id]) }}"><i
                        class="ri-home-fill"></i> Home</a></li>
            @if(!blank($path))
                @if (count($path) < 6)
                    @foreach ($path as $key => $value)
                        @if(end($path) === $value && $key === (count($path) - 1))
                            <li class="breadcrumb-item active">{{ str_limit($value, 20)  }}</li>
                        @else
                            @if (!blank($value))
                                <li class="breadcrumb-item ">
                                    <a href="{{ route('manage.query', ['account_id' => $account_id, 'query' => url_encode(\App\Helpers\Tool::combineBreadcrumb($key + 1, $path))]) }}">
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
                                    <a href="{{ route('manage.query', ['account_id' => $account_id, 'query' => url_encode(\App\Helpers\Tool::combineBreadcrumb($key + 1, $path))]) }}">
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
                <tr class="row mx-0">
                    <td colspan="4">
                        <form class="form-inline">
                            <label class="mb-0 mr-2 my-1">
                                <input class="form-control form-control-sm mr-sm-2" type="text" name="keywords"
                                       placeholder="搜索" value="{{ $keywords }}">
                            </label>
                            <button class="btn btn-primary btn-sm mr-2 my-1" type="submit">搜索</button>
                        </form>
                    </td>
                </tr>
                <tr class="row mx-0">
                    <td colspan="4">
                        <a href="{{ route('admin.account.list') }}" class="btn btn-sm btn-primary mr-2 my-1">
                            <i class="ri-arrow-go-back-fill"></i>
                            返回账号列表
                        </a>
                        @if(!blank($path))
                            <a class="btn btn-sm btn-primary mr-2 my-1"
                               href="{{ route('manage.query', ['account_id' => $account_id, 'query' => url_encode(\App\Helpers\Tool::fetchGoBack($path))]) }}">
                                <i class="ri-arrow-go-back-fill"></i> 返回上级
                            </a>
                        @endif
                        @if(!blank($list))
                            <a class="btn btn-sm btn-primary mr-2 my-1 refresh" href="javascript:void(0)"><i
                                    class="ri-refresh-line"></i> 刷新列表</a>
                        @endif
                        <a class="btn btn-sm btn-primary mr-2 my-1" href="javascript:void(0)" data-toggle="modal"
                           data-target="#uploadModal"><i class="ri-upload-line"></i> 上传文件</a>
                        <a class="btn btn-sm btn-primary mr-2 my-1" href="javascript:void(0)" data-toggle="modal"
                           data-target="#mkdirModal"><i class="ri-folder-add-line"></i> 创建文件夹</a>
                        @if(blank($readme))
                            <a class="btn btn-sm btn-primary mr-2 my-1"
                               href="{{ route('manage.readme',['account_id' => $account_id, 'parent_id' => $item['id'], 'redirect' => url()->current()]) }}"><i
                                    class="ri-pencil-line"></i> 新建readme.md</a>
                        @else
                            <a class="btn btn-sm btn-primary mr-2 my-1"
                               href="{{ route('manage.readme',['account_id' => $account_id, 'file_id' => $readme['id'], 'redirect' => url()->current()]) }}"><i
                                    class="ri-pencil-line"></i> 编辑readme.md</a>
                        @endif
                    </td>
                </tr>
                @if(blank($list))
                    <tr class="row mx-0 text-center">
                        <td colspan="4">
                            Ops! 暂无资源
                        </td>
                    </tr>
                @else
                    @foreach($list as $data)
                        <tr class="list-item row mx-0 align-items-center"
                            data-id="{{ $data['id'] }}"
                            data-name="{{ $data['name'] }}"
                            data-file="{{ !array_has($data,'folder') }}"
                            data-size="{{ $data['size'] }}"
                            data-route="{{ route('manage.query', ['account_id' => $account_id, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name'])))]) }}">
                            <td class="col-5"
                                style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                <a title="{{ $data['name'] }}"
                                   href="{{ route('manage.query', ['account_id' => $account_id, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name'])))]) }}"
                                   class="text-decoration-none stretched-link">
                                    <i class="ri-{{ \App\Helpers\Tool::fetchExtIco($data['ext'] ?? 'file') }}-fill"></i>
                                    {{ $data['name'] }}
                                </a>
                            </td>

                            <td class="col-2">{{ convert_size($data['size']) }}</td>
                            <td class="col-3">{{ date('y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}</td>
                            <td class="col-2">
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger delete mr-2 my-1"
                                   id="delete_{{ $data['id'] }}">删除</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {{ $list->appends(['sortBy'=> request()->get('sortBy'), 'keywords' => request()->get('keywords')])->links('default.components.page') }}
        </div>
    </div>
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-upload-line"></i> 上传文件</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="uploader m-3"><input type="file" class="filepond"
                                                     name="filepond"></div>
                    <p class="text-danger text-center">上传过程中,请勿刷新,否则会导致上传失败!!!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary refresh">刷新</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mkdirModal" tabindex="-1" role="dialog" aria-labelledby="mkdirModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-folder-add-line"></i> 创建文件夹</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="mkdirForm">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label class="form-control-label" for="filename">文件夹名</label>
                            <input type="text" class="form-control" id="filename" name="filename">
                            <input type="hidden" name="parent_id" id="parent_id" value="{{ $item['id'] }}">
                            <input type="hidden" name="query" id="query" value="{{ $query }}">
                            <input type="hidden" name="account_id" id="account_id"
                                   value="{{ $account_id }}">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">创建</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@push('stylesheet')
    <link rel="stylesheet" href="https://cdn.staticfile.org/filepond/4.23.1/filepond.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.staticfile.org/filepond/4.23.1/filepond.min.js"></script>
    <script>
        const inputElement = document.querySelector('input[type="file"]')
        const pond = FilePond.create(inputElement, {
            allowMultiple: true,
            maxFiles: 10,
            maxParallelUploads: 1,
            labelIdle: '拖放文件，或者 <span class="filepond--label-action"> 浏览 </span>',
            labelInvalidField: '字段包含无效文件',
            labelFileWaitingForSize: '计算文件大小',
            labelFileSizeNotAvailable: '文件大小不可用',
            labelFileLoading: '加载',
            labelFileLoadError: '加载错误',
            labelFileProcessing: '上传',
            labelFileProcessingComplete: '已上传',
            labelFileProcessingAborted: '上传已取消',
            labelFileProcessingError: '上传出错',
            labelFileProcessingRevertError: '还原出错',
            labelFileRemoveError: '删除出错',
            labelTapToCancel: '点击取消',
            labelTapToRetry: '点击重试',
            labelTapToUndo: '点击撤消',
            labelButtonRemoveItem: '删除',
            labelButtonAbortItemLoad: '中止',
            labelButtonRetryItemLoad: '重试',
            labelButtonAbortItemProcessing: '取消',
            labelButtonUndoItemProcessing: '撤消',
            labelButtonRetryItemProcessing: '重试',
            labelButtonProcessItem: '上传',
            labelMaxFileSizeExceeded: '文件太大',
            labelMaxFileSize: '最大值: {filesize}',
            labelMaxTotalFileSizeExceeded: '超过最大文件大小',
            labelMaxTotalFileSize: '最大文件大小：{filesize}',
            labelFileTypeNotAllowed: '文件类型无效',
            fileValidateTypeLabelExpectedTypes: '应为 {allButLastType} 或 {lastType}',
            imageValidateSizeLabelFormatError: '不支持图像类型',
            imageValidateSizeLabelImageSizeTooSmall: '图像太小',
            imageValidateSizeLabelImageSizeTooBig: '图像太大',
            imageValidateSizeLabelExpectedMinSize: '最小值: {minWidth} × {minHeight}',
            imageValidateSizeLabelExpectedMaxSize: '最大值: {maxWidth} × {maxHeight}',
            imageValidateSizeLabelImageResolutionTooLow: '分辨率太低',
            imageValidateSizeLabelImageResolutionTooHigh: '分辨率太高',
            imageValidateSizeLabelExpectedMinResolution: '最小分辨率：{minResolution}',
            imageValidateSizeLabelExpectedMaxResolution: '最大分辨率：{maxResolution}',
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort) => {
                    console.log('start upload file.', file)
                    // 请求创建上传地址
                    axios.post('/admin/manage/uploadSession', {
                        filename: file.name,
                        size: file.size,
                        path: "{{ $query }}",
                        account_id: "{{ $account_id }}",
                    })
                        .then(function(res) {
                            const data = res.data.data
                            const code = res.data.code
                            const err = res.data.error
                            if (code !== 0) {
                                console.error(err)
                                error(err)
                                return
                            }
                            const r = data.uploadUrl
                            const l = 33554432 // 分片大小
                            let i = 1,//分片段
                                s = 0,//分片开始长度
                                c = 0,//分片结束长度
                                u = file.size,//文件大小
                                d = Math.ceil(u / l)// 分片数


                            const f = () => {
                                c = s + l >= u ? u : s + l
                                let e = file.slice(s, c)
                                let url = `${r}&chunk=${i}&chunks=${d}`
                                axios.put(url, e, {
                                    headers: {
                                        'Content-Type': 'application/octet-stream',
                                        'Content-Range': `bytes ${s}-${c - 1}/${file.size}`,
                                    },
                                    onUploadProgress: (e) => {
                                        e.lengthComputable && progress(e.lengthComputable, (e.loaded + s) * 100, u * 100)
                                    },
                                }).then((e) => {
                                    console.log(e)
                                    202 === e.status
                                        ? (s += l, i++, f())
                                        : 201 === e.status && (console.log('file upload success.'), load(e.data))
                                })
                            }
                            f()
                        })
                        .catch(function(e) {
                            error('上传出错啦！')
                            console.log(e)
                        })
                    return {
                        abort: () => {
                            abort()
                        },
                    }
                },
                fetch: null,
                revert: null,
            },
        })
        pond.on('processfile', (err, file) => {
            pond.removeFile(file)
        })
        pond.on('processfiles', () => {
            Swal.fire({
                title: '上传成功！',
                text: '文件已全部上传',
                icon: 'success',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: '刷新',
            }).then((result) => {
                if (result.value) {
                    axios.post('/admin/manage/refresh/', {
                        query: "{{ $query }}",
                        account_id: "{{ $account_id }}",
                    })
                        .then(function(response) {
                            let data = response.data
                            if (data.error === '') {
                                window.location.reload()
                            }
                        })
                        .catch(function(error) {
                            console.log(error)
                        })
                }
            })
        })
        pond.on('warning', (err) => {
            Swal.fire({
                title: '出错了',
                text: '一次最多可添加10个文件，请重试！ ',
                icon: 'warning',
            })
            console.log(err)

        })
        pond.on('error', (err) => {
            Swal.fire({
                title: '出错了',
                text: err.body,
                icon: 'warning',
            })
            console.log(err)
        })
    </script>
    <script>
        $(function() {
            $('.list-item').on('click', function(e) {
                if ($(this).attr('data-route')) {
                    window.location.href = $(this).attr('data-route')
                }
                e.stopPropagation()
            })
            $('form#mkdirForm').on('submit', function(e) {
                e.preventDefault()
                const data = $(this).serialize()
                axios.post('/admin/manage/mkdir', data)
                    .then(function(response) {
                        const data = response.data
                        if (data.error === '') {
                            console.log(data)
                            Swal.fire({
                                title: '操作成功',
                                text: '文件夹创建成功',
                                icon: 'success',
                            }).then(() => {
                                $('#mkdirModal').modal('hide')
                                setTimeout(() => {
                                    window.location.reload()
                                }, 500)
                            })
                        }
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            })

            $('.refresh').on('click', function(e) {
                axios.post('/admin/manage/refresh/', {
                    query: "{{ $query }}",
                    account_id: "{{ $account_id }}",
                })
                    .then(function(response) {
                        let data = response.data
                        if (data.error === '') {
                            window.location.reload()
                        }
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            })

            $('.delete').on('click', function(e) {
                let id = $(this).parent().parent().attr('data-id')
                Swal.fire({
                    title: '确定删除吗?',
                    text: '删除后无法恢复!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                }).then((result) => {
                    if (result.value) {
                        axios.post('/admin/manage/delete', {
                            file_id: id,
                            query: "{{ $query }}",
                            account_id: "{{ $account_id }}",
                        })
                            .then(function(response) {
                                let data = response.data
                                if (data.error === '') {
                                    Swal.fire({
                                        title: '操作成功',
                                        text: '删除成功',
                                        icon: 'success',
                                    }).then(() => {
                                        window.location.reload()
                                    })
                                } else {
                                    Swal.showValidationMessage(
                                        `请求出错: ${error}`,
                                    )
                                }
                            })
                            .catch(function(error) {
                                console.log(error)
                            })
                    }
                })
                e.stopPropagation()
            })
        })
    </script>
@endpush
