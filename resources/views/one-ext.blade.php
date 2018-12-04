<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <title>{{ \App\Helpers\Tool::config('name','OLAINDEX') }}</title>
    <link href="https://cdn.bootcss.com/mdui/0.4.1/css/mdui.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
        .mdui-appbar .mdui-toolbar {
            height: 56px;
            font-size: 16px;
        }

        .mdui-toolbar > * {
            padding: 0 6px;
            margin: 0 2px;
            opacity: 0.5;
        }

        .mdui-toolbar > .mdui-typo-headline {
            padding: 0 16px 0 0;
        }

        .mdui-toolbar > i {
            padding: 0;
        }

        .mdui-toolbar > a:hover, a.mdui-typo-headline, a.active {
            opacity: 1;
        }

        .mdui-container {
            max-width: 1200px;
        }

        .mdui-list-item {
            -webkit-transition: none;
            transition: none;
        }

        .mdui-list > .th {
            background-color: initial;
        }

        .mdui-list-item > a {
            width: 100%;
            line-height: 48px
        }

        .mdui-list-item {
            margin: 2px 0;
            padding: 0;
        }

        .mdui-toolbar > a:last-child {
            opacity: 1;
        }

        @media screen and (max-width: 980px) {
            .mdui-list-item .mdui-text-right {
                display: none;
            }

            .mdui-container {
                width: 100% !important;
                margin: 0;
            }

            .mdui-toolbar > *:not(.mdui-switch) {
                display: none;
            }

            .mdui-toolbar > a:last-child, .mdui-toolbar > .mdui-typo-headline, .mdui-toolbar > i:first-child {
                display: block;
            }
        }

        a {
            text-decoration: none;
            color: rgba(0, 0, 0, .87);
        }

        .obj-list .mdui-col {
            padding: 10px;
        }

        .obj-list .col-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .obj-list .mdui-col:hover {
            background-color: #eaeaea;
        }

        .obj-list .col-icon {
            width: 100%;
            height: 100px;
            text-align: center
        }

        .obj-list .col-icon img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>

<body class="mdui-theme-accent-blue mdui-theme-primary-indigo">
<header class="mdui-appbar mdui-color-theme">
    <div class="mdui-toolbar mdui-color-theme mdui-container" style="position: relative">
        <a href="/" class="mdui-typo-headline">{{ \App\Helpers\Tool::config('name') }}</a>
        @if(!blank($path_array))
            @foreach ($path_array as $key => $value)
                @if(end($path_array) === $value && $key === (count($path_array) - 1))
                    <i class="mdui-icon material-icons mdui-icon-dark" style="margin:0;">chevron_right</i>
                    <span>{{ str_limit($value,20)  }}</span>
                @else
                    @if (!blank($value))
                        <i class="mdui-icon material-icons mdui-icon-dark" style="margin:0;">chevron_right</i>
                        <a
                            href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getBreadcrumbUrl($key + 1,$path_array))) }}">{{ str_limit($value,20) }}</a>
                    @endif
                @endif
            @endforeach
        @endif

        <label class="mdui-switch" style="position: absolute;right: 0">
            <img src="https://i.loli.net/2018/12/04/5c05f0c25aebd.png"
                 style="width: 18px;position: relative;top: 5px;right: 5px;" alt="切换">
            <input class="display-type" id="display-type-chk" type="checkbox"/>
            <i class="mdui-switch-icon"></i>
        </label>
    </div>
</header>

<div class="mdui-container">

    <div class="mdui-container-fluid">

        @if (!blank($head))
            <div class="mdui-typo" style="padding: 20px;">
                {!! $head !!}
            </div>
        @endif

        <div class="mdui-row list-detail" style=" display: none;">
            <ul class="mdui-list">
                <li class="mdui-list-item th">
                    <div class="mdui-col-xs-12 mdui-col-sm-7">文件</div>
                    <div class="mdui-col-sm-3 mdui-text-right">修改时间</div>
                    <div class="mdui-col-sm-2 mdui-text-right">大小</div>
                </li>
                @if(!blank($path_array))
                    <li class="mdui-list-item mdui-ripple">
                        <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl(\App\Helpers\Tool::getParentUrl($path_array))) }}">
                            <div class="mdui-col-xs-12 mdui-col-sm-7">
                                <i class="mdui-icon material-icons">arrow_upward</i>
                                返回上一层
                            </div>
                            <div class="mdui-col-sm-3 mdui-text-right"></div>
                            <div class="mdui-col-sm-2 mdui-text-right"></div>
                        </a>
                    </li>
                @endif

                @foreach($items as $item)
                    @if(array_has($item,'folder'))
                        <li class="mdui-list-item mdui-ripple">
                            <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}">
                                <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                    <i class="mdui-icon material-icons">folder_open</i>
                                    {{ $item['name'] }}
                                </div>
                                <div
                                    class="mdui-col-sm-3 mdui-text-right">{{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}</div>
                                <div
                                    class="mdui-col-sm-2 mdui-text-right">{{ array_has($item,'folder')? '-' : \App\Helpers\Tool::convertSize($item['size']) }}</div>
                            </a>
                        </li>
                    @else
                        <li class="mdui-list-item file mdui-ripple">
                            <a href="{{ route('show',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}"
                               target="_blank">
                                <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                    <i class="mdui-icon material-icons">{{ \App\Helpers\Tool::fileIcon($item['ext']) }}</i>
                                    {{ $item['name'] }}
                                </div>
                                <div
                                    class="mdui-col-sm-3 mdui-text-right">{{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}</div>
                                <div
                                    class="mdui-col-sm-2 mdui-text-right">{{ array_has($item,'folder')? '-' : \App\Helpers\Tool::convertSize($item['size']) }}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div style="margin-top: 20px; display: none;"
             class="obj-list mdui-row-xs-1 mdui-row-sm-4 mdui-row-md-5 mdui-row-lg-6 mdui-row-xl-7 mdui-grid-list">
            @foreach($items as $item)
                @if(array_has($item,'folder'))
                    <div class="mdui-col">
                        <a href="{{ route('home',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}">
                            <div class="col-icon">
                                <img
                                    src="https://static2.sharepointonline.com/files/fabric/office-ui-fabric-react-assets/foldericons/folder-large_frontplate_nopreview.svg"
                                    alt="">
                            </div>
                            <div class="col-detail" style="text-align: center">
                                <div class="col-title">
                                    {{ $item['name'] }}
                                </div>
                                <br/>
                                <div class="col-date">
                                    {{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}
                                </div>
                            </div>
                        </a>
                    </div>
                @else
                    <div class="mdui-col file">
                        <a target="_blank"
                           href="{{ route('show',\App\Helpers\Tool::getEncodeUrl($origin_path ? $origin_path.'/'.$item['name'] : $item['name'])) }}">
                            <div class="col-icon">
                                @if(in_array($item['ext'],['bmp','jpg','jpeg','png','gif']))
                                    <img class="lazy"
                                         data-original="{{ route('thumb',['id'=>$item['id'],'size'=>'small']) }}"
                                         src="https://i.loli.net/2018/12/04/5c0625755d6ce.gif" alt="">
                                @else
                                    <img style="height: 80%;"
                                         src="https://i.loli.net/2018/12/04/5c05f4c540cce.png" alt="">
                                @endif
                            </div>
                            <div class="col-detail" style="text-align: center">
                                <div class="col-title" title="{{ $item['name'] }}">
                                    {{ $item['name'] }}
                                </div>
                                <br/>
                                <div class="col-date">
                                    {{ date('M m H:i',strtotime($item['lastModifiedDateTime'])) }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
        {{ $items->links('page-ext') }}

        @if (!blank($readme))
            <div class="mdui-typo mdui-shadow-3" style="padding: 20px;margin: 20px;">
                <div class="mdui-chip">
                    <span class="mdui-chip-icon"><i class="mdui-icon material-icons">face</i></span>
                    <span class="mdui-chip-title">README.md</span>
                </div>
                {!! $readme !!}
            </div>
        @endif
    </div>
    <script src="https://cdn.bootcss.com/mdui/0.4.1/js/mdui.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/store.js/1.3.20/store.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery_lazyload/1.9.7/jquery.lazyload.min.js"></script>
    <script>
        $(function () {
            let display_type = store.get('display_type'); // 读取 cookie
            if (display_type !== 'table') {
                $('.list-detail').hide();
                $('.obj-list').show();
                $('img.lazy').lazyload();
                $('#display-type-chk').attr('checked', true);
            } else {
                $('.list-detail').show();
                $('.obj-list').hide();
            }
            $('.display-type').on('click', function () {
                if (display_type !== 'table') {
                    store.set('display_type', 'table');
                } else {
                    store.set('display_type', 'list');
                }
                window.location.reload();
            });
            $('img.lazy').lazyload();
        });
    </script>
</div>
</body>

</html>
