@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 fw-bold mb-1">نشان‌شده‌ها</h1>
    <p class="text-muted mb-4">خبرهایی که برای خواندن بعدی ذخیره کرده‌اید.</p>

    @if ($posts->isEmpty())
        <div class="empty-state">
            <p class="mb-3">هنوز خبری را ذخیره نکرده‌اید.</p>
            <a href="{{ route('news.index') }}" class="btn btn-primary">رفتن به اخبار</a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($posts as $post)
                <div class="col">
                    @include('news._card', ['post' => $post])
                    <form method="POST" action="{{ route('news.bookmark', $post) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">حذف از نشان‌شده‌ها</button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
