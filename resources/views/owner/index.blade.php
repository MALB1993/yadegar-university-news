@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h4 mb-4">بخش کارمندان و اساتید</h1>

    <div class="row g-4">
        <div class="col-lg-8">
            @include('news._latest', ['posts' => $latestPosts])
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body d-grid gap-2">
                    <h2 class="h6 mb-2">کارهای شما</h2>
                    <a href="{{ route('news.index') }}" class="btn btn-outline-primary">همه‌ی اخبار</a>
                    @can('create', App\Models\Post::class)
                        <a href="{{ route('news.create') }}" class="btn btn-success">ثبت خبر جدید</a>
                    @endcan
                    @can('viewAny', App\Models\Category::class)
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">مدیریت دسته‌بندی‌ها</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
