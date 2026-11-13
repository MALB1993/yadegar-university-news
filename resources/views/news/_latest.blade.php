<div class="row row-cols-1 row-cols-md-2 g-4">
    @forelse ($posts as $post)
        <div class="col">
            @include('news._card', ['post' => $post])
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">هنوز خبری منتشر نشده است.</div>
        </div>
    @endforelse
</div>
