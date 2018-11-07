@extends('layouts.admin')
@section('title','其它')
@section('content')
    <div class="form-group">
        <label for="action">操作：</label>
        <select class="custom-select" id="action" name="action">
            <option value="">请选择操作</option>
            <option value="move">移动</option>
            <option value="copy">复制</option>
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
                <input type="hidden" name="source_id" id="source_id">
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
                <input type="hidden" name="target_id" id="target_id">
            </div>
            <span class="form-text text-danger">移动文件请填写完整文件地址</span>
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
                if (source === target) {
                    swal('提示', '请确保源地址与目标不一致', 'warning');
                    return false;
                }
                if (action === 'move') {
                    // 移动
                    fetchItemId(source, "source_id");
                    fetchItemId(target, "target_id");
                    setTimeout(function () {
                        let source_id = $("#source_id").val();
                        let target_id = $("#target_id").val();
                        if (!source_id || !target_id) {
                            return false;
                        }
                        move(source_id, target_id);
                    }, 2000);
                } else if (action === 'copy') {
                    // 复制
                    if (source === '' || target === '') {
                        swal('提示', '源地址与目标地址错误', 'warning');
                        return false;
                    }
                    fetchItemId(source, "source_id");
                    fetchItemId(target, "target_id");
                    setTimeout(function () {
                        let source_id = $("#source_id").val();
                        let target_id = $("#target_id").val();
                        if (!source_id || !target_id) {
                            return false;
                        }
                        copy(source_id, target_id);
                    }, 2000);
                } else {
                    swal('提示', '请确保提交内容完整', 'warning');
                    return false;
                }
            });
        });

        function fetchItemId(path, to) {
            axios.post(Config.routes.path2id, {
                path: path,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    $("#" + to).val(res.data.id);
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '源地址无效', 'warning');
                    return false;
                });
        }

        function move(source_id, target_id) {
            axios.post(Config.routes.move, {
                source_id: source_id,
                target_id: target_id,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    console.log(res);
                    swal('移动成功');
                    setTimeout(window.location.reload(), 1000);
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '移动出现问题，请检查文件是否存在', 'warning');
                });
        }

        function copy(source_id, target_id) {
            axios.post(Config.routes.copy, {
                source_id: source_id,
                target_id: target_id,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    console.log(res);
                    swal('复制成功');
                    setTimeout(window.location.reload(), 1000);
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '复制出现问题，请检查文件是否存在', 'warning');
                });
        }

        function createShareLink($id) {
            axios.post(Config.routes.share, {
                id: id,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    console.log(res);
                    setTimeout(window.location.reload(), 1000);
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '操作出现错误', 'warning');
                });
        }

        function deleteShareLink($id) {
            axios.post(Config.routes.delete_share, {
                id: id,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    console.log(res);
                    setTimeout(window.location.reload(), 1000);
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '操作出现问题', 'warning');
                });
        }

        function uploadUrl(path, url) {
            axios.post(Config.routes.upload_url, {
                path: path,
                url: url,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    console.log(res);
                    setTimeout(window.location.reload(), 1000);
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '操作出现问题', 'warning');
                });
        }
    </script>
@stop
