<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'دانایار') }}</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">دانایار</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="باز و بسته کردن منو">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                @auth
                    <ul class="navbar-nav me-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active fw-semibold' : '' }}" href="{{ route('home') }}">داشبورد</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('news.*') ? 'active fw-semibold' : '' }}" href="{{ route('news.index') }}">اخبار</a>
                        </li>
                        @can('create', App\Models\Post::class)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('news.create') ? 'active fw-semibold' : '' }}" href="{{ route('news.create') }}">ثبت خبر</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('owner.my-news') ? 'active fw-semibold' : '' }}" href="{{ route('owner.my-news') }}">آمار من</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\Category::class)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active fw-semibold' : '' }}" href="{{ route('categories.index') }}">دسته‌بندی‌ها</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('moderation.*') ? 'active fw-semibold' : '' }}" href="{{ route('moderation.comments') }}">نظارت بر نظرات</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\User::class)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active fw-semibold' : '' }}" href="{{ route('users.index') }}">کاربران</a>
                            </li>
                        @endcan
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                @include('layouts._avatar', ['user' => auth()->user(), 'size' => 32])
                                <span>{{ auth()->user()->fullname }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">پروفایل من</a></li>
                                <li><a class="dropdown-item" href="{{ route('bookmarks.index') }}">نشان‌شده‌ها</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">خروج</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @else
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">ورود</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="btn btn-primary btn-sm ms-lg-2" href="{{ route('register') }}">ثبت‌نام</a>
                            </li>
                        @endif
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    @if (session('status') || session('error'))
        <div class="container pt-4">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
                </div>
            @endif
        </div>
    @endif

    <main class="flex-grow-1 pb-5">
        @yield('content')
    </main>

    <footer class="site-footer py-4">
        <div class="container">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="fw-bold text-primary">دانایار</div>
                    <div class="small text-muted">سامانه‌ی دانشجویی دانشگاه یادگار امام خمینی (ره) شهرری</div>
                </div>
                <div class="col-md-6 text-md-end small text-muted">
                    اخبار، اطلاعیه‌ها و خدمات دانشجویی در یک جا
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
