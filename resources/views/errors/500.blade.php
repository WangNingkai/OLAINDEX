@extends('errors::illustrated-layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __($exception->getMessage() ?: 'Server Error'))
