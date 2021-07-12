@extends('layout.core')

@section('title')Пост@stop

@section('content')
        <div class="row">
            <div class="col">
                <h3>{{$post->title}}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col">
                {{$post->body}}
            </div>
        </div>
@stop
