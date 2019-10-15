<div class="row">
    <div class="col-sm-6 center-block">
        <form action="{{ 
            Arr::get(request()->route()->action, 'as') == 'admin.google2fa'
                ? auth('admin')->user()->is_tfa ? route('admin.google2fa.unbind') : route('admin.google2fa.bind') 
                : route('admin.google2fa.auth')
            }}" method="POST">
            @csrf
            @if (Arr::get(request()->route()->action, 'as') == 'admin.google2fa' && !auth('admin')->user()->is_tfa)
            <input type="hidden" name="tfa_secret" value="{{ $secret }}" />
            @endif 
            <fieldset>
                <div class="form-group">
                    <label for="exampleInputEmail1">二步验证码</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="code"
                        placeholder="请输入二步验证码..." oldautocomplete="remove" autocomplete="off">
                </div>
                @if (Arr::get(request()->route()->action, 'as') != 'admin.google2fa')
                <div class="form-group row">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> 记住此设备
                            </label>
                        </div>
                    </div>
                </div>
                @endif
                <hr class="c_fd">
                <button type="submit" class="btn btn-primary">提交</button>
            </fieldset>
        </form>
    </div>
</div>