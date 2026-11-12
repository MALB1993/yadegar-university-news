@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">اخبار دانشگاه</h1>
        @can('create', App\Models\Post::class)
            <a href="{{ route('news.create') }}" class="btn btn-primary">ثبت خبر جدید</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('news.index') }}" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="جستجو در عنوان خبرها">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">همه‌ی دسته‌بندی‌ها</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($categoryId == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-primary">جستجو</button>
        </div>
    </form>

    @forelse ($posts as $post)
        <div class="card mb-3">
            <div class="card-body">
                <span class="badge bg-secondary mb-2">{{ $post->category->name }}</span>
                <h2 class="h5">
                    <a href="{{ route('news.show', $post) }}" class="text-decoration-none">{{ $post->title }}</a>
                </h2>
                <p class="text-muted small mb-0">{{ $post->user->fullname }} · {{ $post->created_at->format('Y/m/d') }}</p>
            </div>
        </div>
    @empty
        <p class="text-muted">خبری پیدا نشد.</p>
    @endforelse

    {{ $posts->links() }}
</div>
@endsection
