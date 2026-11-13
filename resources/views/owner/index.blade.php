@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="hero p-4 p-md-5 mb-4 shadow-sm">
        <h1 class="h2 fw-bold mb-2">{{ $isEmployee ? 'بخش مدیریت کارمندان' : 'بخش اساتید' }}</h1>
        <p class="lead mb-3">
            @if ($isEmployee)
                اخبار را منتشر و زمان‌بندی کنید، نظرات را بررسی کنید و دسترسی کاربران را تنظیم کنید.
            @else
                اخبار خود را منتشر، زمان‌بندی و بازدیدشان را پیگیری کنید.
            @endif
        </p>
        <div class="d-flex flex-wrap gap-2">
            @can('create', App\Models\Post::class)
                <a href="{{ route('news.create') }}" class="btn btn-light btn-sm">+ ثبت خبر جدید</a>
                <a href="{{ route('owner.my-news') }}" class="btn btn-outline-light btn-sm">آمار خبرهای من</a>
            @endcan
            <a href="{{ route('news.index') }}" class="btn btn-outline-light btn-sm">همه‌ی اخبار</a>
            @can('viewAny', App\Models\Category::class)
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-sm">دسته‌بندی‌ها</a>
                <a href="{{ route('moderation.comments') }}" class="btn btn-outline-light btn-sm">نظارت بر نظرات</a>
            @endcan
            @can('viewAny', App\Models\User::class)
                <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm">کاربران</a>
            @endcan
        </div>
    </div>

    <div class="row row-cols-2 row-cols-lg-5 g-3 mb-4">
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">تعداد اخبار</div>
                    <div class="stat-value text-primary">{{ $stats['posts'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">پیش‌نویس‌ها</div>
                    <div class="stat-value text-dark">{{ $stats['drafts'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">زمان‌بندی‌شده</div>
                    <div class="stat-value text-info">{{ $stats['scheduled'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">کاربران</div>
                    <div class="stat-value text-success">{{ $stats['users'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">نظرات</div>
                    <div class="stat-value text-warning">{{ $stats['comments'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <h2 class="section-title mb-3">گزارش دسته‌بندی‌ها</h2>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>دسته‌بندی</th>
                                <th class="text-center">تعداد خبر</th>
                                <th class="text-center">مجموع بازدید</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categoryReport as $category)
                                <tr>
                                    <td>
                                        <a href="{{ route('news.index', ['category' => $category->id]) }}" class="text-decoration-none">{{ $category->name }}</a>
                                    </td>
                                    <td class="text-center">{{ $category->posts_count }}</td>
                                    <td class="text-center">{{ (int) $category->posts_sum_views }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">دسته‌بندی‌ای ثبت نشده است.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h2 class="section-title mb-3">آخرین اخبار</h2>
            <div class="d-grid gap-3">
                @forelse ($latestPosts as $post)
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center gap-3">
                            <div>
                                <a href="{{ route('news.show', $post) }}" class="fw-semibold text-body text-decoration-none stretched-link">{{ $post->title }}</a>
                                <div class="small text-muted">{{ $post->user->fullname }} · {{ $post->category->name }}</div>
                            </div>
                            @if ($post->status === 'draft')
                                <span class="badge bg-dark">پیش‌نویس</span>
                            @elseif ($post->isScheduled())
                                <span class="badge bg-info text-dark">زمان‌بندی</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">هنوز خبری ثبت نشده است.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
