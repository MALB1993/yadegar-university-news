<article class="card h-100 border-0 shadow-sm news-card">
    <div class="news-cover cover-{{ $post->category->id % 5 }}">
        <span class="badge bg-white text-dark">{{ $post->category->name }}</span>
        @if ($post->is_pinned)
            <span class="badge bg-warning text-dark">سنجاق‌شده</span>
        @endif
        @if ($post->status === 'draft')
            <span class="badge bg-dark">پیش‌نویس</span>
        @elseif ($post->isScheduled())
            <span class="badge bg-info text-dark">زمان‌بندی‌شده</span>
        @endif
    </div>

    <div class="card-body d-flex flex-column">
        <h2 class="h6 fw-bold mb-2">
            <a href="{{ route('news.show', $post) }}" class="stretched-link text-body text-decoration-none">{{ $post->title }}</a>
        </h2>
        <p class="small text-muted flex-grow-1 mb-3">{{ $post->excerpt }}</p>

        <div class="d-flex justify-content-between small text-muted">
            <span>{{ $post->user->fullname }}</span>
            <span>{{ $post->created_at->format('Y/m/d') }}</span>
        </div>
        <div class="d-flex flex-wrap gap-3 small text-muted mt-2 pt-2 border-top">
            <span>{{ $post->reading_minutes }} دقیقه مطالعه</span>
            <span>{{ $post->views }} بازدید</span>
            <span>{{ $post->comments_count }} نظر</span>
        </div>
    </div>
</article>
