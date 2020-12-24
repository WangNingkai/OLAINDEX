@extends('default.layouts.main')
@section('title', '账号设置')
@section('content')
    <div class="card border-light mb-3 shadow">
        <div class="card-header">账号设置</div>
        <div class="card-body">
            <form action="" method="post">
                @csrf
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input"
                               id="open_sp"
                               @if($config['open_sp'] ?? 0) checked @endif
                               onchange="$('input[name=\'config[open_sp]\']').val(Number(this.checked))">
                        <label class="custom-control-label" for="open_sp">开启sharepoint挂载</label>
                        <input type="hidden" name="config[open_sp]" value="{{ $config['open_sp'] ?? 0}}">
                    </div>
                    <br>
                    <span class=" form-text text-danger">开启sharepoint挂载将使用切换为sharepoint</span>
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="sp"><b>SharePoint地址：</b></label>
                    <input type="text" class="form-control" id="sp" name="config[sp]"
                           value="{{ $config['sp'] ?? ''}}">
                    <span class=" form-text text-danger">site_id:{{ $config['sp_id'] ?? '-'}}</span>
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="root"><b>起始目录</b></label>
                    <input type="text" class="form-control" id="root" name="config[root]"
                           value="{{ array_get($config, 'root', '') }}">
                    <span class=" form-text text-danger">索引开始目录，修改为sharepoint需要注意此项设置</span>
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="image_path"><b>图床地址</b></label>
                    <input type="text" class="form-control" id="image_path" name="config[image_path]"
                           value="{{ array_get($config, 'image_path', '/') }}">
                </div>
                <div class="form-group">
                    <label for="encrypt_path"><b>加密路径</b></label>
                    <textarea class="form-control" id="encrypt_path" name="config[encrypt_path]"
                              rows="3">{{ array_get($config, 'encrypt_path', '') }}</textarea>
                    <span class=" form-text text-danger">加密的文件、文件夹需要密码访问，形式"目录:密码"，使用"|"隔开，分隔符为英文符号</span>
                </div>
                <div class="form-group">
                    <label for="hide_path"><b>隐藏路径</b></label>
                    <textarea class="form-control" id="hide_path" name="config[hide_path]"
                              rows="3">{{ array_get($config, 'hide_path', '') }}</textarea>
                    <span class=" form-text text-danger">标记的文件、文件夹前台不显示，使用"|"隔开，分隔符为英文符号</span>
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="list_limit"><b>列表默认显示条数</b></label>
                    <input type="text" class="form-control" id="list_limit" name="config[list_limit]"
                           value="{{ array_get($config, 'list_limit', 10) }}">
                </div>

                <button type="submit" class="btn btn-primary">提交</button>
                <a href="{{ route('admin.account.list') }}" class="btn btn-danger"> <i
                        class="ri-arrow-go-back-fill"></i> 返回</a>
            </form>
        </div>
    </div>
@stop
