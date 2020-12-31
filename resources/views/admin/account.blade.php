@extends('admin.layouts.main')
@section('title', '账号管理')
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
                    账号管理
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  <span class="d-none d-sm-inline">
                    <a href="{{ route('admin.account.list') }}" class="btn btn-white">
                      管理账号
                    </a>
                  </span>
                    <a href="{{ route('install') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        绑定账号
                    </a>
                    <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                       data-bs-target="#modal-report" aria-label="Create new report">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">账号设置</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        @csrf

                        <div class="form-group mb-3 ">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="open_sp"
                                       @if( $config['open_sp'] ?? 0) checked
                                       @endif onchange="$('input[name=\'config[open_sp]\']').val(Number(this.checked))">
                                <span class="form-check-label">开启sharepoint挂载</span>
                                <input type="hidden" name="config[open_sp]"
                                       value="{{ $config['open_sp'] ?? 0 }}">
                            </label>
                            <span class="form-hint text-danger">开启sharepoint挂载将使用切换为sharepoint</span>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="sp">SharePoint地址</label>
                            <div>
                                <input type="text" class="form-control" id="sp" name="config[sp]"
                                       value="{{ $config['sp'] ?? ''}}">
                                <span class="form-hint text-danger">site_id:{{ $config['sp_id'] ?? '-'}}</span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="root">起始索引目录</label>
                            <div>
                                <input type="text" class="form-control" id="root" name="config[root]"
                                       value="{{ array_get($config, 'root', '') }}">
                                <span class="form-hint text-danger">索引开始目录，修改为sharepoint需要注意此项设置</span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="image_path">图床地址</label>
                            <div>
                                <input type="text" class="form-control" id="image_path" name="config[image_path]"
                                       value="{{ array_get($config, 'image_path', '/') }}">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="encrypt_path">加密路径</label>
                            <textarea class="form-control" id="encrypt_path" name="config[encrypt_path]"
                                      rows="3">{{ array_get($config, 'encrypt_path', '') }}</textarea>
                            <span
                                class="form-hint text-danger">加密的文件、文件夹需要密码访问，形式"目录:密码"，使用"|"隔开，分隔符为英文符号 (包含优先级)</span>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="hide_path">隐藏路径</label>
                            <textarea class="form-control" id="hide_path" name="config[hide_path]"
                                      rows="3">{{ array_get($config, 'hide_path', '') }}</textarea>
                            <span class="form-hint text-danger">标记的文件、文件夹前台不显示，使用"|"隔开，分隔符为英文符号</span>
                        </div>


                        <div class="form-group mb-3">
                            <label class="form-label" for="list_limit">列表默认显示条数</label>
                            <div>
                                <input type="text" class="form-control" id="list_limit" name="config[list_limit]"
                                       value="{{ array_get($config, 'list_limit', 10) }}">
                            </div>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">提交</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
