@php
    /* @var $accounts \App\Models\Account[] */
    $icons = [];
@endphp
@extends('default.layouts.main')
@section('title','OLAINDEX')
@section('content')
    <div class="btn-group mb-3" role="group" aria-label="choiceAccount">
        <button type="button" class="btn btn-primary btn-sm">网盘列表</button>
        <div class="btn-group" role="group">
            <button id="btnChoiceAccount" type="button" class="btn btn-primary btn-sm dropdown-toggle"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnChoiceAccount">
                @foreach($accounts as $account)
                    <a class="dropdown-item"
                       href="{{ route('drive',['hash' => $account['hash_id']]) }}">{{ $account['remark'] }}</a>
                @endforeach
            </div>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="d-none d-md-block d-md-none">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="ri-home-fill"></i> Home</a></li>
            <li class="breadcrumb-item">Second</li>
            <li class="breadcrumb-item">Third</li>
        </ol>
    </nav>
    <div class="card border-light mb-3">
        <div class="card-header"><i class="ri-send-plane-fill"></i> HEAD</div>
        <div class="card-body markdown-body" id="head">
            {!! marked($doc['head']) !!}
        </div>
    </div>
    <div class="card border-light mb-3">
        <div class="card-header"></div>
        <div class="card-body"></div>
    </div>
    <div class="card border-light mb-3">
        <div class="card-header"><i class="ri-bookmark-fill"></i> README</div>
        <div class="card-body markdown-body" id="readme">
            {!! marked($doc['readme']) !!}
        </div>
    </div>
@stop

