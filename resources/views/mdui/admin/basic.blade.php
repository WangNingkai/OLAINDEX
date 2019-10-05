@extends('mdui.layouts.admin')
@section('content')
    <div class="mdui-container-fluid mdui-m-t-2 mdui-m-b-2">

        <div class="mdui-typo">
            <h1>基本设置
                <small>参数设置</small>
            </h1>
        </div>
        <form action="" method="post">
            @csrf
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="name">站点名称</label>
                <input type="text" class="mdui-textfield-input" id="name" name="name"
                       value="{{ setting('name','OLAINDEX') }}">
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="root">OneDrive根目录</label>
                <input type="text" class="mdui-textfield-input" id="root" name="root"
                       value="{{ setting('root') }}" required>
                <div class="mdui-textfield-helper">目录索引起始文件夹地址，文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ | 否则无法索引。
                </div>
            </div>
            <br>
            <label for="origin_path" class="mdui-textfield-label">路径兼容模式</label> &nbsp; &nbsp;
            <br>
            <select name="origin_path" id="origin_path" class="mdui-select" mdui-select="{position: 'bottom'}">
                <option value="1" @if(setting('origin_path',0) === 1) selected @endif>开启</option>
                <option value="0" @if(setting('origin_path',0) === 0) selected @endif>关闭</option>
            </select>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="expires">缓存时间(秒)</label>
                <input type="text" class="mdui-textfield-input" id="expires" name="expires"
                       value="{{ setting('expires',0) }}">
                <div class="mdui-textfield-helper">建议缓存时间小于60分钟，否则会导致缓存失效</div>
            </div>
            <br>
            <label for="queue_refresh" class="mdui-textfield-label">队列刷新缓存</label> &nbsp; &nbsp;
            <br>
            <select name="queue_refresh" id="queue_refresh" class="mdui-select" mdui-select="{position: 'bottom'}">
                <option value="1" @if(setting('queue_refresh',0) === 1) selected @endif>开启</option>
                <option value="0" @if(setting('queue_refresh',0) === 0) selected @endif>关闭</option>
            </select>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="hide_path">隐藏目录</label>
                <textarea name="encrypt_path" id="hide_path" class="mdui-textfield-input"
                          rows="3">{{ setting('hide_path') }}</textarea>
                <div class="mdui-textfield-helper">填写需要隐藏的文件或文件夹路径,每个组路径使用英文“|”隔开</div>
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="encrypt_path">加密目录</label>
                <textarea name="encrypt_path" id="encrypt_path" class="mdui-textfield-input"
                          rows="3">{{ setting('encrypt_path') }}</textarea>
                <div class="mdui-textfield-helper">填写需要加密的文件或文件夹路径，加密的目录使用英文“,”隔开；“:”后是每个加密组的密码，每个组加密使用英文“|”隔开，格式如：
                    /path1,/path2:password1|/path3,/path4:password2|... (以OneDrive根目录为基础)
                </div>
            </div>
            <br>
            <label for="image_hosting" class="mdui-textfield-label">加密选项</label> &nbsp; &nbsp;
            <br>
            <label class="mdui-checkbox">
                <input type="checkbox" name="encrypt_option[]" value="list"
                       @if(in_array('list',setting('encrypt_option',[]),false)) checked @endif/>
                <i class="mdui-checkbox-icon"></i>
                加密目录列表
            </label> &nbsp; &nbsp;
            <label class="mdui-checkbox">
                <input type="checkbox" name="encrypt_option[]" value="show"
                       @if(in_array('show',setting('encrypt_option',[]),false)) checked @endif/>
                <i class="mdui-checkbox-icon"></i>
                加密文件预览
            </label> &nbsp; &nbsp;
            <label class="mdui-checkbox">
                <input type="checkbox" name="encrypt_option[]" value="download"
                       @if(in_array('download',setting('encrypt_option',[]),false)) checked @endif/>
                <i class="mdui-checkbox-icon"></i>
                加密文件下载
            </label> &nbsp; &nbsp;
            <label class="mdui-checkbox">
                <input type="checkbox" name="encrypt_option[]" value="view"
                       @if(in_array('view',setting('encrypt_option',[]),false)) checked @endif/>
                <i class="mdui-checkbox-icon"></i>
                加密图片查看页
            </label> &nbsp; &nbsp;
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="encrypt_path">自定义加密文案</label>
                <textarea name="encrypt_tip" id="encrypt_tip" class="mdui-textfield-input"
                          rows="3">{{ setting('encrypt_tip') }}</textarea>
                <div class="mdui-textfield-helper">加密页文案（支持markdown）
                </div>
            </div>
            <br>
            <label for="image_hosting" class="mdui-textfield-label">是否开启图床</label> &nbsp; &nbsp;
            <br>
            <select name="image_hosting" id="image_hosting" class="mdui-select" mdui-select="{position: 'bottom'}">
                <option value="1" @if(setting('image_hosting') === 1) selected @endif>开启</option>
                <option value="0" @if(setting('image_hosting') === 0) selected @endif>关闭</option>
                <option value="2" @if(setting('image_hosting') === 2) selected @endif>仅管理员开启</option>
            </select>
            <br>
            <label for="image_home" class="mdui-textfield-label">是否将图床设为首页</label> &nbsp; &nbsp;
            <br>
            <select name="image_home" id="image_home" class="mdui-select" mdui-select="{position: 'bottom'}">
                <option value="">开启图床设为首页</option>
                <option value="1" @if(setting('image_home',0) === 1) selected @endif>开启</option>
                <option value="0" @if(setting('image_home',0) === 0) selected @endif>关闭</option>
            </select>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="image_hosting_path">OneDrive中图床保存地址</label>
                <input type="text" class="mdui-textfield-input" id="image_hosting_path" name="image_hosting_path"
                       value="{{ setting('image_hosting_path') }}">
                <div class="mdui-textfield-helper">文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ |</div>
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="image_upload_throttle">图床上传频次限制</label>
                <input type="text" class="mdui-textfield-input" id="image_upload_throttle" name="image_upload_throttle"
                       value="{{ setting('image_upload_throttle') }}">
                <div class="mdui-textfield-helper">重试等待时间默认是1分钟（格式：5,10，每10分钟最多上传5次；5 每分钟最多上传5次）</div>
            </div>
            <br>
            <label for="open_search" class="mdui-textfield-label">开启搜索</label> &nbsp; &nbsp;
            <br>
            <select name="open_search" id="open_search" class="mdui-select" mdui-select="{position: 'bottom'}">
                <option value="1" @if(setting('open_search') === 1) selected @endif>开启</option>
                <option value="0" @if(setting('open_search') === 0) selected @endif>关闭</option>
            </select>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="search_throttle">搜索频次限制</label>
                <input type="text" class="mdui-textfield-input" id="search_throttle" name="search_throttle"
                       value="{{ setting('search_throttle') }}">
                <div class="mdui-textfield-helper">重试等待时间默认是1分钟（格式：5,10，每10分钟最多搜索5次；5 每分钟最多搜索5次）</div>
            </div>
            <br>
            <label for="export_download" class="mdui-textfield-label">开启批量下载</label> &nbsp; &nbsp;
            <br>
            <select name="export_download" id="export_download" class="mdui-select" mdui-select="{position: 'bottom'}">
                <option value="1" @if(setting('export_download',0) === 1) selected @endif>开启</option>
                <option value="0" @if(setting('export_download',0) === 0) selected @endif>关闭</option>
            </select>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="hotlink_protection">防盗链</label>
                <input type="text" class="mdui-textfield-input" id="hotlink_protection" name="hotlink_protection"
                       value="{{ setting('hotlink_protection') }}">
                <div class="mdui-textfield-helper">留空则不开启。白名单链接以空格分开（此处采用 Http Referer 防盗链机制，如需加强请自行从服务器层面配置）</div>

            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="copyright">自定义版权显示</label>
                <input type="text" class="mdui-textfield-input" id="copyright" name="copyright"
                       value="{{ setting('copyright') }}">
                <div class="mdui-textfield-helper">留空则不显示。使用markdown格式表示 如：Made by [xxx](https://xxx)</div>
            </div>

            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="statistics">统计代码</label>
                <input type="text" class="mdui-textfield-input" id="statistics" name="statistics"
                       value="{{ setting('statistics') }}">
                <div class="mdui-textfield-helper">站点统计代码</div>
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="third_access_token">第三方接口token</label>
                <input type="text" class="mdui-textfield-input" id="third_access_token" name="third_access_token"
                       value="{{ setting('third_access_token') }}">
                <div class="mdui-textfield-helper">第三方接口token</div>
            </div>
            <br>
            <button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" type="submit"><i
                    class="mdui-icon material-icons">check</i> 保存
            </button>
        </form>
    </div>
@stop
