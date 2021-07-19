@extends('layout.core')

@section('title')Посты@stop

@section('content')
    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">id</th>
            <th scope="col">Заголовок</th>
            <th scope="col">Дата</th>
        </tr>
        </thead>
        <tbody>
        @foreach($posts as $post)
        <tr>
            <th scope="row">{{$post->id}}</th>
            <td>{{$post->title}}</td>
            <td>{{$post->created_at}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{$paginator->onEachSide(5)->links('pages')}}
@stop
