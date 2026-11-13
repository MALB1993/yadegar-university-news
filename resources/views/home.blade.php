@extends('layouts.app')

@section('content')
@php($user = auth()->user())
<div class="container py-4">
    <div class="hero p-4 p-md-5 mb-4 shadow-sm">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            @include('layouts._avatar', ['user' => $user, 'size' => 64])
            <div>
                <p class="mb-1 opacity-75">خوش آمدید</p>
                <h1 class="h3 fw-bold mb-1">{{ $user->fullname }}</h1>
                <span class="badge bg-white text-dark">{{ $user->roleLabel }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">نشان‌شده‌ها</div>
                    <div class="stat-value text-primary">{{ $bookmarkCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">نظرات من</div>
                    <div class="stat-value text-success">{{ $commentCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">آخرین اخبار</h2>
                <a href="{{ route('news.index') }}" class="small">همه‌ی اخبار</a>
            </div>
            @include('news._latest', ['posts' => $latestPosts])
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-grid gap-2">
                    <h2 class="h6 mb-2">دسترسی سریع</h2>

                    @if ($user->role === 'student')
                        <a href="{{ route('client.index') }}" class="btn btn-primary">بخش دانشجویان</a>
                    @else
                        <a href="{{ route('owner.index') }}" class="btn btn-success">بخش کارمندان و اساتید</a>
                    @endif

                    <a href="{{ route('news.index') }}" class="btn btn-outline-primary">همه‌ی اخبار</a>
                    <a href="{{ route('bookmarks.index') }}" class="btn btn-outline-warning">نشان‌شده‌ها</a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">ویرایش پروفایل</a>

                    @can('create', App\Models\Post::class)
                        <a href="{{ route('news.create') }}" class="btn btn-outline-success">ثبت خبر جدید</a>
                    @endcan
                    @can('viewAny', App\Models\Category::class)
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">مدیریت دسته‌بندی‌ها</a>
                    @endcan
                    @can('viewAny', App\Models\User::class)
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">مدیریت کاربران</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
