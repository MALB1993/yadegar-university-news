@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 fw-bold mb-1">نظارت بر نظرات</h1>
    <p class="text-muted mb-4">آخرین نظرات کاربران را مرور کنید و نظرهای نامناسب را حذف کنید.</p>

    <form method="GET" action="{{ route('moderation.comments') }}" class="row g-2 mb-4">
        <div class="col-md-9">
            <input type="search" name="q" value="{{ $search }}" class="form-control"
                   placeholder="جستجو در متن نظر یا نام نویسنده">
        </div>
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-outline-primary">جستجو</button>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>نظر</th>
                        <th>نویسنده</th>
                        <th>خبر</th>
                        <th class="text-center">پسند</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $comment)
                        <tr>
                            <td style="min-width: 220px;">
                                <div>{{ \Illuminate\Support\Str::limit($comment->body, 120) }}</div>
                                <div class="small text-muted">{{ $comment->created_at->format('Y/m/d H:i') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $comment->user->fullname }}</div>
                                <div class="small text-muted">{{ $comment->user->roleLabel }}</div>
                            </td>
                            <td>
                                <a href="{{ route('news.show', $comment->post) }}#comment-{{ $comment->id }}" class="text-decoration-none">
                                    {{ \Illuminate\Support\Str::limit($comment->post->title, 50) }}
                                </a>
                            </td>
                            <td class="text-center">{{ $comment->liked_by_count }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                      onsubmit="return confirm('این نظر حذف شود؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">نظری با این مشخصات پیدا نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $comments->links() }}
    </div>
</div>
@endsection
