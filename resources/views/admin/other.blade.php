@extends('layouts.admin')
@section('title','其它')
@section('content')
    <div class="form-group">
        <label for="action">操作：</label>
        <select class="custom-select" id="action" name="action">
            <option value="">请选择操作</option>
            <option value="copy">复制</option>
            <option value="move">移动</option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label" for="source">源地址：</label>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">od://</span>
                </div>
                <input type="text" class="form-control" name="source" id="source">
            </div>
            <span class="form-text text-danger">填写完整地址（包括文件名）</span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label" for="target">目标地址：</label>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">od://</span>
                </div>
                <input type="text" class="form-control" name="target" id="target">
            </div>
            <span class="form-text text-danger">移动文件请填写完整文件地址（包括文件名）不填默认为根目录</span>
        </div>
    </div>
    <div class="form-group invisible">
        <p>已完成：<span class="text-danger" id="status">0</span></p>
    </div>
    <button type="submit" id="submit_btn" class="btn btn-primary">提交</button>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/axios@0.18.0/dist/axios.min.js"></script>
    <script>
        $(function () {
            $("#submit_btn").on("click", function () {

                let action = $("#action").val();
                let source = $("#source").val();
                let target = $("#target").val();
                getItemId(source);
                return;
                if (action === '' || source === '' || target === '') {
                    swal('提示', '请确保提交内容完整', 'warning');
                    return false;
                }
                return;


                url = (action === 'copy' ? Config.routes.copy : Config.routes.move);
                axios.post(url, {
                    source: source,
                    target: target,
                    _token: Config._token
                })
                    .then(function (response) {
                        if (response.status === 200) {
                            console.log(response);
                            // url = response.data.data.url;
                            // setInterval("getStatus(url)", 2000);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
        });

        function getStatus(url) {
            axios.get(url)
                .then(function (response) {
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function getItemId(path) {
            axios.post(Config.routes.path2id, {
                path: path,
                _token: Config._token
            })
                .then(function (response) {
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    </script>
@stop
