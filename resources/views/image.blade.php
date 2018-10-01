<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>图床</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4/dist/{{ \App\Helpers\Tool::config('theme','materia') }}/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/webuploader@0.1.8/dist/webuploader.min.css">
    <style>
        #uploader .queueList {
            margin: 20px;
            border: 3px dashed #e6e6e6;
        }
        #uploader .queueList.filled {
            padding: 17px;
            margin: 0;
            border: 3px dashed transparent;
        }
        #uploader .queueList.webuploader-dnd-over {
            border: 3px dashed #999999;
        }

        #uploader p {margin: 0;}

        .element-invisible {
            position: absolute !important;
            clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
            clip: rect(1px,1px,1px,1px);
        }

        #uploader .placeholder {
            min-height: 350px;
            padding-top: 178px;
            text-align: center;
            background: url(https://cdn.jsdelivr.net/npm/webuploader@0.1.8/_draft/music/image.png) center 93px no-repeat;
            color: #cccccc;
            font-size: 18px;
            position: relative;
        }

        #uploader .placeholder .webuploader-pick {
            font-size: 18px;
            background: #00b7ee;
            border-radius: 3px;
            line-height: 44px;
            padding: 0 30px;
            *width: 120px;
            color: #fff;
            display: inline-block;
            margin: 0 auto 20px auto;
            cursor: pointer;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        #uploader .placeholder .webuploader-pick-hover {
            background: #00a2d4;
        }

        #uploader .placeholder .flashTip {
            color: #666666;
            font-size: 12px;
            position: absolute;
            width: 100%;
            text-align: center;
            bottom: 20px;
        }
        #uploader .placeholder .flashTip a {
            color: #0785d1;
            text-decoration: none;
        }
        #uploader .placeholder .flashTip a:hover {
            text-decoration: underline;
        }

        #uploader .filelist {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        #uploader .filelist:after {
            content: '';
            display: block;
            width: 0;
            height: 0;
            overflow: hidden;
            clear: both;
        }

        #uploader .filelist li {
            width: 110px;
            height: 110px;
            background: url(https://cdn.jsdelivr.net/npm/webuploader@0.1.8/_draft/music/bg.png) no-repeat;
            text-align: center;
            margin: 0 8px 20px 0;
            position: relative;
            display: inline;
            float: left;
            overflow: hidden;
            font-size: 12px;
        }

        #uploader .filelist li p.log {
            position: relative;
            top: -45px;
        }

        #uploader .filelist li p.title {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow : ellipsis;
            top: 5px;
            text-indent: 5px;
            text-align: left;
        }

        #uploader .filelist li p.progress {
            position: absolute;
            width: 100%;
            bottom: 0;
            left: 0;
            height: 8px;
            overflow: hidden;
            z-index: 50;
            margin: 0;
            border-radius: 0;
            background: none;
            -webkit-box-shadow: 0 0 0;
        }
        #uploader .filelist li p.progress span {
            display: none;
            overflow: hidden;
            width: 0;
            height: 100%;
            background: #1483d8 url(https://cdn.jsdelivr.net/npm/webuploader@0.1.8/_draft/music/icons.png) repeat-x;

            -webit-transition: width 200ms linear;
            -moz-transition: width 200ms linear;
            -o-transition: width 200ms linear;
            -ms-transition: width 200ms linear;
            transition: width 200ms linear;

            -webkit-animation: progressmove 2s linear infinite;
            -moz-animation: progressmove 2s linear infinite;
            -o-animation: progressmove 2s linear infinite;
            -ms-animation: progressmove 2s linear infinite;
            animation: progressmove 2s linear infinite;

            -webkit-transform: translateZ(0);
        }

        @-webkit-keyframes progressmove {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 17px 0;
            }
        }
        @-moz-keyframes progressmove {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 17px 0;
            }
        }
        @keyframes progressmove {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 17px 0;
            }
        }

        #uploader .filelist li p.imgWrap {
            position: relative;
            z-index: 2;
            line-height: 110px;
            vertical-align: middle;
            overflow: hidden;
            width: 110px;
            height: 110px;

            -webkit-transform-origin: 50% 50%;
            -moz-transform-origin: 50% 50%;
            -o-transform-origin: 50% 50%;
            -ms-transform-origin: 50% 50%;
            transform-origin: 50% 50%;

            -webit-transition: 200ms ease-out;
            -moz-transition: 200ms ease-out;
            -o-transition: 200ms ease-out;
            -ms-transition: 200ms ease-out;
            transition: 200ms ease-out;
        }

        #uploader .filelist li img {
            width: 100%;
        }

        #uploader .filelist li p.error {
            background: #f43838;
            color: #fff;
            position: absolute;
            bottom: 0;
            left: 0;
            height: 28px;
            line-height: 28px;
            width: 100%;
            z-index: 100;
        }

        #uploader .filelist li .success {
            display: block;
            position: absolute;
            left: 0;
            bottom: 0;
            height: 40px;
            width: 100%;
            z-index: 200;
            background: url(https://cdn.jsdelivr.net/npm/webuploader@0.1.8/_draft/music/success.png) no-repeat right bottom;
        }

        #uploader .filelist div.file-panel {
            position: absolute;
            height: 0;
            filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#80000000', endColorstr='#80000000')\0;
            background: rgba( 0, 0, 0, 0.5 );
            width: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 300;
        }

        #uploader .filelist div.file-panel span {
            width: 24px;
            height: 24px;
            display: inline;
            float: right;
            text-indent: -9999px;
            overflow: hidden;
            background: url(https://cdn.jsdelivr.net/npm/webuploader@0.1.8/_draft/music/icons.png) no-repeat;
            margin: 5px 1px 1px;
            cursor: pointer;
        }

        #uploader .filelist div.file-panel span.rotateLeft {
            background-position: 0 -24px;
        }
        #uploader .filelist div.file-panel span.rotateLeft:hover {
            background-position: 0 0;
        }

        #uploader .filelist div.file-panel span.rotateRight {
            background-position: -24px -24px;
        }
        #uploader .filelist div.file-panel span.rotateRight:hover {
            background-position: -24px 0;
        }

        #uploader .filelist div.file-panel span.cancel {
            background-position: -48px -24px;
        }
        #uploader .filelist div.file-panel span.cancel:hover {
            background-position: -48px 0;
        }

        #uploader .statusBar {
            height: 63px;
            border-top: 1px solid #dadada;
            padding: 0 20px;
            line-height: 63px;
            vertical-align: middle;
            position: relative;
        }

        #uploader .statusBar .progress {
            border: 1px solid #1483d8;
            width: 198px;
            background: #fff;
            height: 18px;
            position: relative;
            display: inline-block;
            text-align: center;
            line-height: 20px;
            color: #6dbfff;
            position: relative;
            margin: 0 10px 0 0;
        }
        #uploader .statusBar .progress span.percentage {
            width: 0;
            height: 100%;
            left: 0;
            top: 0;
            background: #1483d8;
            position: absolute;
        }
        #uploader .statusBar .progress span.text {
            position: relative;
            z-index: 10;
        }

        #uploader .statusBar .info {
            display: inline-block;
            font-size: 14px;
            color: #666666;
        }

        #uploader .statusBar .btns {
            position: absolute;
            top: 10px;
            right: 20px;
            line-height: 40px;
        }

        #filePicker2 {
            display: inline-block;
            float: left;
        }

        #uploader .statusBar .btns .webuploader-pick,
        #uploader .statusBar .btns .uploadBtn,
        #uploader .statusBar .btns .uploadBtn.state-uploading,
        #uploader .statusBar .btns .uploadBtn.state-paused {
            background: #ffffff;
            border: 1px solid #cfcfcf;
            color: #565656;
            padding: 0 18px;
            display: inline-block;
            border-radius: 3px;
            margin-left: 10px;
            cursor: pointer;
            font-size: 14px;
            float: left;
        }
        #uploader .statusBar .btns .webuploader-pick-hover,
        #uploader .statusBar .btns .uploadBtn:hover,
        #uploader .statusBar .btns .uploadBtn.state-uploading:hover,
        #uploader .statusBar .btns .uploadBtn.state-paused:hover {
            background: #f0f0f0;
        }

        #uploader .statusBar .btns .uploadBtn {
            background: #00b7ee;
            color: #fff;
            border-color: transparent;
        }
        #uploader .statusBar .btns .uploadBtn:hover {
            background: #00a2d4;
        }

        #uploader .statusBar .btns .uploadBtn.disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
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
<div class="container" style="margin-top: 20px">
    @if (session()->has('alertMessage'))
        <div class="alert alert-dismissible alert-{{ session()->pull('alertType')}}">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{{ session()->pull('alertMessage') }}</p>
        </div>
    @endif
    <div class="card border-light mb-3">
        <div class="card-body">
            <div class="page-container">
                <h3>图床</h3>
                <p>您可以尝试文件拖拽，使用QQ截屏工具，然后激活窗口后粘贴，或者点击添加图片按钮.</p>
                <div id="uploader" class="wu-example">
                    <div class="queueList">
                        <div id="dndArea" class="placeholder">
                            <div id="filePicker"></div>
                            <p>或将照片拖到这里，单次最多可选10张</p>
                        </div>
                    </div>
                    <div class="statusBar" style="display:none;">
                        <div class="progress">
                            <span class="text">0%</span>
                            <span class="percentage"></span>
                        </div><div class="info"></div>
                        <div class="btns">
                            <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="showTab" style="display: none;">
        <ul id="navTab" class="nav nav-tabs">
            <li class="nav-item active show">
                <a class="nav-link" data-toggle="tab" href="#urlPanel">URL</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#htmlPanel">HTML</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#bbPanel">bbCode</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#markdownPanel">Markdown</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#markdownLinkPanel">Markdown with Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#deletePanel">Delete Link</a>
            </li>
        </ul>
        <div id="navTabContent" class="tab-content">
            <div class="tab-pane fade in active show" id="urlPanel">
                <pre style="margin-top: 5px;"><code id="urlCode"></code></pre>
            </div>
            <div class="tab-pane fade" id="htmlPanel">
                <pre style="margin-top: 5px;"><code id="htmlCode"></code></pre>
            </div>
            <div class="tab-pane fade" id="bbPanel">
                <pre style="margin-top: 5px;"><code id="bbCode"></code></pre>
            </div>
            <div class="tab-pane fade" id="markdownPanel">
                <pre style="margin-top: 5px;"><code id="markdown"></code></pre>
            </div>
            <div class="tab-pane fade" id="markdownLinkPanel">
                <pre style="margin-top: 5px;"><code id="markdownLinks"></code></pre>
            </div>
            <div class="tab-pane fade" id="deletePanel">
                <pre style="margin-top: 5px;"><code id="deleteCode"></code></pre>
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
<script src="https://cdn.jsdelivr.net/npm/webuploader@0.1.8/dist/webuploader.min.js"></script>
<script>
    jQuery(function() {
        var $ = jQuery,    // just in case. Make sure it's not an other libaray.

            $wrap = $('#uploader'),

            // 图片容器
            $queue = $('<ul class="filelist"></ul>')
                .appendTo( $wrap.find('.queueList') ),

            // 状态栏，包括进度和控制按钮
            $statusBar = $wrap.find('.statusBar'),

            // 文件总体选择信息。
            $info = $statusBar.find('.info'),

            // 上传按钮
            $upload = $wrap.find('.uploadBtn'),

            // 没选择文件之前的内容。
            $placeHolder = $wrap.find('.placeholder'),

            // 总体进度条
            $progress = $statusBar.find('.progress').hide(),

            // 添加的文件数量
            fileCount = 0,

            // 添加的文件总大小
            fileSize = 0,

            // 优化retina, 在retina下这个值是2
            ratio = window.devicePixelRatio || 1,

            // 缩略图大小
            thumbnailWidth = 110 * ratio,
            thumbnailHeight = 110 * ratio,

            // 可能有pedding, ready, uploading, confirm, done.
            state = 'pedding',

            // 所有文件的进度信息，key为file id
            percentages = {},

            supportTransition = (function(){
                var s = document.createElement('p').style,
                    r = 'transition' in s ||
                        'WebkitTransition' in s ||
                        'MozTransition' in s ||
                        'msTransition' in s ||
                        'OTransition' in s;
                s = null;
                return r;
            })(),

            // WebUploader实例
            uploader;

        if ( !WebUploader.Uploader.support() ) {
            alert( 'Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
            throw new Error( 'WebUploader does not support the browser you are using.' );
        }

        // 实例化
        uploader = WebUploader.create({
            fileVal : 'olaindex_img',
            pick: {
                id: '#filePicker',
                label: '点击选择图片'
            },
            dnd: '#uploader .queueList',
            paste: document.body,
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            },
            // swf文件路径
            swf: 'https://cdn.jsdelivr.net/npm/webuploader@0.1.8/dist/Uploader.swf',
            disableGlobalDnd: true,
            chunked: true,
            formData: {
                '_token' : '{{ csrf_token() }}'
            },
            // server: 'http://webuploader.duapp.com/server/fileupload.php',
            server: '{{ route('image.upload') }}',
            fileNumLimit: 10,
            fileSizeLimit: 5 * 1024 * 1024,    // 5 M
            fileSingleSizeLimit: 1 * 1024 * 1024    // 1 M
        });

        // 添加“添加文件”的按钮，
        uploader.addButton({
            id: '#filePicker2',
            label: '继续添加'
        });

        // 当有文件添加进来时执行，负责view的创建
        function addFile( file ) {
            var $li = $( '<li id="' + file.id + '">' +
                '<p class="title">' + file.name + '</p>' +
                '<p class="imgWrap"></p>'+
                '<p class="progress"><span></span></p>' +
                '</li>' ),

                $btns = $('<div class="file-panel">' +
                    '<span class="cancel">删除</span>' +
                    '<span class="rotateRight">向右旋转</span>' +
                    '<span class="rotateLeft">向左旋转</span></div>').appendTo( $li ),
                $prgress = $li.find('p.progress span'),
                $wrap = $li.find( 'p.imgWrap' ),
                $info = $('<p class="error"></p>'),

                showError = function( code ) {
                    switch( code ) {
                        case 'exceed_size':
                            text = '文件大小超出';
                            break;

                        case 'interrupt':
                            text = '上传暂停';
                            break;

                        default:
                            text = '上传失败，请重试';
                            break;
                    }

                    $info.text( text ).appendTo( $li );
                };

            if ( file.getStatus() === 'invalid' ) {
                showError( file.statusText );
            } else {
                // @todo lazyload
                $wrap.text( '预览中' );
                uploader.makeThumb( file, function( error, src ) {
                    if ( error ) {
                        $wrap.text( '不能预览' );
                        return;
                    }

                    var img = $('<img src="'+src+'">');
                    $wrap.empty().append( img );
                }, thumbnailWidth, thumbnailHeight );

                percentages[ file.id ] = [ file.size, 0 ];
                file.rotation = 0;
            }

            file.on('statuschange', function( cur, prev ) {
                if ( prev === 'progress' ) {
                    $prgress.hide().width(0);
                } else if ( prev === 'queued' ) {
                    $li.off( 'mouseenter mouseleave' );
                    $btns.remove();
                }

                // 成功
                if ( cur === 'error' || cur === 'invalid' ) {
                    console.log( file.statusText );
                    showError( file.statusText );
                    percentages[ file.id ][ 1 ] = 1;
                } else if ( cur === 'interrupt' ) {
                    showError( 'interrupt' );
                } else if ( cur === 'queued' ) {
                    percentages[ file.id ][ 1 ] = 0;
                } else if ( cur === 'progress' ) {
                    $info.remove();
                    $prgress.css('display', 'block');
                } else if ( cur === 'complete' ) {
                    $li.append( '<span class="success"></span>' );
                }

                $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
            });

            $li.on( 'mouseenter', function() {
                $btns.stop().animate({height: 30});
            });

            $li.on( 'mouseleave', function() {
                $btns.stop().animate({height: 0});
            });

            $btns.on( 'click', 'span', function() {
                var index = $(this).index(),
                    deg;

                switch ( index ) {
                    case 0:
                        uploader.removeFile( file );
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if ( supportTransition ) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                    // use jquery animate to rotation
                    // $({
                    //     rotation: rotation
                    // }).animate({
                    //     rotation: file.rotation
                    // }, {
                    //     easing: 'linear',
                    //     step: function( now ) {
                    //         now = now * Math.PI / 180;

                    //         var cos = Math.cos( now ),
                    //             sin = Math.sin( now );

                    //         $wrap.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
                    //     }
                    // });
                }


            });

            $li.appendTo( $queue );
        }

        // 负责view的销毁
        function removeFile( file ) {
            var $li = $('#'+file.id);

            delete percentages[ file.id ];
            updateTotalProgress();
            $li.off().find('.file-panel').off().end().remove();
        }

        function updateTotalProgress() {
            var loaded = 0,
                total = 0,
                spans = $progress.children(),
                percent;

            $.each( percentages, function( k, v ) {
                total += v[ 0 ];
                loaded += v[ 0 ] * v[ 1 ];
            } );

            percent = total ? loaded / total : 0;

            spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
            spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
            updateStatus();
        }

        function updateStatus() {
            var text = '', stats;

            if ( state === 'ready' ) {
                text = '选中' + fileCount + '张图片，共' +
                    WebUploader.formatSize( fileSize ) + '。';
            } else if ( state === 'confirm' ) {
                stats = uploader.getStats();
                if ( stats.uploadFailNum ) {
                    text = '已成功上传' + stats.successNum+ '张照片至XX相册，'+
                        stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                }

            } else {
                stats = uploader.getStats();
                text = '共' + fileCount + '张（' +
                    WebUploader.formatSize( fileSize )  +
                    '），已上传' + stats.successNum + '张';

                if ( stats.uploadFailNum ) {
                    text += '，失败' + stats.uploadFailNum + '张';
                }
            }

            $info.html( text );
        }

        function setState( val ) {
            var file, stats;

            if ( val === state ) {
                return;
            }

            $upload.removeClass( 'state-' + state );
            $upload.addClass( 'state-' + val );
            state = val;

            switch ( state ) {
                case 'pedding':
                    $placeHolder.removeClass( 'element-invisible' );
                    $queue.parent().removeClass('filled');
                    $queue.hide();
                    $statusBar.addClass( 'element-invisible' );
                    uploader.refresh();
                    break;

                case 'ready':
                    $placeHolder.addClass( 'element-invisible' );
                    $( '#filePicker2' ).removeClass( 'element-invisible');
                    $queue.parent().addClass('filled');
                    $queue.show();
                    $statusBar.removeClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'uploading':
                    $( '#filePicker2' ).addClass( 'element-invisible' );
                    $progress.show();
                    $upload.text( '暂停上传' );
                    break;

                case 'paused':
                    $progress.show();
                    $upload.text( '继续上传' );
                    break;

                case 'confirm':
                    $progress.hide();
                    $upload.text( '开始上传' ).addClass( 'disabled' );

                    stats = uploader.getStats();
                    if ( stats.successNum && !stats.uploadFailNum ) {
                        setState( 'finish' );
                        return;
                    }
                    break;
                case 'finish':
                    stats = uploader.getStats();
                    if ( stats.successNum ) {
                        alert( '上传成功' );
                    } else {
                        // 没有成功的图片，重设
                        state = 'done';
                        location.reload();
                    }
                    break;
            }

            updateStatus();
        }

        uploader.onUploadProgress = function( file, percentage ) {
            var $li = $('#'+file.id),
                $percent = $li.find('.progress span');

            $percent.css( 'width', percentage * 100 + '%' );
            percentages[ file.id ][ 1 ] = percentage;
            updateTotalProgress();
        };

        uploader.onFileQueued = function( file ) {
            fileCount++;
            fileSize += file.size;

            if ( fileCount === 1 ) {
                $placeHolder.addClass( 'element-invisible' );
                $statusBar.show();
            }

            addFile( file );
            setState( 'ready' );
            updateTotalProgress();
        };

        uploader.onFileDequeued = function( file ) {
            fileCount--;
            fileSize -= file.size;

            if ( !fileCount ) {
                setState( 'pedding' );
            }

            removeFile( file );
            updateTotalProgress();

        };

        uploader.on( 'all', function( type ) {
            var stats;
            switch( type ) {
                case 'uploadFinished':
                    setState( 'confirm' );
                    break;

                case 'startUpload':
                    setState( 'uploading' );
                    break;

                case 'stopUpload':
                    setState( 'paused' );
                    break;

            }
        });

        uploader.onError = function( code ) {
            alert( 'Eroor: ' + code );
        };

        $upload.on('click', function() {
            if ( $(this).hasClass( 'disabled' ) ) {
                return false;
            }

            if ( state === 'ready' ) {
                uploader.upload();
            } else if ( state === 'paused' ) {
                uploader.upload();
            } else if ( state === 'uploading' ) {
                uploader.stop();
            }
        });

        $info.on( 'click', '.retry', function() {
            uploader.retry();
        } );

        $info.on( 'click', '.ignore', function() {
            alert( 'todo' );
        } );

        $upload.addClass( 'state-' + state );
        updateTotalProgress();
    });
</script>
</body>
</html>
