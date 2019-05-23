<div class="alert alert-dismissible alert-primary">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>验证错误 !</strong> 
    @foreach ($errors->all() as $error)
    <p>{{ $error }}</p>
    @endforeach
</div>
