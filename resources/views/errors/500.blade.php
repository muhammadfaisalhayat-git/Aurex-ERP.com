@extends('errors.layout')

@section('title', __('Server Error'))
@section('icon')
    <i class="fas fa-exclamation-triangle"></i>
@endsection
@section('code', '500')
@section('message', __('messages.server_error') ?? 'Whoops, something went wrong on our servers.')