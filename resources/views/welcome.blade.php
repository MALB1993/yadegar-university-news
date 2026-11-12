@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center py-5">
        <h1 class="display-5 fw-bold">دانایار</h1>
        <p class="lead text-muted mt-3">
            سامانه‌ی دانشجویی دانشگاه یادگار امام خمینی (ره) شهرری؛ اخبار، اطلاعیه‌ها و گفت‌وگوی دانشگاهی در یک جا.
        </p>

        <div class="mt-4">
            @auth
                <a href="{{ url('/home') }}" class="btn btn-primary btn-lg">ورود به داشبورد</a>
                <a href="{{ route('news.index') }}" class="btn btn-outline-primary btn-lg ms-2">مشاهده‌ی اخبار</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">ورود</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg ms-2">ثبت‌نام</a>
                @endif
            @endauth
        </div>
    </div>

    <div class="row g-4 pb-5">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">اخبار و اطلاعیه‌ها</h2>
                    <p class="small text-muted mb-0">آخرین خبرها و اطلاعیه‌های دانشگاه را همیشه در دسترس داشته باشید.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">دسته‌بندی موضوعی</h2>
                    <p class="small text-muted mb-0">اخبار بر اساس دسته‌بندی مرتب شده‌اند تا سریع‌تر پیدا کنید.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">نظرات و گفت‌وگو</h2>
                    <p class="small text-muted mb-0">زیر هر خبر نظر بگذارید و با دانشجویان و اساتید گفت‌وگو کنید.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
