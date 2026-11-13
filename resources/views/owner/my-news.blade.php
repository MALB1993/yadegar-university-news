@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">آمار خبرهای من</h1>
            <p class="text-muted mb-0">بازدید، نظرات و وضعیت خبرهایی که خودتان نوشته‌اید.</p>
        </div>
        <a href="{{ route('news.create') }}" class="btn btn-primary">+ ثبت خبر جدید</a>
    </div>

    <div class="row row-cols-2 row-cols-lg-5 g-3 mb-4">
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">کل خبرها</div>
                    <div class="stat-value text-primary">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">مجموع بازدید</div>
                    <div class="stat-value text-success">{{ $stats['views'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">مجموع نظرات</div>
                    <div class="stat-value text-warning">{{ $stats['comments'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">پیش‌نویس</div>
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
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <h2 class="section-title mb-3">پربازدیدترین خبرها</h2>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    @forelse ($topPosts as $post)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <a href="{{ route('news.show', $post) }}" class="text-body text-decoration-none fw-semibold text-truncate me-2">{{ $post->title }}</a>
                                <span class="text-muted flex-shrink-0">{{ $post->views }} بازدید</span>
                            </div>
                            <div class="progress" role="progressbar" aria-label="میزان بازدید {{ $post->title }}"
                                 aria-valuenow="{{ $post->views }}" aria-valuemin="0" aria-valuemax="{{ $maxViews }}" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ round($post->views / $maxViews * 100) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">هنوز خبری نوشته‌اید.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <h2 class="section-title mb-3">همه‌ی خبرهای من</h2>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>عنوان</th>
                                <th>وضعیت</th>
                                <th class="text-center">بازدید</th>
                                <th class="text-center">نظر</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($posts as $post)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $post->title }}</div>
                                        <div class="small text-muted">{{ $post->category->name }} · {{ $post->created_at->format('Y/m/d') }}</div>
                                    </td>
                                    <td>
                                        @if ($post->status === 'draft')
                                            <span class="badge bg-dark">پیش‌نویس</span>
                                        @elseif ($post->isScheduled())
                                            <span class="badge bg-info text-dark">زمان‌بندی: {{ $post->published_at->format('Y/m/d H:i') }}</span>
                                        @else
                                            <span class="badge bg-success">منتشر شده</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $post->views }}</td>
                                    <td class="text-center">{{ $post->comments_count }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('news.edit', $post) }}" class="btn btn-sm btn-outline-primary">ویرایش</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">هنوز خبری نوشته‌اید.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
