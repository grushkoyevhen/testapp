@extends('layout.core')

@section('title')Добавить пост@stop

@section('content')

    @if($errors->kappa->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach($errors->kappa->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{route('post.add')}}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control" name="title" id="title">
        </div>
        <div class="mb-3">
            <label for="text" class="form-label">Текст</label>
            <textarea type="text" class="form-control" name="text" id="text"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Добавить</button>
    </form>
@stop
