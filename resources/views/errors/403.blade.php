@extends('errors.layout')

@section('title', __('Forbidden'))
@section('icon')
    <i class="fas fa-lock"></i>
@endsection
@section('code', '403')
@section('message', $exception->getMessage() ?: __('messages.forbidden') ?: 'Access to this resource is denied.')