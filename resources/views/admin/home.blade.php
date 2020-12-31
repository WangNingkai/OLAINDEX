@extends('admin.layouts.main')
@section('title', '控制台')
@section('content')
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    概览
                </div>
                <h2 class="page-title">
                    控制台
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
        <div class="col-sm-3">
            <a href="{{ route('admin.account.list') }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="text-end text-green">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                               viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                               stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none"/><circle cx="9" cy="7"
                                                                                                         r="4"/><path
                                  d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path
                                  d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                        </span>
                        </div>
                        <div class="h1 m-0">{{ $accounts_count }}</div>
                        <div class="text-muted mb-3">绑定账号</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="{{ route('admin.url.list') }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="text-end text-green">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                               viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                               stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none"/><path
                                  d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5"/><path
                                  d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5"/></svg>
                        </span>
                        </div>
                        <div class="h1 m-0">{{ $links_count }}</div>
                        <div class="text-muted mb-3">转换短链</div>
                    </div>
                </div>
            </a>

        </div>
    </div>
@stop
