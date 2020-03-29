@extends('default.layouts.main')
@section('title', '设置')
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">基础设置</div>
        <div class="card-body">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-basic-tab" data-toggle="tab" href="#config-basic"
                       role="tab"
                       aria-controls="config-basic" aria-selected="true">基础设置</a>
                    <a class="nav-item nav-link" id="nav-show-tab" data-toggle="tab" href="#config-show" role="tab"
                       aria-controls="config-show" aria-selected="false">显示设置</a>
                    <a class="nav-item nav-link" id="nav-disk-tab" data-toggle="tab" href="#config-disk" role="tab"
                       aria-controls="config-disk" aria-selected="false">网盘详情</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="config-basic" role="tabpanel"
                     aria-labelledby="nav-basic-tab">
                    <div class="my-4">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-control-label" for="clientId"><b>client_id</b></label>
                                <input type="text" class="form-control" id="clientId" name="clientId">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="clientSecret"><b>client_secret</b></label>
                                <input type="text" class="form-control" id="clientSecret" name="clientSecret">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-show" role="tabpanel" aria-labelledby="nav-show-tab">
                    <div class="my-4">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-control-label" for="clientId"><b>client_id</b></label>
                                <input type="text" class="form-control" id="clientId" name="clientId">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="clientSecret"><b>client_secret</b></label>
                                <input type="text" class="form-control" id="clientSecret" name="clientSecret">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-disk" role="tabpanel" aria-labelledby="nav-disk-tab">
                    <div class="my-4">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-control-label" for="clientId"><b>client_id</b></label>
                                <input type="text" class="form-control" id="clientId" name="clientId">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="clientSecret"><b>client_secret</b></label>
                                <input type="text" class="form-control" id="clientSecret" name="clientSecret">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @stop
        </div>
    </div>


