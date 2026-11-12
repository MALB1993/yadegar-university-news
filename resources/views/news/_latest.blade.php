<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>آخرین اخبار</span>
        <a href="{{ route('news.index') }}" class="small">همه‌ی اخبار</a>
    </div>
    <ul class="list-group list-group-flush">
        @forelse ($posts as $post)
            <li class="list-group-item">
                <a href="{{ route('news.show', $post) }}" class="text-decoration-none fw-semibold">{{ $post->title }}</a>
                <div class="small text-muted mt-1">
                    <span class="badge bg-secondary">{{ $post->category->name }}</span>
                    {{ $post->created_at->format('Y/m/d') }}
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">هنوز خبری ثبت نشده است.</li>
        @endforelse
    </ul>
</div>
