@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="hero p-4 p-md-5 mb-4 shadow-sm">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h1 class="h2 fw-bold mb-2">اخبار دانشگاه</h1>
                <p class="lead mb-3">آخرین اطلاعیه‌ها، برنامه‌ها و رویدادهای دانشگاه را اینجا پیدا کنید.</p>
                @can('create', App\Models\Post::class)
                    <a href="{{ route('news.create') }}" class="btn btn-light btn-sm">+ ثبت خبر جدید</a>
                @endcan
            </div>
            <div class="col-lg-5">
                <form method="GET" action="{{ route('news.index') }}" class="input-group input-group-lg">
                    @if ($categoryId)
                        <input type="hidden" name="category" value="{{ $categoryId }}">
                    @endif
                    @if ($followedOnly)
                        <input type="hidden" name="followed" value="1">
                    @endif
                    <input type="search" name="q" value="{{ $search }}" class="form-control border-0"
                           placeholder="جستجو در عنوان خبرها…" aria-label="جستجو">
                    <button class="btn btn-light px-4" type="submit">جستجو</button>
                </form>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('news.index') }}"
           class="btn btn-sm category-chip {{ $categoryId || $followedOnly ? 'btn-outline-primary' : 'btn-primary' }}">همه</a>
        @auth
            <a href="{{ route('news.index', ['followed' => 1]) }}"
               class="btn btn-sm category-chip {{ $followedOnly ? 'btn-success' : 'btn-outline-success' }}">
                دسته‌های من{{ $hasFollowedCategories ? '' : ' (هنوز خالی است)' }}
            </a>
        @endauth
        @foreach ($categories as $category)
            <a href="{{ route('news.index', ['category' => $category->id]) }}"
               class="btn btn-sm category-chip {{ $categoryId === $category->id ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $category->name }}
                <span class="badge bg-light text-dark ms-1">{{ $category->posts_count }}</span>
            </a>
        @endforeach
    </div>

    @if ($pinnedPosts->isNotEmpty())
        <h2 class="section-title mb-3">خبرهای مهم</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
            @foreach ($pinnedPosts as $post)
                <div class="col">@include('news._card', ['post' => $post])</div>
            @endforeach
        </div>
    @endif

    <h2 class="section-title mb-3">
        @if ($search !== '')
            نتایج جستجو
        @elseif ($followedOnly)
            اخبار دسته‌های دنبال‌شده
        @else
            آخرین اخبار
        @endif
    </h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse ($posts as $post)
            <div class="col">@include('news._card', ['post' => $post])</div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    @if ($search !== '')
                        خبری با عنوان «{{ $search }}» پیدا نشد.
                    @elseif ($followedOnly && ! $hasFollowedCategories)
                        هنوز دسته‌ای را دنبال نکرده‌اید. از صفحه‌ی خبر یا بخش دانشجویان می‌توانید دسته‌ها را دنبال کنید.
                    @elseif ($followedOnly)
                        در دسته‌های دنبال‌شده‌ی شما هنوز خبری منتشر نشده است.
                    @else
                        در این دسته‌بندی هنوز خبری منتشر نشده است.
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>
@endsection
