@extends('mdui.layouts.main')
@section('content')
    <div class="mdui-container-fluid">
        <div class="mdui-col-md-6 mdui-col-offset-md-3">
            <h4 class="mdui-typo-headline-opacity"><span class="mdui-center" style="text-align: center">登录后台</span></h4>
            <form action="" method="post">
                @csrf
                <div class="mdui-textfield mdui-textfield-floating-label">
                    <i class="mdui-icon material-icons">face</i>
                    <label class="mdui-textfield-label" for="name">用户名</label>
                    <input name="name" class="mdui-textfield-input" type="text" id="name" value="{{ old('name') }}"
                           required/>
                    @if($errors->has('name'))
                        <div
                            class="mdui-textfield-helper">{{ $errors->first('name') }}</div>  @endif
                </div>
                <div class="mdui-textfield mdui-textfield-floating-label">
                    <i class="mdui-icon material-icons">https</i>
                    <label class="mdui-textfield-label" for="password">请输入密码</label>
                    <input name="password" class="mdui-textfield-input" type="password" id="password" required/>
                    @if($errors->has('password'))
                        <div
                            class="mdui-textfield-helper">{{ $errors->first('password') }}</div>  @endif
                </div>
                <br>
                <button type="submit" class="mdui-center mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme">
                    <i class="mdui-icon material-icons">fingerprint</i>
                    登录
                </button>
            </form>
        </div>

    </div>
@stop
