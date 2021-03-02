@php
    $themes = [
        'Cerulean' => 'cerulean',
        'Cosmo' => 'cosmo',
        'Cyborg' => 'cyborg',
        'Darkly' => 'darkly',
        'Flatly' => 'flatly',
        'Journal' => 'journal',
        'Litera' => 'litera',
        'Lumen' => 'lumen',
        'Materia' => 'materia',
        'Lux' => 'lux',
        'Minty' => 'minty',
        'Pulse' => 'pulse',
        'Sandstone' => 'sandstone',
        'Simplex' => 'simplex',
        'Sketchy' => 'sketchy',
        'Slate' => 'slate',
        'Solar' => 'solar',
        'Spacelab' => 'spacelab',
        'Superhero' => 'superhero',
        'United' => 'united',
        'Yeti' => 'yeti',
    ];
    $main_themes = [
        'default' => 'default',
        'mdui' => 'mdui',
    ];
@endphp
@extends('admin.layouts.main')
@section('title', '基础设置')
@section('content')
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    设置
                </div>
                <h2 class="page-title">
                    站点设置
                </h2>
            </div>
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#basic-config" class="nav-link active" data-bs-toggle="tab">基础设置</a>
                    </li>
                    <li class="nav-item">
                        <a href="#show-config" class="nav-link" data-bs-toggle="tab">显示设置</a>
                    </li>
                    <li class="nav-item">
                        <a href="#image-config" class="nav-link" data-bs-toggle="tab">图床设置</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="basic-config">
                            <form action="" method="post">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label" for="site_name">网站名称</label>
                                    <div>
                                        <input type="text" class="form-control" id="site_name" name="site_name"
                                               value="{{ setting('site_name','OLAINDEX') }}">
                                        <small class="form-hint text-danger">显示的网站名称.</small>
                                    </div>
                                </div>


                                <div class="form-group mb-3 ">
                                    <label class="form-label" for="main_theme">显示主题</label>
                                    <div>
                                        <select class="form-select" name="main_theme" id="main_theme">
                                            @foreach( $main_themes as $name => $theme)
                                                <option value="{{ $theme }}"
                                                        @if(setting('main_theme') === $theme) selected @endif>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3 ">
                                    <label class="form-label" for="site_theme">主题</label>
                                    <div>
                                        <select class="form-select" name="site_theme" id="site_theme">
                                            @foreach( $themes as $name => $theme)
                                                <option value="{{ $theme }}"
                                                        @if(setting('site_theme') === $theme) selected @endif>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-hint text-danger">仅在显示主题设置为 default 时生效.</small>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="cache_expires">网盘资源缓存时间(秒)</label>
                                    <div>
                                        <input type="text" class="form-control" id="cache_expires" name="cache_expires"
                                               value="{{ setting('cache_expires', 1800) }}">
                                        <small class="form-hint text-danger">建议缓存时间小于60分钟，否则会导致缓存失效.</small>
                                    </div>
                                </div>

                                <div class="form-group mb-3 ">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="open_search"
                                               @if( setting('open_search',0)) checked
                                               @endif onchange="$('input[name=\'open_search\']').val(Number(this.checked))">
                                        <span class="form-check-label">开启目录搜索</span>
                                        <input type="hidden" name="open_search"
                                               value="{{ setting('open_search', 0) }}">
                                    </label>
                                    <span class="form-hint text-danger">目录搜索仅为当前目录资源搜索，不适用全局搜索</span>
                                </div>


                                <div class="form-group mb-3 ">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="open_short_url"
                                               @if( setting('open_short_url',0)) checked
                                               @endif onchange="$('input[name=\'open_short_url\']').val(Number(this.checked))">
                                        <span class="form-check-label">开启预览短链</span>
                                        <input type="hidden" name="open_short_url"
                                               value="{{ setting('open_short_url', 0) }}">
                                    </label>
                                    <span class="form-hint text-danger">开启后预览文件只显示短链</span>
                                </div>

                                <div class="form-group mb-3 ">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="single_account_mode"
                                               @if( setting('single_account_mode',0)) checked
                                               @endif onchange="$('input[name=\'single_account_mode\']').val(Number(this.checked))">
                                        <span class="form-check-label">路径兼容模式</span>
                                        <input type="hidden" name="single_account_mode"
                                               value="{{ setting('single_account_mode', 0) }}">
                                    </label>
                                    <span class="form-hint text-danger">开启后按路径访问资源</span>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="encrypt_tip">加密资源提示文案</label>
                                    <textarea class="form-control" id="encrypt_tip" name="encrypt_tip"
                                              rows="1">{{ setting('encrypt_tip') }}</textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="copyright">页脚文案</label>
                                    <textarea class="form-control" id="copyright" name="copyright"
                                              rows="3">{{ setting('copyright') }}</textarea>
                                    <span class="form-hint text-danger">留空则不显示。使用markdown格式表示 如：Made by [xxx](https://xxx)</span>
                                </div>


                                <div class="form-group mb-3">
                                    <label class="form-label" for="stats_code">统计代码</label>
                                    <textarea class="form-control" id="encrypt_tip" name="stats_code"
                                              rows="3">{{ setting('stats_code') }}</textarea>
                                    <span class="form-hint text-danger">站点统计代码</span>
                                </div>


                                <div class="form-group mb-3">
                                    <label class="form-label" for="access_token">第三方接口token</label>
                                    <div>
                                        <input type="text" class="form-control" id="access_token" name="access_token"
                                               value="{{ setting('access_token', '') }}">
                                        <small class="form-hint text-danger">第三方接口token(图床、文件列表)</small>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="download_limit">资源下载频率限制（次/分钟）</label>
                                    <div>
                                        <input type="text" class="form-control" id="download_limit"
                                               name="download_limit"
                                               value="{{ setting('download_limit', 0) }}">
                                        <small class="form-hint text-danger">全局文件直链访问速率限制</small>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="user_limit">单一用户下载资源频率限制（次/分钟）</label>
                                    <div>
                                        <input type="text" class="form-control" id="user_limit"
                                               name="user_limit"
                                               value="{{ setting('user_limit', 0) }}">
                                        <small class="form-hint text-danger">单个用户文件直链访问速率限制（根据IP统计）</small>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="api_limit">接口访问频率限制（次/分钟）</label>
                                    <div>
                                        <input type="text" class="form-control" id="api_limit" name="api_limit"
                                               value="{{ setting('api_limit', 0) }}">
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary">提交</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="show-config">
                            <form action="" method="post">
                                @csrf
                                <p class="form-text text-danger">文件展示类型（扩展名）以空格分开</p>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="show_image">图片</label>
                                    <div>
                                        <input type="text" class="form-control" id="show_image" name="show_image"
                                               value="{{ setting('show_image', '') }}">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="show_video">视频</label>
                                    <div>
                                        <input type="text" class="form-control" id="show_video" name="show_video"
                                               value="{{ setting('show_video', '') }}">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="show_dash">Dash视频</label>
                                    <div>
                                        <input type="text" class="form-control" id="show_dash" name="show_dash"
                                               value="{{ setting('show_dash', '') }}">
                                        <span class="form-hint text-danger">不支持个人账号</span>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="show_audio">音频</label>
                                    <div>
                                        <input type="text" class="form-control" id="show_audio" name="show_audio"
                                               value="{{ setting('show_audio', '') }}">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="show_doc">Office文档</label>
                                    <div>
                                        <input type="text" class="form-control" id="show_doc" name="show_doc"
                                               value="{{ setting('show_doc', '') }}">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="show_code">代码</label>
                                    <div>
                                        <input type="text" class="form-control" id="show_code" name="show_code"
                                               value="{{ setting('show_code', '') }}">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="show_stream">文件流</label>
                                    <div>
                                        <input type="text" class="form-control" id="show_stream" name="show_stream"
                                               value="{{ setting('show_stream', '') }}">
                                    </div>
                                </div>
                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary">提交</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="image-config">
                            <form action="" method="post">
                                @csrf

                                <div class="form-group mb-3 ">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="open_image_host"
                                               @if( setting('open_image_host',0)) checked
                                               @endif onchange="$('input[name=\'open_image_host\']').val(Number(this.checked))">
                                        <span class="form-check-label">开启图床功能</span>
                                        <input type="hidden" name="open_image_host"
                                               value="{{ setting('open_image_host', 0) }}">
                                    </label>
                                    <span class="form-hint text-danger">开启后OneDrive可以作为图床使用</span>
                                </div>

                                <div class="form-group mb-3 ">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="public_image_host"
                                               @if( setting('public_image_host',0)) checked
                                               @endif onchange="$('input[name=\'public_image_host\']').val(Number(this.checked))">
                                        <span class="form-check-label">设为公有图床</span>
                                        <input type="hidden" name="public_image_host"
                                               value="{{ setting('public_image_host', 0) }}">
                                    </label>
                                    <span class="form-hint text-danger">开启后任何人都可以访问使用</span>
                                </div>

                                <div class="form-group mb-3 ">
                                    <label class="form-label" for="image_host_account">选择图床账号</label>
                                    <div>
                                        <select class="form-select" name="image_host_account" id="image_host_account">
                                            @foreach( $accounts as $key => $account)
                                                <option value="{{ $account['id'] }}"
                                                        @if(setting('image_host_account') === $account['id'] ) selected @endif>
                                                    {{ $account['remark'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-hint text-danger">图床默认将使用主账号.</small>
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

        </div>
    </div>
@stop
