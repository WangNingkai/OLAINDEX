@extends('mdui.layouts.main')
@section('content')
    <div class="mdui-container-fluid">
        <div class="mdui-col-md-6 mdui-col-offset-md-3">
            <h4 class="mdui-typo-headline-opacity">登录管理</h4>
            <form action="" method="post">
                @csrf
                <div class="mdui-textfield mdui-textfield-floating-label">
                    <i class="mdui-icon material-icons">https</i>
                    <label class="mdui-textfield-label" for="password">请输入密码</label>
                    <input name="password" class="mdui-textfield-input" type="password" id="password" required/>
                </div>
                <br>
                <button type="submit" class="mdui-center mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme">
                    <i class="mdui-icon material-icons">fingerprint</i>
                    登陆
                </button>
            </form>
        </div>

    </div>
@stop
