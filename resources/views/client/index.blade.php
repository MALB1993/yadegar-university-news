@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="hero p-4 p-md-5 mb-4 shadow-sm">
        <h1 class="h2 fw-bold mb-2">بخش دانشجویان</h1>
        <p class="lead mb-3">دسته‌های مورد علاقه‌ی خود را دنبال کنید، خبرهای مهم را ذخیره کنید و زیر خبرها نظر بگذارید.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('news.index') }}" class="btn btn-light btn-sm">همه‌ی اخبار</a>
            <a href="{{ route('news.index', ['followed' => 1]) }}" class="btn btn-outline-light btn-sm">اخبار دسته‌های من</a>
            <a href="{{ route('bookmarks.index') }}" class="btn btn-outline-light btn-sm">نشان‌شده‌ها</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="section-title mb-3">خبرهای دسته‌های من</h2>
            @if ($followedIds->isEmpty())
                <div class="empty-state mb-4">
                    هنوز دسته‌ای را دنبال نمی‌کنید. از فهرست کناری دسته‌ای را انتخاب کنید تا اخبارش اینجا نمایش داده شود.
                </div>
            @else
                <div class="mb-4">
                    @include('news._latest', ['posts' => $feedPosts])
                </div>
            @endif

            @if ($recentPosts->isNotEmpty())
                <h2 class="section-title mb-3">اخیراً خوانده‌شده</h2>
                <div class="list-group shadow-sm rounded-4 mb-4">
                    @foreach ($recentPosts as $post)
                        <a href="{{ route('news.show', $post) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0">
                            <span class="fw-semibold">{{ $post->title }}</span>
                            <span class="badge bg-light text-dark">{{ $post->category->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            <h2 class="section-title mb-3">آخرین اخبار</h2>
            @include('news._latest', ['posts' => $latestPosts])
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top: 90px;">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">دسته‌بندی‌ها</h2>
                    <p class="small text-muted mb-3">با دنبال کردن یک دسته، اخبارش در بخش بالا نمایش داده می‌شود.</p>

                    <div class="d-grid gap-2">
                        @foreach ($categories as $category)
                            @php($isFollowing = $followedIds->contains($category->id))
                            <form method="POST" action="{{ route('categories.follow', $category) }}" class="d-flex gap-2 align-items-center">
                                @csrf
                                <a href="{{ route('news.index', ['category' => $category->id]) }}"
                                   class="flex-grow-1 small text-decoration-none text-body">
                                    {{ $category->name }}
                                    <span class="text-muted">({{ $category->posts_count }})</span>
                                </a>
                                <button type="submit" class="btn btn-sm {{ $isFollowing ? 'btn-success' : 'btn-outline-success' }}">
                                    {{ $isFollowing ? 'دنبال می‌کنم' : 'دنبال کنم' }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
