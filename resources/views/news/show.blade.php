@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="card mb-4">
                <div class="card-body">
                    <span class="badge bg-secondary">{{ $post->category->name }}</span>
                    <h1 class="h3 mt-2">{{ $post->title }}</h1>
                    <p class="text-muted small">{{ $post->user->fullname }} · {{ $post->created_at->format('Y/m/d H:i') }}</p>

                    @if ($post->image_url)
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="img-fluid rounded mb-3">
                    @endif

                    <div class="lh-lg">{!! nl2br(e($post->body)) !!}</div>

                    <div class="mt-4 d-flex gap-2">
                        @can('update', $post)
                            <a href="{{ route('news.edit', $post) }}" class="btn btn-outline-primary btn-sm">ویرایش</a>
                        @endcan
                        @can('delete', $post)
                            <form method="POST" action="{{ route('news.destroy', $post) }}" onsubmit="return confirm('این خبر حذف شود؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">حذف</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </article>

            <section id="comments">
                <h2 class="h5 mb-3">نظرات ({{ $post->comments->count() }})</h2>

                <form method="POST" action="{{ route('comments.store', $post) }}" class="mb-4">
                    @csrf
                    <textarea name="body" rows="3" class="form-control mb-2 @error('body') is-invalid @enderror" placeholder="نظر خود را بنویسید">{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn btn-primary btn-sm">ثبت نظر</button>
                </form>

                @forelse ($post->comments as $comment)
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <strong class="small">{{ $comment->user->fullname }}</strong>
                                <span class="text-muted small">{{ $comment->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <p class="mb-1 mt-1">{!! nl2br(e($comment->body)) !!}</p>
                            @can('delete', $comment)
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="text-end" onsubmit="return confirm('این نظر حذف شود؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0">حذف</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <p class="text-muted">هنوز نظری ثبت نشده است.</p>
                @endforelse
            </section>
        </div>
    </div>
</div>
@endsection
