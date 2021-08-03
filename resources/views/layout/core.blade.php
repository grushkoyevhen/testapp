<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>@yield('title', 'Дефолт')</title>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{$user->name ?? ''}}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{route('logout', [], false)}}">Выйти</a></li>
                            </ul>
                        </li>
                        @endauth
                        @guest
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Гость
                            </a>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="row">
                <div class="col">
                    <h3>Навигация</h3>
                        <ul class="nav nav-pills flex-column">
                            @guest
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('reg.index') ? 'active' : '' }}" aria-current="page" href="{{ route('reg.index', [], false) }}">Регистрация</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('auth.index') ? 'active' : '' }}" aria-current="page" href="{{ route('auth.index', [], false) }}">Авторизация</a>
                            </li>
                            @endguest
                            @auth
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('dashboard.index') ? 'active' : '' }}" aria-current="page" href="{{ route('dashboard.index', [], false) }}">Сводка</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('post.add', 'post.add_failed') ? 'active' : '' }}" aria-current="page" href="{{ route('post.add', [], false) }}">Добавить пост</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (Route::is('post.index', 'post.page', 'post.single', 'post.addcomment_failed')) ? 'active' : '' }}" aria-current="page" href="{{ route('post.index', [], false) }}">Посты</a>
                            </li>
                            @endauth
                        </ul>
                </div>
                <div class="col-9">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
