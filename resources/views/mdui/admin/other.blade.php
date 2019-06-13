@extends('mdui.layouts.admin')
@section('js')
    <script src="https://cdn.staticfile.org/axios/0.19.0/axios.js"></script>
    <script>
        $(function () {
            $("#submit_btn").on("click", function () {
                swal('提示', '请稍等...', 'info');
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
                } else if (action === 'upload_url') {
                    upload_url(target, source);
                } else if (action === 'create_share') {
                    fetchItemId(source, "source_id");
                    setTimeout(function () {
                        let source_id = $("#source_id").val();
                        if (!source_id) {
                            return false;
                        }
                        create_share(source_id);
                    }, 2000);
                } else if (action === 'delete_share') {
                    fetchItemId(source, "source_id");
                    setTimeout(function () {
                        let source_id = $("#source_id").val();
                        if (!source_id) {
                            return false;
                        }
                        delete_share(source_id);
                    }, 2000);
                } else {
                    swal('提示', '暂不支持', 'warning');
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
                    swal({
                        title: "操作成功",
                        text: "文件已移动",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "确定",
                    }).then((result) => {
                        if (result.value) {
                            window.location.reload();
                        }
                    });
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
                    let redirect = res.data.redirect;
                    swal({
                        title: "操作成功",
                        html:
                            '文件在后台复制，查看进度点击' +
                            '<a href="' + redirect + '" target="_blank">链接</a>',
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "确定",
                    }).then((result) => {
                        if (result.value) {
                            window.location.reload();
                        }
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '复制出现问题，请检查文件是否存在', 'warning');
                });
        }

        function upload_url(path, url) {
            axios.post(Config.routes.upload_url, {
                path: path,
                url: url,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    console.log(res);
                    let redirect = res.data.redirect;
                    swal({
                        title: "操作成功",
                        html:
                            '文件在后台下载，查看进度点击' +
                            '<a href="' + redirect + '" target="_blank">链接</a>',
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "确定",
                    }).then((result) => {
                        if (result.value) {
                            window.location.reload();
                        }
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '出现问题，请检查文件链接是否有效', 'warning');
                });
        }

        function create_share(id) {
            axios.post(Config.routes.share, {
                id: id,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    console.log(res);
                    let url = res.data.redirect;
                    $("#target").val(url);
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '出现问题，请检查文件地址是否有效', 'warning');
                    return false;
                });
        }

        function delete_share(id) {
            axios.post(Config.routes.delete_share, {
                id: id,
                _token: Config._token
            })
                .then(function (response) {
                    let res = response.data;
                    console.log(res);
                    swal({
                        title: "操作成功",
                        text: "已删除",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "确定",
                    }).then((result) => {
                        if (result.value) {
                            window.location.reload();
                        }
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    swal('提示', '出现问题，请检查操作是否有效', 'warning');
                    return false;
                });
        }
    </script>
@stop
@section('content')
    <div class="mdui-container-fluid mdui-m-t-2 mdui-m-b-2">

        <div class="mdui-typo">
            <h1>其他操作</h1>
        </div>
        <label for="action" class="mdui-textfield-label">操作</label> &nbsp; &nbsp;
        <br>
        <select name="action" id="action" class="mdui-select" mdui-select="{position: 'bottom'}">
            <option value="">请选择操作</option>
            <option value="move">移动</option>
            <option value="copy">复制</option>
            <option value="upload_url" class="mdui-text-color-red">离线下载(实验性)</option>
            <option value="create_share">创建分享下载直链</option>
            <option value="delete_share">删除分享下载直链</option>
        </select>
        <br>
        <div class="mdui-textfield mdui-textfield-floating-label">
            <label class="mdui-textfield-label" for="source">源路径</label>
            <input type="text" class="mdui-textfield-input" id="source" name="source">
            <input type="hidden" name="source_id" id="source_id">
            <div class="mdui-textfield-helper">移动、复制和创建分享操作，请填写完整OneDrive地址（包括文件/文件夹名），离线下载，填写完整的下载地址。</div>

        </div>
        <br>
        <div class="mdui-textfield mdui-textfield-floating-label">
            <label class="mdui-textfield-label" for="target">目标路径</label>
            <input type="text" class="mdui-textfield-input" id="target" name="target">
            <input type="hidden" name="target_id" id="target_id">
            <div class="mdui-textfield-helper">移动复制操作时，请填写目标文件或文件夹的完整地址（包括文件/文件夹名），离线下载操作请填写完整的下载路径（包括文件/文件夹名）；创建、删除分享时可不填。</div>
        </div>

        <br>
        <button id="submit_btn" class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" type="button">
            <i class="mdui-icon material-icons">check</i> 提交
        </button>
    </div>
@stop
