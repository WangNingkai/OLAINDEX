@extends('default.layouts.admin')
@section('title','基础设置')
@section('content')
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="name">站点名称</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="{{ setting('name')}}">
        </div>
        <div class="form-group">
            <label class="form-control-label" for="theme">站点主题</label>
            <select class="custom-select" name="theme" id="theme">
                @foreach( \App\Utils\Extension::THEME as $name => $theme)
                    <option value="{{ $theme }}" @if(setting('theme') === $theme) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="root">OneDrive根目录</label>
            <input type="text" class="form-control" id="root" name="root" value="{{ setting('root') }}">
            <span class="form-text text-danger">目录索引起始文件夹地址，文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ | 否则无法索引。</span>
        </div>
        <div class="form-group">
            <label class="form-control-label">路径兼容模式</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="origin_path1" name="origin_path" class="custom-control-input"
                       @if((int)setting('origin_path',0) === 1) checked @endif value="1">
                <label class="custom-control-label" for="origin_path1">是</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="origin_path0" name="origin_path" class="custom-control-input"
                       @if((int)setting('origin_path',0) === 0) checked @endif value="0">
                <label class="custom-control-label" for="origin_path0">否</label>
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="expires">缓存时间(秒)</label>
            <input type="text" class="form-control" id="expires" name="expires" value="{{ setting('expires',1800) }}">
            <span class="form-text text-danger">建议缓存时间小于60分钟，否则会导致缓存失效</span>
        </div>
        <div class="form-group">
            <label class="form-control-label">队列刷新缓存</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="queue_refresh1" name="queue_refresh" class="custom-control-input"
                       @if((int)setting('queue_refresh',0) === 1) checked @endif value="1">
                <label class="custom-control-label" for="queue_refresh1">开启</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="queue_refresh0" name="queue_refresh" class="custom-control-input"
                       @if((int)setting('queue_refresh',0) === 0) checked @endif value="0">
                <label class="custom-control-label" for="queue_refresh0">关闭</label>
            </div>
            <span class="form-text text-danger">需要后台配置队列守护任务</span>
        </div>
        <div class="form-group">
            <label for="hide_path">目录隐藏</label>
            <textarea class="form-control" id="hide_path" name="hide_path"
                      rows="5">{{ setting('hide_path') }}</textarea>
            <span class="form-text text-danger">填写需要隐藏的文件或文件夹路径,每个组使用英文“|”隔开</span>
        </div>
        <div class="form-group">
            <label for="encrypt_path">目录加密</label>
            <textarea class="form-control" id="encrypt_path" name="encrypt_path"
                      rows="5">{{ setting('encrypt_path') }}</textarea>
            <span class="form-text text-danger">填写需要加密的文件或文件夹路径，加密的目录使用英文“,”隔开；“:”后是每个加密组的密码，每个组加密使用英文“|”隔开，格式如： /path1,/path2:password1|/path3,/path4:password2|... (以OneDrive根目录为基础)</span>
        </div>
        <div class="form-group">
            <label for="">加密选项</label>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c1" name="encrypt_option[]" value="list"
                       @if(in_array('list',setting('encrypt_option',[]),false)) checked @endif>
                <label class="custom-control-label" for="c1">加密目录列表</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c2" name="encrypt_option[]" value="show"
                       @if(in_array('show',setting('encrypt_option',[]),false)) checked @endif>
                <label class="custom-control-label" for="c2">加密文件预览</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c3" name="encrypt_option[]" value="download"
                       @if(in_array('download',setting('encrypt_option',[]),false)) checked @endif>
                <label class="custom-control-label" for="c3">加密文件下载</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c4" name="encrypt_option[]" value="view"
                       @if(in_array('view',setting('encrypt_option',[]),false)) checked @endif>
                <label class="custom-control-label" for="c4">加密图片查看页</label>
            </div>
            <span class="form-text text-danger">选择需要加密强度，默认加密列表</span>
        </div>
        <div class="form-group">
            <label for="encrypt_tip">自定义加密文案</label>
            <textarea class="form-control" id="encrypt_tip" name="encrypt_tip"
                      rows="5">{{ setting('encrypt_tip') }}</textarea>
            <span class="form-text text-danger">加密页提示文案（支持markdown）</span>
        </div>
        <div class="form-group">
            <label class="form-control-label">是否开启图床</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting1" name="image_hosting" class="custom-control-input"
                       @if((int)setting('image_hosting',0) === 1) checked @endif value="1">
                <label class="custom-control-label" for="image_hosting1">开启</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting0" name="image_hosting" class="custom-control-input"
                       @if((int)setting('image_hosting',0) === 0) checked @endif value="0">
                <label class="custom-control-label" for="image_hosting0">关闭</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting2" name="image_hosting" class="custom-control-input"
                       @if((int)setting('image_hosting',0) === 2) checked @endif value="2">
                <label class="custom-control-label" for="image_hosting2">仅管理员开启</label>
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-label">是否将图床设为首页</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_home1" name="image_home" class="custom-control-input"
                       @if((int)setting('image_home',0) === 1) checked @endif value="1">
                <label class="custom-control-label" for="image_home1">是</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_home0" name="image_home" class="custom-control-input"
                       @if((int)setting('image_home',0) === 0) checked @endif value="0">
                <label class="custom-control-label" for="image_home0">否</label>
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="image_hosting_path">OneDrive中图床保存地址</label>
            <input type="text" class="form-control" id="image_hosting_path" name="image_hosting_path"
                   value="{{ setting('image_hosting_path') }}">
            <span class="form-text text-danger">文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ |。</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="image_upload_throttle">图床上传频次限制</label>
            <input type="text" class="form-control" id="image_upload_throttle" name="image_upload_throttle"
                   value="{{ setting('image_upload_throttle') }}">
            <span class="form-text text-danger">重试等待时间默认是1分钟（格式：5,10，每10分钟最多上传5次；5 每分钟最多上传5次）</span>
        </div>
        <div class="form-group">
            <label class="form-control-label">开启搜索</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="open_search1" name="open_search" class="custom-control-input"
                       @if((int)setting('open_search',0) === 1) checked @endif value="1">
                <label class="custom-control-label" for="open_search1">开启</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="open_search0" name="open_search" class="custom-control-input"
                       @if((int)setting('open_search',0) === 0) checked @endif value="0">
                <label class="custom-control-label" for="open_search0">关闭</label>
            </div>
            <span class="form-text text-danger">搜索资源（过度开放会增加账号封禁风险）</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="search_throttle">搜索频次限制</label>
            <input type="text" class="form-control" id="search_throttle" name="search_throttle"
                   value="{{ setting('search_throttle') }}">
            <span class="form-text text-danger">重试等待时间默认是1分钟（格式：5,10，每10分钟最多搜索5次；5 每分钟最多搜索5次）</span>
        </div>
        <div class="form-group">
            <label class="form-control-label">开启批量下载</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="export_download1" name="export_download" class="custom-control-input"
                       @if((int)setting('export_download',0) === 1) checked @endif value="1">
                <label class="custom-control-label" for="export_download1">开启</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="export_download0" name="export_download" class="custom-control-input"
                       @if((int)setting('export_download',0) === 0) checked @endif value="0">
                <label class="custom-control-label" for="export_download0">关闭</label>
            </div>
            <span class="form-text text-danger">前台显示资源批量下载地址</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="hotlink_protection">防盗链</label>
            <input type="text" class="form-control" id="hotlink_protection" name="hotlink_protection"
                   value="{{ setting('hotlink_protection') }}">
            <span class="form-text text-danger">留空则不开启。白名单链接以空格分开（此处采用 Http Referer 防盗链机制，如需加强请自行从服务器层面配置）</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="copyright">自定义版权显示</label>
            <input type="text" class="form-control" id="copyright" name="copyright"
                   value="{{ setting('copyright') }}">
            <span class="form-text text-danger">留空则不显示。使用markdown格式表示 如：Made by [xxx](https://xxx)</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="statistics">统计代码</label>
            <input type="text" class="form-control" id="statistics" name="statistics"
                   value="{{ setting('statistics') }}">
            <span class="form-text text-danger">站点统计代码</span>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="third_access_token">第三方接口token</label>
            <input type="text" class="form-control" id="third_access_token" name="third_access_token"
                   value="{{ setting('third_access_token') }}">
            <span class="form-text text-danger">第三方接口token(图床、文件列表)</span>
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
@stop
