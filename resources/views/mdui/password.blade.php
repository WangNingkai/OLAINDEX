@extends('mdui.layouts.main')
@section('js')
    <script src="https://cdn.staticfile.org/marked/0.6.2/marked.min.js"></script>
    <script>
        $(function(){
            @if (!blank(setting('encrypt_tip')))
            document.getElementById('head').innerHTML = marked(`{!! setting('encrypt_tip') !!}`);
            @endif
        })
    </script>
@stop
@section('content')
    <div class="mdui-container-fluid">
        @if (!blank(setting('encrypt_tip')))
            <div class="mdui-typo mdui-shadow-2 mdui-p-a-3 mdui-m-a-2">
                <div class="mdui-chip mdui-m-a-2">
                    <span class="mdui-chip-icon"><i class="mdui-icon material-icons">face</i></span>
                    <span class="mdui-chip-title">Info</span>
                </div>
                <div id="head"></div>
            </div>
        @endif
        <div class="mdui-col-md-6 mdui-col-offset-md-3 mdui-p-a-3">
            <form action="{{ route('password') }}" method="post">
                @csrf
                <div class="mdui-textfield mdui-textfield-floating-label">
                    <i class="mdui-icon material-icons">https</i>
                    <label class="mdui-textfield-label" for="password">此文件夹或文件受到保护，您需要提供访问密码才能查看</label>
                    <input name="password" class="mdui-textfield-input" type="password" id="password" required/>
                    <input type="hidden" name="encryptKey" value="{{ encrypt($encryptKey) }}">
                    <input type="hidden" name="route" value="{{ encrypt($route) }}">
                    <input type="hidden" name="requestPath" value="{{ encrypt($requestPath) }}">
                </div>
                <br>
                <button type="submit" class="mdui-center mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme">
                    <i class="mdui-icon material-icons">fingerprint</i>
                    查看
                </button>

            </form>
        </div>

    </div>
@stop
