@extends('default.layouts.main')
@section('title','OLAINDEX')
@section('content')
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
        </div>
    </div>
    <div class="card border-light mb-3">
        <div class="card-header"></div>
        <div class="card-body"></div>
    </div>
    <div class="card border-light mb-3">
        <div class="card-header"><i class="ri-bookmark-fill"></i> README</div>
        <div class="card-body markdown-body" id="readme">
        </div>
    </div>
@stop
