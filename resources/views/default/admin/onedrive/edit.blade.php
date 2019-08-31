@extends('default.layouts.admin')
@section('css')
<link href="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/css/fileinput.min.css" rel="stylesheet">
<link href="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/css/fileinput-rtl.min.css" rel="stylesheet">
<link href="https://cdn.bootcss.com/Chart.js/2.8.0/Chart.min.css" rel="stylesheet">
<style>
.pie-chart {
    max-height: 400px;
    max-width: 400px;
    margin: 0px auto 20px auto;
}
</style>
@endSection
@section('title','修改 OneDrive')
@section('content')
@includeWhen(!empty(session('message')), 'default.widgets.success')
@includeWhen($errors->isNotEmpty(), 'default.widgets.errors')
    <div class="">
        <p class="text-center text-muted">{{ Tool::getBindAccount() }}</p>
        <p class="text-center">
            <span class="text-info">状态: {{ Tool::getOneDriveInfo('state') }} &nbsp;&nbsp;</span>
            <span class="text-danger">已使用: {{ Tool::getOneDriveInfo('used') }} &nbsp;&nbsp;</span>
            <span class="text-warning">剩余: {{ Tool::getOneDriveInfo('remaining') }} &nbsp;&nbsp;</span>
            <span class="text-success">全部: {{ Tool::getOneDriveInfo('total') }} &nbsp;&nbsp;</span>
        </p>
    </div>
    <div>
        <canvas id="myChart" class="pie-chart"></canvas>
    </div>

<form action="{{ route('admin.onedrive.update', ['onedrive' => $oneDrive->id]) }}" method="POST">
    {{ method_field('PUT') }}
    @csrf
    <div class="form-group row">
        <label for="name" class="col-sm-2 col-form-label">名称</label>
        <div class="col-sm-10">
            <input type="text" name="name" class="form-control" id="name" placeholder="名称..." value="{{ $oneDrive->name }}">
        </div>
    </div>
    <div class="form-group row">
        <label for="root" class="col-sm-2 col-form-label">根目录</label>
        <div class="col-sm-10">
            <input type="text" name="root" class="form-control" id="root" placeholder="根目录..." value="{{ $oneDrive->root }}">
            <span class="form-text text-danger">目录索引起始文件夹地址，文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: &quot; * : <>? / \ | 否则无法索引。</span>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="cover_id" value="{{ !empty($oneDrive->cover) ? $oneDrive->cover->path : '' }}" data-image-id="{{ $oneDrive->cover_id }}">
        <label for="image" class="col-sm-2 col-form-label">封面</label>
        <div class="col-sm-10">
            <input id="image" name="image" type="file">
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2" for="expires">缓存时间(秒)</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="expires" name="expires" value="{{ $oneDrive->expires }}">
            <span class="form-text text-danger">建议缓存时间小于60分钟，否则会导致缓存失效</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2">是否开启图床</label>
        <div class="col-sm-10">
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting1" name="settings[image_hosting]" class="custom-control-input"
                    @if (Arr::get($oneDrive->settings, 'image_hosting') == 'enabled') checked @endif value="enabled">
                <label class="custom-control-label" for="image_hosting1">开启 &nbsp;&nbsp;</label>

            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting0" name="settings[image_hosting]" class="custom-control-input"
                    @if (Arr::get($oneDrive->settings, 'image_hosting') == 'disabled') checked @endif value="disabled">
                <label class="custom-control-label" for="image_hosting0">关闭 &nbsp;&nbsp;</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="image_hosting2" name="settings[image_hosting]" class="custom-control-input"
                    @if (Arr::get($oneDrive->settings, 'image_hosting') == 'admin_enabled') checked @endif value="admin_enabled">
                <label class="custom-control-label" for="image_hosting2">仅管理员开启 </label>
            </div>
        </div>
    </div>
    <div class="row">
        <label class="form-control-label col-sm-2">是否将图床设为首页</label>
        <div class="form-group col-sm-10">
            <input type="hidden" name="settings[image_home]" value="0">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="settings[image_home]" id="customSwitch1"
                    data-on-text="开启" data-off-text="关闭"
                    @if (Arr::get($oneDrive->settings, 'image_home')) checked @endif value="{{ Arr::get($oneDrive->settings, 'image_home') ? '1' : '0'}}">
                <label class="custom-control-label" for="customSwitch1"></label>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="root" class="col-sm-2 col-form-label">图床保存地址</label>
        <div class="col-sm-10">
            <input type="text" name="settings[image_hosting_path]" class="form-control" id="image_hosting_path" placeholder="图床保存地址..." value="{{ Arr::get($oneDrive->settings, 'image_hosting_path') }}">
        </div>
    </div>
    <div class="row">
        <label class="form-control-label col-sm-2">是否设为默认</label>
        <div class="form-group col-sm-10">
            <input type="hidden" name="is_default" value="0">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="is_default" id="is_default"
                    data-on-text="开启" data-off-text="关闭"
                    @if ($oneDrive->is_default) checked @endif value="{{ $oneDrive->is_default ? '1' : '0'}}">
                <label class="custom-control-label" for="is_default"></label>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="encrypt_path">加密</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="encrypt_path" name="encrypt_path" rows="5">{{ Arr::get($oneDrive->settings, 'encrypt_path', '') }}</textarea>
            <span class="form-text text-danger">填写需要加密的文件或文件夹路径，格式如： /path1/xxx/ /path2/xxx/ password1,/path3/xxx/ /path4/xxx/ password2 (以OneDrive根目录为基础)</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="form-control-label col-sm-2" for="">加密选项</label>
        <div class="col-sm-10">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input"  id="c1" name="settings[encrypt_option][]" value="list" @if (in_array('list', Arr::get($oneDrive->settings, 'encrypt_option', []))) checked @endif>
                <label class="custom-control-label" for="c1">加密目录列表</label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c2" name="settings[encrypt_option][]" value="show" @if (in_array('show', Arr::get($oneDrive->settings, 'encrypt_option', []))) checked @endif>
                <label class="custom-control-label" for="c2">加密文件预览</label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c3" name="settings[encrypt_option][]" value="download" @if (in_array('download', Arr::get($oneDrive->settings, 'encrypt_option', []))) checked @endif>
                <label class="custom-control-label" for="c3">加密文件下载</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="c4" name="settings[encrypt_option][]" value="view" @if (in_array('view', Arr::get($oneDrive->settings, 'encrypt_option', []))) checked @endif>
                <label class="custom-control-label" for="c4">加密图片查看页</label>
            </div>
            <span class="form-text text-danger">选择需要加密强度，默认加密列表</span>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <p class="pull-right text-danger">展示的文件后缀, 以空格分开</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="image">图片</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="image" name="image" value="{{ Arr::get($oneDrive->settings, 'image') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="video">视频</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="video" name="video" value="{{ Arr::get($oneDrive->settings, 'video') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="dash">Dash视频</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="dash" name="dash" value="{{ Arr::get($oneDrive->settings, 'dash') }}">
            <span class="form-text text-danger">不支持个人版账户</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="audio">音频</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="audio" name="audio" value="{{ Arr::get($oneDrive->settings, 'audio') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="doc">文档</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="doc" name="doc" value="{{ Arr::get($oneDrive->settings, 'doc') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="code">代码</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="code" name="code" value="{{ Arr::get($oneDrive->settings, 'code') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-sm-2" for="stream">文件流</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="stream" name="stream" value="{{ Arr::get($oneDrive->settings, 'stream') }}">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </div>
</form>
@stop

@section('js')
<script src="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/js/plugins/piexif.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/js/fileinput.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/themes/fa/theme.min.js"></script>
<script src="https://cdn.bootcss.com/Chart.js/2.8.0/Chart.min.js"></script>
<script type="text/javascript">
$(function () {
    var deleteImage = function () {
        var image_ids = [$("input[name='cover_id']").data('image-id')];
        
        $.ajax({
            type: "POST",
            url: "{{ route('admin.image.delete') }}",
            data: {
                "image_ids": image_ids
            },
            success: function () {
                $("input[name='cover_id']").val('');
            }
        });
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#image").fileinput({
        theme: 'fa',
        showUpload: false,
        dropZoneEnabled: false,
        maxFileCount: 1,
        uploadUrl: "{{ route('admin.image') }}",
        allowedFileExtensions: ['jpg','png', 'jpeg'],
        allowedFileTypes: ['image'],
        @if (!empty($oneDrive->cover))
        initialPreviewAsData: true, 
        initialPreview: [
            "{{ $oneDrive->cover->path }}",
        ],
        initialPreviewConfig: [
            {
                caption: "{{ $oneDrive->cover->path }}",
                url: "{{ route('admin.image.delete') }}",// 预展示图片的删除调取路径  
                key: 100,// 可修改 场景2中会用的  
                extra: {image_ids: [ {{$oneDrive->cover_id}} ]} //调用删除路径所传参数  
            }
        ]     
        @endif
    }).on('fileuploaded', function(event, previewId, index, fileId) {
        var path = previewId.response.data.path;
        var image_id = previewId.response.data.id;
        $("input[name='cover_id']").val(image_id)
        $("input[name='cover_id']").data('image-id', image_id);
        var $index = $('#' + index);
        $index.find('img').attr('title', path);
        $index.find('.file-caption-info').text(path);
        $(".file-caption-name").attr('title', path);
        $(".file-caption-name").val(path);
    }).on('fileclear', function(event) {
        deleteImage();
    }).on('filesuccessremove', function(event, id) {
        deleteImage();
    });

    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'doughnut',

        // The data for our dataset
        data: {
            labels: ['已使用', '剩余'],
            datasets: [{
                label: '使用情况',
                backgroundColor: ["#FF0039","#FF7518"],
                {{--  borderColor: 'rgb(255, 99, 132)',  --}}
                data: [{{ Str::before(Tool::getOneDriveInfo('used'), ' ') }}, {{ convertUnit(Tool::getOneDriveInfo('used'), Tool::getOneDriveInfo('remaining')) }}]
            }]
        },

        // Configuration options go here
        options: {}
    });

})
</script>
@endSection