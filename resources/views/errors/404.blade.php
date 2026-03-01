@extends('errors.layout')

@section('title', __('Not Found'))
@section('icon')
    <i class="fas fa-map-marked-alt"></i>
@endsection
@section('code', '404')
@section('message', __('messages.page_not_found') ?? 'Sorry, the page you are looking for could not be found.')