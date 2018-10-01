<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4/dist/{{ \App\Helpers\Tool::config('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-fileinput@4.5.0/css/fileinput.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('list') }}">{{ \App\Helpers\Tool::config('name','OLAINDEX') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container" style="margin-top: 10px">
    @if (session()->has('alertMessage'))
        <div class="alert alert-dismissible alert-{{ session()->pull('alertType')}}">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{{ session()->pull('alertMessage') }}</p>
        </div>
    @endif
    <div class="card border-light mb-3">
        <div class="card-header">
            <h3 class="card-title">图床</h3>
            <span>每个文件最大 5 MB . 每分钟请求最多10个文件.</span>
        </div>
        <div class="card-body">
            <form enctype="multipart/form-data">
                <div class="form-group">
                    <input id="olaindex_img" type="file" multiple class="file" data-overwrite-initial="false" data-min-file-count="1" data-max-file-count="10" name="olaindex_img" accept="image/*">
                </div>
            </form>
            <div id="showurl" style="display: none;">
                <ul id="navTab" class="nav nav-tabs">
                    <li class="active"><a href="#urlcodes" data-toggle="tab">URL</a></li>
                    <li><a href="#htmlcodes" data-toggle="tab">HTML</a></li>
                    <li><a href="#bbcodes" data-toggle="tab">BBCode</a></li>
                    <li><a href="#markdowncodes" data-toggle="tab">Markdown</a></li>
                    <li><a href="#markdowncodes2" data-toggle="tab">Markdown with Link</a></li>
                    <li><a href="#deletepanel" data-toggle="tab">Delete Link</a></li>
                </ul>
                <div id="navTabContent" class="tab-content">
                    <div class="tab-pane fade in active" id="urlcodes">
                        <pre style="margin-top: 5px;"><code id="urlcode"></code></pre>
                    </div>
                    <div class="tab-pane fade" id="htmlcodes">
                        <pre style="margin-top: 5px;"><code id="htmlcode"></code></pre>
                    </div>
                    <div class="tab-pane fade" id="bbcodes">
                        <pre style="margin-top: 5px;"><code id="bbcode"></code></pre>
                    </div>
                    <div class="tab-pane fade" id="markdowncodes">
                        <pre style="margin-top: 5px;"><code id="markdown"></code></pre>
                    </div>
                    <div class="tab-pane fade" id="markdowncodes2">
                        <pre style="margin-top: 5px;"><code id="markdownlinks"></code></pre>
                    </div>
                    <div class="tab-pane fade" id="deletepanel">
                        <pre style="margin-top: 5px;"><code id="deletecode"></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer id="footer">
        <div class="row text-center">
            <div class="col-lg-12">
                <p>Made by <a href="http://imwnk.cn">IMWNK</a>.</p>
            </div>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-fileinput@4.5.0/js/fileinput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-fileinput@4.5.0/js/locales/zh.min.js"></script>
<script>
    $("#olaindex_img").fileinput({
        language: 'zh',
        uploadUrl: '{{ route('image.upload') }}',
        allowedFileExtensions: ["jpeg", "jpg", "png", "gif", "bmp"],
        uploadExtraData:{"_token": "{{ csrf_token() }}"},
        overwriteInitial: false,
        maxFileSize: 5120,
        maxFilesNum: 10,
        maxFileCount: 10,
    });
    $("#olaindex_img").on("fileuploaded", function (event, data, previewId, index) {
        var form = data.form, files = data.files, extra = data.extra, response = data.response, reader = data.reader;
        if (response.code == "success") {
            if ($("showurl").css("display")) {
                $("#urlcode").append(response.data.url + "\n");
                $("#htmlcode").append("&lt;img src=\"" + response.data.url + "\" alt=\"" + files[index].name + "\" title=\"" + files[index].name + "\" /&gt;" + "\n");
                $("#bbcode").append("[img]" + response.data.url + "[/img]" + "\n");
                $("#markdown").append("![" + files[index].name + "](" + response.data.url + ")" + "\n");
                $("#markdownlinks").append("[![" + files[index].name + "](" + response.data.url + ")]" + "(" + response.data.url + ")" + "\n");
                $("#deletecode").append(response.data.delete + "\n")
            } else if (response.data.url) {
                $("#showurl").show();
                $("#urlcode").append(response.data.url + "\n");
                $("#htmlcode").append("&lt;img src=\"" + response.data.url + "\" alt=\"" + files[index].name + "\" title=\"" + files[index].name + "\" /&gt;" + "\n");
                $("#bbcode").append("[img]" + response.data.url + "[/img]" + "\n");
                $("#markdown").append("![" + files[index].name + "](" + response.data.url + ")" + "\n");
                $("#markdownlinks").append("[![" + files[index].name + "](" + response.data.url + ")]" + "(" + response.data.url + ")" + "\n");
                $("#deletecode").append(response.data.delete + "\n")
            }
        }
    });
</script>
</body>
</html>
