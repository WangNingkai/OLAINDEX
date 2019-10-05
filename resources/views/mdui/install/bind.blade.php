@extends('mdui.layouts.main')
@section('content')

    <div class="mdui-container-fluid">
        <div class="mdui-typo">
            <h1>应用安装
                <small>应用ID和密钥</small>
            </h1>
        </div>

        <div class="mdui-typo">
            <h4 class="doc-article-title">
                请确认<code>client_id</code>和<code>client_secret</code>
            </h4>
        </div>
        <div class="mdui-textfield mdui-textfield-floating-label">
            <i class="mdui-icon material-icons">https</i>
            <label class="mdui-textfield-label" for="client_secret">应用 机密(client secret)</label>
            <input type="text" class="mdui-textfield-input" disabled name="client_secret" id="client_secret" required
                   value="{{ setting('client_secret') }}"/>
        </div>
        <br>
        <div class="mdui-textfield mdui-textfield-floating-label">
            <i class="mdui-icon material-icons">apps</i>
            <label class="mdui-textfield-label" for="client_id">应用 ID(client_id)</label>
            <input type="text" class="mdui-textfield-input" disabled name="client_id" id="client_id" required
                   value="{{ setting('client_id') }}"/>
        </div>
        <br>

        <div class="mdui-textfield mdui-textfield-floating-label">
            <i class="mdui-icon material-icons">link</i>
            <label class="mdui-textfield-label" for="redirect_uri">回调地址(redirect_uri)</label>
            <input type="text" name="redirect_uri" disabled id="redirect_uri" class="mdui-textfield-input"
                   value="{{ setting('redirect_uri') }}" required/>
        </div>
        <div class="mdui-textfield">
            <i class="mdui-icon material-icons">info</i>
            <label class="mdui-textfield-label" for="account_type">账户类型(com/cn)</label>
            <input type="text" name="account_type" disabled id="account_type" class="mdui-textfield-input"
                   value="{{ setting('account_type') }}" required/>
        </div>
        <form id="bind-form" action="{{ route('bind') }}" method="POST"
              class="mdui-hidden">
            @csrf
        </form>
        <br>
        <a class="mdui-btn mdui-btn-raised mdui-float-left"
           href="{{ route('reset') }}">返回更改</a>
        <a class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" href="javascript:void(0)"
           onclick="event.preventDefault();document.getElementById('bind-form').submit();">绑定账号</a>
    </div>

@stop
