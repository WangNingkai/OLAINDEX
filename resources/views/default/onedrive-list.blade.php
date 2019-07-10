@extends ('default.layouts.main')
@section('title', getAdminConfig('name'))
@section('css')
<link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/blueimp-gallery/2.33.0/css/blueimp-gallery-indicator.min.css">
<link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/blueimp-gallery/2.33.0/css/blueimp-gallery.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('css/normalize.css') }}" />
{{--  <link rel="stylesheet" type="text/css" href="{{ asset('css/default.css') }}"> --}}
<link rel="stylesheet" type="text/css" href="{{ asset('css/fsbanner.css') }}">
@stop
@section('js')
<script type="text/javascript" src="{{ asset('js/fsbanner.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $('.ex[name=2] .fsbanner').fsBanner({
			trigger:'mouse'
        });

        {{--  $('.fsbanner > div').hover(
            function () {
                $(this).children('.join-box').css({
                    'display': 'flex'
                })
            },
            function () {
                $(this).children('.join-box').css({
                    'display': 'none'
                })
            }
        );  --}}
    });
</script>
@stop
@section('content')
<div class="jumbotron">
    <h1 class="display-3">选择 OneDrive </h1>
    <hr class="my-4">
    <p class="lead">
        <article class="htmleaf-container">
            <div class='ex' name='2'>
                <div class='fsbanner'>
                    @foreach ($oneDrives as $oneDrive)
                    <div style='background-image:url({{ asset("img/3.jpg") }})'>
                        <span class='name'>{{ $oneDrive->name }}</span>
                        <span class="join-box fsbanner-button clockwise fsbanner-both">
                            <a href="{{ route('home', ['oneDrive' => $oneDrive->id]) }}" class="join"> 进&nbsp;&nbsp;入 </a>
                            <p class="fsbanner-inner"></p>
                        </span>
                    </div>   
                    @endforeach
                </div>
            </div>
        </article>
    </p>
</div>
@stop