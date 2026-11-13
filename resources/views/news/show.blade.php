@extends('layouts.app')

@section('content')
<div class="container py-4">
    <nav aria-label="مسیر" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('news.index') }}">اخبار</a></li>
            <li class="breadcrumb-item"><a href="{{ route('news.index', ['category' => $post->category_id]) }}">{{ $post->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</li>
        </ol>
    </nav>

    @if ($post->isScheduled())
        <div class="alert alert-info border-0 shadow-sm">
            این خبر زمان‌بندی شده است و در تاریخ {{ $post->published_at->format('Y/m/d H:i') }} منتشر می‌شود. تا آن زمان فقط نویسنده و کارمندان آن را می‌بینند.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <article class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="news-cover cover-hero cover-{{ $post->category->id % 5 }}">
                    <div class="d-flex flex-wrap gap-2 mb-auto">
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
                    <h1 class="h3 fw-bold mb-0 mt-3">{{ $post->title }}</h1>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center gap-3 mb-4 small text-muted">
                        @include('layouts._avatar', ['user' => $post->user, 'size' => 44])
                        <div>
                            <div class="fw-semibold text-body">{{ $post->user->fullname }}</div>
                            <div>{{ $post->user->roleLabel }} · {{ $post->created_at->format('Y/m/d H:i') }}</div>
                        </div>
                    </div>

                    @if ($post->image_url)
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="img-fluid rounded-4 mb-4 w-100 object-fit-cover">
                    @endif

                    <div class="prose">{!! nl2br(e($post->body)) !!}</div>

                    @canany(['update', 'delete'], $post)
                        <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                            @can('update', $post)
                                <a href="{{ route('news.edit', $post) }}" class="btn btn-outline-primary btn-sm">ویرایش</a>
                            @endcan
                            @can('create', App\Models\Post::class)
                                <form method="POST" action="{{ route('news.duplicate', $post) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">ساخت پیش‌نویس از این خبر</button>
                                </form>
                            @endcan
                            @can('delete', $post)
                                <form method="POST" action="{{ route('news.destroy', $post) }}" onsubmit="return confirm('این خبر حذف شود؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">حذف</button>
                                </form>
                            @endcan
                        </div>
                    @endcanany
                </div>
            </article>

            <section id="comments" class="mt-5">
                <h2 class="section-title mb-4">نظرات ({{ $post->comments->count() }})</h2>

                <form method="POST" action="{{ route('comments.store', $post) }}" class="bg-white rounded-4 shadow-sm p-3 mb-4">
                    @csrf
                    <label for="comment-body" class="form-label small text-muted">نظر شما</label>
                    <textarea id="comment-body" name="body" rows="3"
                              class="form-control border-0 bg-light @error('body') is-invalid @enderror"
                              placeholder="دیدگاه خود را بنویسید…">{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-primary btn-sm px-4">ثبت نظر</button>
                    </div>
                </form>

                @forelse ($post->comments as $comment)
                    <div class="d-flex gap-3 mb-3" id="comment-{{ $comment->id }}">
                        @include('layouts._avatar', ['user' => $comment->user, 'size' => 40])
                        <div class="flex-grow-1 bg-white rounded-4 shadow-sm p-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                <div>
                                    <span class="fw-semibold">{{ $comment->user->fullname }}</span>
                                    <span class="badge bg-light text-dark border ms-1">{{ $comment->user->roleLabel }}</span>
                                </div>
                                <span class="small text-muted">{{ $comment->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <p class="mb-2">{!! nl2br(e($comment->body)) !!}</p>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <form method="POST" action="{{ route('comments.like', $comment) }}">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm {{ in_array($comment->id, $likedCommentIds, true) ? 'btn-primary' : 'btn-outline-primary' }}">
                                        پسندیدن ({{ $comment->liked_by_count }})
                                    </button>
                                </form>

                                @can('delete', $comment)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                          onsubmit="return confirm('این نظر حذف شود؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">حذف نظر</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">هنوز نظری ثبت نشده است. اولین نفر باشید!</div>
                @endforelse
            </section>
        </div>

        <aside class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-lg-top" style="top: 90px;">
                <div class="card-body">
                    <form method="POST" action="{{ route('news.bookmark', $post) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn w-100 {{ $isBookmarked ? 'btn-warning' : 'btn-outline-warning' }}">
                            {{ $isBookmarked ? 'حذف از نشان‌شده‌ها' : 'ذخیره برای بعد' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('categories.follow', $post->category) }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn w-100 {{ $isFollowingCategory ? 'btn-success' : 'btn-outline-success' }}">
                            {{ $isFollowingCategory ? 'دنبال می‌کنید: '.$post->category->name : 'دنبال کردن «'.$post->category->name.'»' }}
                        </button>
                    </form>

                    <dl class="row small mb-0">
                        <dt class="col-6 text-muted fw-normal">دسته‌بندی</dt>
                        <dd class="col-6 text-end mb-2">{{ $post->category->name }}</dd>

                        <dt class="col-6 text-muted fw-normal">زمان مطالعه</dt>
                        <dd class="col-6 text-end mb-2">{{ $post->reading_minutes }} دقیقه</dd>

                        <dt class="col-6 text-muted fw-normal">بازدید</dt>
                        <dd class="col-6 text-end mb-2">{{ $post->views }}</dd>

                        <dt class="col-6 text-muted fw-normal">نظرات</dt>
                        <dd class="col-6 text-end mb-0">{{ $post->comments->count() }}</dd>
                    </dl>
                </div>
            </div>
        </aside>
    </div>

    @if ($related->isNotEmpty())
        <section class="mt-5">
            <h2 class="section-title mb-4">اخبار مرتبط</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach ($related as $relatedPost)
                    <div class="col">@include('news._card', ['post' => $relatedPost])</div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
