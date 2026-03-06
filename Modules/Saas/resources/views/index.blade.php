@extends('saas::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('saas.name') !!}</p>
@endsection
