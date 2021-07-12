@extends('layout.core')

@section('title')Регистрация@stop

@section('content')
    <form action="{{route('reg.index')}}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Логин</label>
            <input type="text" class="form-control" name="name" id="name">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" name="password" id="password">
        </div>
        <div class="mb-3">
            <label for="password2" class="form-label">Повторить пароль</label>
            <input type="password" class="form-control" name="password2" id="password2">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-MAIL</label>
            <input type="email" class="form-control" name="email" id="email">
        </div>
        <button type="submit" class="btn btn-primary">Зарегистрироватся</button>
    </form>
@stop
