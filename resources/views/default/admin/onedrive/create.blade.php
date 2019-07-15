@extends('default.layouts.admin')
@section('css')
<link href="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/css/fileinput.min.css" rel="stylesheet">
<link href="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/css/fileinput-rtl.min.css" rel="stylesheet">
@endSection
@section('title','新增 OneDrive')
@section('content')
@includeWhen($errors->isNotEmpty(), 'default.widgets.errors')
<form action="{{ route('admin.onedrive.store') }}" method="POST">
    @csrf
    <div class="form-group row">
        <label for="name" class="col-sm-2 col-form-label">名称</label>
        <div class="col-sm-10">
            <input type="text" name="name" class="form-control" id="name" placeholder="名称..." value="{{ old('name') }}">
        </div>
    </div>
    <div class="form-group row">
        <label for="root" class="col-sm-2 col-form-label">根目录</label>
        <div class="col-sm-10">
            <input type="text" name="root" class="form-control" id="root" placeholder="根目录..." value="{{ old('root') }}">
            <span class="form-text text-danger">目录索引起始文件夹地址，文件或文件夹名不能以点开始或结束，且不能包含以下任意字符: &quot; * : <>? / \ | 否则无法索引。</span>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="cover_id">
        <label for="image" class="col-sm-2 col-form-label">封面</label>
        <div class="col-sm-10">
            <input id="image" name="image" type="file">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">创建</button>
        </div>
    </div>
</form>
@stop
@section('js')
<script src="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/js/plugins/piexif.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/js/fileinput.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap-fileinput/5.0.4/themes/fa/theme.min.js"></script>
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
})
</script>
@endSection