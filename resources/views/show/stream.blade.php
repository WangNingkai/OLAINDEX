@extends('layouts.main')
@section('title',$file['name'])
@section('content')
    <div class="card border-light mb-3">
        <div class="card-header">
            {{ $file['name'] }}
        </div>
        <div class="card-body">
            <div class="text-center"><a href="{{ $file['path'] }}" class="btn btn-success"><i class="fa fa-download"></i> 下载</a></div>
            <hr>
            <div>
                <pre>
                    <code class="language-markup">{{ $file['content'] }}</code>
                </pre>
            </div>
        </div>
    </div>
@stop
