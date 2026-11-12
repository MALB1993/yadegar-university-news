@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h4 mb-4">بخش دانشجویان</h1>

    <div class="row g-4">
        <div class="col-lg-8">
            @include('news._latest', ['posts' => $latestPosts])
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6">راهنما</h2>
                    <p class="small text-muted">
                        در بخش اخبار می‌توانید اطلاعیه‌های دانشگاه را ببینید، با عنوان جستجو کنید و زیر هر خبر نظر بگذارید.
                    </p>
                    <a href="{{ route('news.index') }}" class="btn btn-primary btn-sm">مشاهده‌ی همه‌ی اخبار</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
