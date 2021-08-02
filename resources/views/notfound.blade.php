@extends('layout.core')

@section('title')Страница не найдена@stop

@section('content')
{{$exception->getMessage()}}
@stop
