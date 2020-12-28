@extends('mdui.layouts.main')
@section('content')
    <div class="mdui-m-t-5">
        <div class="mdui-row mdui-m-t-3">
            <div class="mdui-col-xs-10 mdui-col-offset-xs-1">
                <div class="mdui-card">
                    <div class="mdui-card-media">
                        <img src="https://i.loli.net/2018/12/07/5c09d18d9b255.png" alt=""/>
                        <div class="mdui-card-media-covered">
                            <div class="mdui-card-primary">
                                <div class="mdui-card-primary-title">OLAINDEX</div>
                                <div class="mdui-card-primary-subtitle">✨ Another OneDrive Directory Index.</div>
                            </div>
                        </div>
                    </div>
                    <div class="mdui-card-actions">
                        <a class="mdui-btn mdui-ripple" href="{{ route('home') }}"><i class="mdui-icon material-icons">subdirectory_arrow_left</i>
                            返回首页</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

