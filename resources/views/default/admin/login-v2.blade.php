<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ __('messages.admin_login') }}</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://cdn.bootcss.com/animate.css/3.5.2/animate.min.css" rel="stylesheet">
	<link href="https://cdn.bootcss.com/hamburgers/1.1.3/hamburgers.min.css" rel="stylesheet">
	<link href="https://cdn.bootcss.com/select2/4.0.3/css/select2.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="{{ asset('img/img-01.png') }}" alt="IMG">
				</div>
				<form class="login100-form validate-form" action="{{ route('admin.login') }}" method="POST">
					@csrf
					<span class="login100-form-title">
						{{ __('messages.admin_login') }}
					</span>
					<div class="wrap-input100 validate-input" data-validate="请输入账号">
						<input class="input100" type="text" name="email" placeholder="账号" value="{{ old('email') }}">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>
					@if ($errors->has('email'))
						<div class="invalid-feedback" style="display:block; margin-bottom: 10px">{{ $errors->first('email') }}</div>
					@endif
					<div class="wrap-input100 validate-input" data-validate="请输入密码">
						<input class="input100" type="password" name="password" placeholder="{{ __('Password') }}">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					@if ($errors->has('password'))
						<div class="invalid-feedback">{{ $errors->first('password') }}</div>
					@endif
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							{{ __('Login') }}
						</button>
					</div>
					<div class="text-center p-t-136">
						<a class="txt2" href="{{ route('onedrive.list') }}">
							首页
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="https://cdn.bootcss.com/select2/4.0.3/js/select2.min.js"></script>
	<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>