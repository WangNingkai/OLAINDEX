@extends('mdui.layouts.main')
@section('title', '加密访问')
@section('content')
    <div class="mdui-m-t-5">
        <div class="mdui-row">
            <div class="mdui-col-md-6 mdui-col-offset-md-3 mdui-p-a-3">
                <div class="mdui-typo">
                    <div class="mdui-typo-body-2-opacity mdui-text-center">
                        <i class="mdui-icon material-icons">info</i>
                        此文件夹或文件受到保护，您需要提供访问密码才能查看
                    </div>
                </div>
                <form action="{{ route('drive.decrypt') }}" method="post">
                    @csrf
                    <div class="mdui-textfield">
                        <i class="mdui-icon material-icons">https</i>
                        <label class="mdui-textfield-label" for="password">请输入密码</label>
                        <input
                            id="password"
                            name="password"
                            class="mdui-textfield-input"
                            type="password"
                            required
                        />
                        <div class="mdui-textfield-error">密码不能为空</div>
                    </div>
                    <input type="hidden" name="hash" value="{{ $hash }}">
                    <input type="hidden" name="query" value="{{ $item['name'] }}">
                    <input type="hidden" name="redirect" value="{{ $redirect }}">
                    <button type="submit" class="mdui-center mdui-btn mdui-ripple mdui-color-theme">
                        <i class="mdui-icon material-icons">fingerprint</i>
                        确认
                    </button>
                </form>
            </div>
        </div>
    </div>
@stop
