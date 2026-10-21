@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center py-5">
            <h1 class="display-5 fw-bold">دانایار</h1>
            <p class="lead text-muted mt-3">پلتفرمی برای اشتراک‌گذاری مطالب درسی، جزوات و اخبار دانشگاهی</p>
            
            <div class="mt-4">
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-primary btn-lg">ورود به داشبورد</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary me-2">ورود</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary">ثبت‌نام</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection