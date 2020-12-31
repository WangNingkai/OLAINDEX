@extends('admin.layouts.main')
@section('title', '文件管理')
@section('content')
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    {{ $account->remark }}
                </div>
                <h2 class="page-title">
                    文件管理
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  <span class="d-none d-sm-inline">
                    <a href="{{ route('admin.account.list') }}" class="btn btn-primary">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="9" y1="6" x2="20" y2="6"/>
                                        <line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/>
                                        <line x1="5" y1="6" x2="5" y2="6.01"/><line x1="5" y1="12" x2="5" y2="12.01"/>
                                        <line x1="5" y1="18" x2="5" y2="18.01"/>
                                    </svg>
                                </span>
                      账号列表
                    </a>
                  </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb breadcrumb-arrows breadcrumb-alternate">
                    <li class="breadcrumb-item">
                        <a href="{{ route('manage.query', ['account_id' => $account_id]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <polyline points="5 12 3 12 12 3 21 12 19 12"/>
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>
                            </svg>
                            Home
                        </a>
                    </li>
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
            <div class="card">
                <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                        <div class="ms-auto text-muted">
                            <div class="ms-2 d-inline-block">
                                <form class="form-inline">
                                    <label>
                                        <input type="text" name="keywords"
                                               placeholder="搜索" class="form-control form-control">
                                    </label>
                                    <button class="btn btn-primary mr-2 my-1" type="submit">搜索</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable table-hover table-borderless">
                        <thead>
                        <tr>
                            <th>
                                文件
                            </th>
                            <th>大小</th>
                            <th>时间</th>
                            <th class="text-end">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4">
                                @if(!blank($path))
                                    <a class="btn btn-primary mr-2 my-1"
                                       href="{{ route('manage.query', ['account_id' => $account_id, 'query' => url_encode(\App\Helpers\Tool::fetchGoBack($path))]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1"/>
                                        </svg>
                                        返回上级
                                    </a>
                                @endif
                                @if(!blank($list))
                                    <a class="btn btn-primary mr-2 my-1 refresh" href="javascript:void(0)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                                        </svg>
                                        刷新列表
                                    </a>
                                @endif
                                <a class="btn btn-primary mr-2 my-1" href="javascript:void(0)" data-bs-toggle="modal"
                                   data-bs-target="#uploadModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"/>
                                        <polyline points="9 15 12 12 15 15"/>
                                        <line x1="12" y1="12" x2="12" y2="21"/>
                                    </svg>
                                    上传文件
                                </a>
                                <a class="btn btn-primary mr-2 my-1" href="javascript:void(0)" data-bs-toggle="modal"
                                   data-bs-target="#mkdirModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path
                                            d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/>
                                        <line x1="12" y1="10" x2="12" y2="16"/>
                                        <line x1="9" y1="13" x2="15" y2="13"/>
                                    </svg>
                                    创建文件夹
                                </a>
                                @if(blank($readme))
                                    <a class="btn btn-primary mr-2 my-1"
                                       href="{{ route('manage.readme',['account_id' => $account_id, 'parent_id' => $item['id'], 'redirect' => url()->current()]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                            <line x1="16" y1="5" x2="19" y2="8"/>
                                        </svg>
                                        新建readme.md
                                    </a>
                                @else
                                    <a class="btn btn-primary mr-2 my-1"
                                       href="{{ route('manage.readme',['account_id' => $account_id, 'file_id' => $readme['id'], 'redirect' => url()->current()]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                            <line x1="16" y1="5" x2="19" y2="8"/>
                                        </svg>
                                        编辑readme.md
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @if(blank($list))
                            <tr>
                                <td colspan="4" class="text-center">
                                    Ops! 暂无资源
                                </td>
                            </tr>
                        @else
                            @foreach($list as $data)
                                <tr class="list-item align-items-center"
                                    data-id="{{ $data['id'] }}"
                                    data-name="{{ $data['name'] }}"
                                    data-file="{{ !array_has($data,'folder') }}"
                                    data-size="{{ $data['size'] }}"
                                    data-route="{{ route('manage.query', ['account_id' => $account_id, 'query' => url_encode(implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $data['name'])))]) }}">
                                    <td style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                                        @if(!array_has($data,'folder'))
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                 height="24" viewBox="0 0 24 24" stroke-width="2"
                                                 stroke="currentColor" fill="none" stroke-linecap="round"
                                                 stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                <path
                                                    d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                 height="24" viewBox="0 0 24 24" stroke-width="2"
                                                 stroke="currentColor" fill="none" stroke-linecap="round"
                                                 stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path
                                                    d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/>
                                            </svg>
                                        @endif

                                        {{ $data['name'] }}
                                    </td>
                                    <td>{{ convert_size($data['size']) }}</td>
                                    <td>{{ date('y-m-d H:i:s', strtotime($data['lastModifiedDateTime'])) }}</td>
                                    <td class="text-end">
                                        <a href="javascript:void(0);" class="btn btn-ghost-danger delete">
                                            删除
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">
                        {{ array_get($item,'folder.childCount',0) }}
                        个项目
                        {{ convert_size(array_get($item,'size',0)) }}
                    </p>
                    {{ $list->appends(['sortBy'=> request()->get('sortBy'), 'keywords' => request()->get('keywords')])->links('admin.components.page') }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"/>
                            <polyline points="9 15 12 12 15 15"/>
                            <line x1="12" y1="12" x2="12" y2="21"/>
                        </svg>
                        上传文件
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="uploader m-3"><input type="file" class="filepond"
                                                     name="filepond"></div>
                    <p class="text-danger text-center">上传过程中,请勿刷新,否则会导致上传失败!!!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary refresh">刷新</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="mkdirModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path
                                d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/>
                            <line x1="12" y1="10" x2="12" y2="16"/>
                            <line x1="9" y1="13" x2="15" y2="13"/>
                        </svg>
                        创建文件夹
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="mkdirForm">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label" for="filename">文件夹名</label>
                            <input type="text" class="form-control" id="filename" name="filename">
                            <input type="hidden" name="parent_id" id="parent_id" value="{{ $item['id'] }}">
                            <input type="hidden" name="query" id="query" value="{{ $query }}">
                            <input type="hidden" name="account_id" id="account_id"
                                   value="{{ $account_id }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">关闭</button>
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
