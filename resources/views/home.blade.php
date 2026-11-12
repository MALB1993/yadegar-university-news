@extends('layouts.app')

@section('content')
@php($user = auth()->user())
<div class="container">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        @switch($user->role)
                            @case('professor')
                                <span class="badge bg-success">استاد</span>
                                @break
                            @case('employee')
                                <span class="badge bg-info">کارمند</span>
                                @break
                            @default
                                <span class="badge bg-primary">دانشجو</span>
                        @endswitch
                    </div>
                    <h1 class="h5 mb-1">{{ $user->fullname }}</h1>
                    <p class="text-muted small mb-0">
                        شماره دانشجویی/پرسنلی: <span dir="ltr">{{ $user->identification_code }}</span>
                    </p>

                    <hr>

                    <div class="d-grid gap-2">
                        @if ($user->role === 'student')
                            <a href="{{ route('client.index') }}" class="btn btn-primary">بخش دانشجویان</a>
                        @else
                            <a href="{{ route('owner.index') }}" class="btn btn-success">بخش کارمندان و اساتید</a>
                        @endif
                        <a href="{{ route('news.index') }}" class="btn btn-outline-primary">همه‌ی اخبار</a>
                        @can('create', App\Models\Post::class)
                            <a href="{{ route('news.create') }}" class="btn btn-outline-success">ثبت خبر جدید</a>
                        @endcan
                        @can('viewAny', App\Models\Category::class)
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">مدیریت دسته‌بندی‌ها</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @include('news._latest', ['posts' => $latestPosts])
        </div>
    </div>
</div>
@endsection
