@if (session()->has('alertMessage'))
    <div class="alert alert-dismissible alert-{{ session()->pull('alertType')}}">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <p>{{ session()->pull('alertMessage') }}</p>
    </div>
@endif
