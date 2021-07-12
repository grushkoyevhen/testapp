@extends('layout.core')

@section('title')Авторизация@stop

@section('content')
    @if($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{route('auth.index')}}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">E-MAIL</label>
            <input type="email" class="form-control" value="{{ old('email') }}" name="email" id="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" name="password" id="password">
        </div>
        <div class="mb-3">
            <input class="form-check-input" type="checkbox" value="{{ (old('remember')) ? old('remember') : '1' }}" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Запомнить?
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Авторизироватся</button>
    </form>
@stop
