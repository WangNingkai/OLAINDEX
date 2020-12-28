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
@extends('default.layouts.main')
@section('title', '设置')
@section('content')
    <div class="card border-light mb-3 shadow">
        <div class="card-header">
            @include('default.components.admin-nav')
        </div>
        <div class="card-body">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-basic-tab" data-toggle="tab" href="#config-basic"
                       role="tab"
                       aria-controls="config-basic" aria-selected="true">基础设置</a>
                    <a class="nav-item nav-link" id="nav-show-tab" data-toggle="tab" href="#config-show" role="tab"
                       aria-controls="config-show" aria-selected="false">显示设置</a>
                    <a class="nav-item nav-link" id="nav-image-tab" data-toggle="tab" href="#config-image" role="tab"
                       aria-controls="config-image" aria-selected="false">图床设置</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="config-basic" role="tabpanel"
                     aria-labelledby="nav-basic-tab">
                    <div class="my-4">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-control-label" for="site_name"><b>网站名称</b></label>
                                <input type="text" class="form-control" id="site_name" name="site_name"
                                       value="{{ setting('site_name','OLAINDEX') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="site_theme"><b>全局主题</b></label>
                                <select class="custom-select" name="site_theme" id="site_theme">
                                    @foreach( $themes as $name => $theme)
                                        <option value="{{ $theme }}"
                                                @if(setting('site_theme') === $theme) selected @endif>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="main_theme"><b>显示主题</b></label>
                                <select class="custom-select" name="main_theme" id="main_theme">
                                    @foreach( $main_themes as $name => $theme)
                                        <option value="{{ $theme }}"
                                                @if(setting('main_theme') === $theme) selected @endif>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="cache_expires">网盘资源缓存时间(秒)</label>
                                <input type="text" class="form-control" id="cache_expires" name="cache_expires"
                                       value="{{ setting('cache_expires',1800) }}">
                                <span class="form-text text-danger">建议缓存时间小于60分钟，否则会导致缓存失效</span>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"
                                           id="open_short_url"
                                           @if( setting('open_short_url',0)) checked
                                           @endif onchange="$('input[name=\'open_short_url\']').val(Number(this.checked))">
                                    <label class="custom-control-label" for="open_short_url">开启预览短链</label>
                                    <input type="hidden" name="open_short_url"
                                           value="{{ setting('open_short_url', 0) }}">
                                </div>
                                <span class="form-text text-danger">开启后预览文件只显示短链</span>
                            </div>

                            <div class="form-group">
                                <div class="form-group">
                                    <label class="form-control-label" for="encrypt_tip">加密资源提示文案</label>
                                    <textarea class="form-control" id="encrypt_tip" name="encrypt_tip"
                                              rows="1">{{ setting('encrypt_tip') }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="copyright">页脚文案</label>
                                <textarea class="form-control" id="copyright" name="copyright"
                                          rows="3">{{ setting('copyright') }}</textarea>
                                <span
                                    class="form-text text-danger">留空则不显示。使用markdown格式表示 如：Made by [xxx](https://xxx)</span>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="stats_code">统计代码</label>
                                <textarea class="form-control" id="stats_code" name="stats_code"
                                          rows="3">{{ setting('stats_code') }}</textarea>
                                <span class="form-text text-danger">站点统计代码</span>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="access_token">第三方接口token</label>
                                <input type="text" class="form-control" id="access_token" name="access_token"
                                       value="{{ setting('access_token', '') }}">
                                <span class="form-text text-danger">第三方接口token(图床、文件列表)</span>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="api_limit">接口访问频率限制（次/分钟）</label>
                                <input type="text" class="form-control" id="api_limit" name="api_limit"
                                       value="{{ setting('api_limit', 30) }}">
                            </div>
                            <button type="submit" class="btn btn-primary">提交 <i class="ri-check-fill"></i></button>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-show" role="tabpanel" aria-labelledby="nav-show-tab">
                    <div class="my-4">
                        <p class="form-text text-danger">文件展示类型（扩展名）以空格分开</p>
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-control-label" for="show_image">图片</label>
                                <input type="text" class="form-control" id="show_image" name="show_image"
                                       value="{{ setting('show_image', '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="show_video">视频</label>
                                <input type="text" class="form-control" id="show_video" name="show_video"
                                       value="{{ setting('show_video', '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="show_dash">Dash视频</label>
                                <input type="text" class="form-control" id="show_dash" name="show_dash"
                                       value="{{ setting('show_dash', '') }}">
                                <span class="form-text text-danger">不支持个人版账户</span>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="show_audio">音频</label>
                                <input type="text" class="form-control" id="show_audio" name="show_audio"
                                       value="{{ setting('show_audio', '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="show_doc">文档</label>
                                <input type="text" class="form-control" id="show_doc" name="show_doc"
                                       value="{{ setting('show_doc', '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="show_code">代码</label>
                                <input type="text" class="form-control" id="show_code" name="show_code"
                                       value="{{ setting('show_code', '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="show_stream">文件流</label>
                                <input type="text" class="form-control" id="show_stream" name="show_stream"
                                       value="{{ setting('show_stream', '') }}">
                            </div>
                            <button type="submit" class="btn btn-primary">提交 <i class="ri-check-fill"></i></button>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-image" role="tabpanel" aria-labelledby="nav-image-tab">
                    <div class="my-4">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"
                                           id="open_image_host"
                                           @if( setting('open_image_host',0)) checked
                                           @endif onchange="$('input[name=\'open_image_host\']').val(Number(this.checked))">
                                    <label class="custom-control-label" for="open_image_host">开启图床功能</label>
                                    <input type="hidden" name="open_image_host"
                                           value="{{ setting('open_image_host', 0) }}">
                                </div>
                                <span class="form-text text-danger">开启后OneDrive可以作为图床使用</span>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"
                                           id="public_image_host"
                                           @if( setting('public_image_host',0)) checked
                                           @endif onchange="$('input[name=\'public_image_host\']').val(Number(this.checked))">
                                    <label class="custom-control-label" for="public_image_host">公共图床功能</label>
                                    <input type="hidden" name="public_image_host"
                                           value="{{ setting('public_image_host', 0) }}">
                                </div>
                                <span class="form-text text-danger">开启后任何人都可以访问使用</span>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="image_host_account"><b>选择图床账号</b></label>
                                <select class="custom-select" name="image_host_account" id="image_host_account">
                                    @foreach( $accounts as $key => $account)
                                        <option value="{{ $account['id'] }}"
                                                @if(setting('image_host_account') === $account['id'] ) selected @endif>
                                            {{ $account['remark'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="form-text text-danger">图床默认将使用主账号</span>
                            </div>
                            <button type="submit" class="btn btn-primary">提交 <i class="ri-check-fill"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

