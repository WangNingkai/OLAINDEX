<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ setting('name','OLAINDEX') }}</title>
    <meta name="keywords" content="OLAINDEX,OneDrive,Index,Microsoft OneDrive,Directory Index"/>
    <meta name="description" content="OLAINDEX,Another OneDrive Directory Index"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.staticfile.org/mdui/0.4.2/css/mdui.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="{{ asset('css/mdui.css') }}">
    @yield('css')
    <script>
        Config = {
            'routes': {
                'upload_image': '{{ route('image.upload') }}'
            },
            '_token': '{{ csrf_token() }}',
        };
    </script>
</head>

<body class="mdui-appbar-with-toolbar mdui-theme-accent-blue mdui-theme-primary-indigo">
<div class="mdui-appbar  mdui-appbar-fixed mdui-color-theme">
    <div class="mdui-toolbar mdui-color-theme mdui-container" style="position: relative">
        <a href="{{ route('home') }}" class="mdui-typo-headline">{{ setting('name') }}</a>
        <div class="mdui-toolbar-spacer"></div>
        @if(in_array(request()->route()->getName(),['home','search']))
            <label class="mdui-switch" mdui-tooltip="{content: '切换视图'}" style="position: absolute;right: 0">
                <i class="mdui-icon material-icons">view_comfy</i> &nbsp;&nbsp;
                <input class="display-type" id="display-type-chk" type="checkbox"/>
                <i class="mdui-switch-icon"></i>
            </label>
        @endif
    </div>
</div>
<div class="mdui-container">
    @yield('content')
</div>
<script src="https://cdn.staticfile.org/mdui/0.4.2/js/mdui.min.js"></script>
<script src="https://cdn.staticfile.org/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/limonte-sweetalert2/7.33.1/sweetalert2.all.min.js"></script>
@if (session()->has('alertMessage'))
    <script>
        $(function () {
            mdui.snackbar({
                message: '{{ session()->pull('alertMessage') }}',
                position: 'right-top'
            });
        });
    </script>
@endif
@yield('js')

</body>

</html>
