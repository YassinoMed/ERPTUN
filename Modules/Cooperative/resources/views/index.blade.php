@extends('cooperative::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('cooperative.name') !!}</p>
@endsection
