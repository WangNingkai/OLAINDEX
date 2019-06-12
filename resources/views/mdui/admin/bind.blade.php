@extends('mdui.layouts.admin')
@section('content')
    <div class="mdui-container-fluid mdui-m-t-2 mdui-m-b-2">

        <div class="mdui-typo">
            <h1>绑定设置</h1>
        </div>
        <form action="" method="post">
            @csrf
            <div class="mdui-textfield mdui-textfield-floating-label">
                <label class="mdui-textfield-label" for="email">已绑定账户</label>
                <input type="text" class="mdui-textfield-input" id="email" name="email"
                       value="{{ setting('account_email') }}" disabled>
            </div>
            <br>

            <button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" type="submit"><i
                    class="mdui-icon material-icons">person</i> 解绑/绑定账户
            </button>
        </form>
    </div>
@stop
