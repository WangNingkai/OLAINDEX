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
                       value="{{ \App\Helpers\Tool::config('name','OLAINDEX') }}">
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="root">OneDrive根目录</label>
                <input type="text" class="mdui-textfield-input" id="root" name="root"
                       value="{{ \App\Helpers\Tool::config('root') }}" required>
                <div class="mdui-textfield-helper">目录索引起始文件夹地址，文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ | 否则无法索引。</div>
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="expires">缓存时间(秒)</label>
                <input type="text" class="mdui-textfield-input" id="expires" name="expires"
                       value="{{ \App\Helpers\Tool::config('expires',0) }}">
                <div class="mdui-textfield-helper">建议缓存时间小于60分钟，否则会导致缓存失效</div>
            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="encrypt_path">加密</label>
                <textarea name="encrypt_path" id="encrypt_path" class="mdui-textfield-input"
                          rows="3">{{ \App\Helpers\Tool::config('encrypt_path','') }}</textarea>
                <div class="mdui-textfield-helper">填写需要加密的文件或文件夹路径，格式如： /path1/xxx/ /path2/xxx/ password1,/path3/xxx/ /path4/xxx/ password2 (以OneDrive根目录为基础)
                </div>
            </div>
            <br>
            <label for="image_hosting" class="mdui-textfield-label">加密选项</label> &nbsp; &nbsp;
            <br>
            <label class="mdui-checkbox">
                <input type="checkbox" name="encrypt_option[]" value="list"
                       @if(in_array('list',\App\Helpers\Tool::config('encrypt_option',[]))) checked @endif/>
                <i class="mdui-checkbox-icon"></i>
                加密目录列表
            </label> &nbsp; &nbsp;
            <label class="mdui-checkbox">
                <input type="checkbox" name="encrypt_option[]" value="show"
                       @if(in_array('show',\App\Helpers\Tool::config('encrypt_option',[]))) checked @endif/>
                <i class="mdui-checkbox-icon"></i>
                加密文件预览
            </label> &nbsp; &nbsp;
            <label class="mdui-checkbox">
                <input type="checkbox" name="encrypt_option[]" value="download"
                       @if(in_array('download',\App\Helpers\Tool::config('encrypt_option',[]))) checked @endif/>
                <i class="mdui-checkbox-icon"></i>
                加密文件下载
            </label> &nbsp; &nbsp;
            <label class="mdui-checkbox">
                <input type="checkbox" name="encrypt_option[]" value="view"
                       @if(in_array('view',\App\Helpers\Tool::config('encrypt_option',[]))) checked @endif/>
                <i class="mdui-checkbox-icon"></i>
                加密图片查看页
            </label> &nbsp; &nbsp;
            <br>
            <br>
            <label for="image_hosting" class="mdui-textfield-label">是否开启图床</label> &nbsp; &nbsp;
            <br>
            <select name="image_hosting" id="image_hosting" class="mdui-select" mdui-select="{position: 'bottom'}">
                <option value="1" @if(\App\Helpers\Tool::config('image_hosting') == 1) selected @endif>开启</option>
                <option value="0" @if(\App\Helpers\Tool::config('image_hosting') == 0) selected @endif>关闭</option>
                <option value="2" @if(\App\Helpers\Tool::config('image_hosting') == 2) selected @endif>仅管理员开启</option>
            </select>
            <br>
            <br>
            <label for="image_home" class="mdui-textfield-label">是否将图床设为首页</label> &nbsp; &nbsp;
            <br>
            <select name="image_home" id="image_home" class="mdui-select" mdui-select="{position: 'bottom'}">
                <option value="">开启图床设为首页</option>
                <option value="1" @if(\App\Helpers\Tool::config('image_home',0) == 1) selected @endif>开启</option>
                <option value="0" @if(\App\Helpers\Tool::config('image_home',0) == 0) selected @endif>关闭</option>
            </select>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="image_hosting_path">OneDrive中图床保存地址</label>
                <input type="text" class="mdui-textfield-input" id="image_hosting_path" name="image_hosting_path"
                       value="{{ \App\Helpers\Tool::config('image_hosting_path') }}">
                <div class="mdui-textfield-helper">文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: " * : <>? / \ |</div>
            </div>

            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="hotlink_protection">防盗链</label>
                <input type="text" class="mdui-textfield-input" id="hotlink_protection" name="hotlink_protection"
                       value="{{ \App\Helpers\Tool::config('hotlink_protection') }}">
                <div class="mdui-textfield-helper">留空则不开启。白名单链接以空格分开（此处采用 Http Referer 防盗链机制，如需加强请自行从服务器层面配置）</div>

            </div>
            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="copyright">自定义版权显示</label>
                <input type="text" class="mdui-textfield-input" id="copyright" name="copyright"
                       value="{{ \App\Helpers\Tool::config('copyright') }}">
                <div class="mdui-textfield-helper">留空则不显示。使用markdown格式表示 如：Made by [xxx](https://xxx)</div>
            </div>

            <br>
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="statistics">统计代码</label>
                <input type="text" class="mdui-textfield-input" id="statistics" name="statistics"
                       value="{{ \App\Helpers\Tool::config('statistics') }}">
                <div class="mdui-textfield-helper">js 统计代码</div>
            </div>

            <br>

            <button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" type="submit"><i
                    class="mdui-icon material-icons">check</i> 保存
            </button>
        </form>
    </div>
@stop
