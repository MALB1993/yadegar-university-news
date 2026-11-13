<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-3">
        <label for="title" class="form-label">عنوان خبر</label>
        <input id="title" type="text" name="title" value="{{ old('title', $post->title) }}" maxlength="255" required
               class="form-control form-control-lg @error('title') is-invalid @enderror">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="category_id" class="form-label">دسته‌بندی</label>
            <select id="category_id" name="category_id" required class="form-select @error('category_id') is-invalid @enderror">
                <option value="">انتخاب کنید</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="status" class="form-label">وضعیت انتشار</label>
            <select id="status" name="status" class="form-select">
                <option value="published" @selected(old('status', $post->status ?? 'published') === 'published')>منتشر شود</option>
                <option value="draft" @selected(old('status', $post->status ?? 'published') === 'draft')>پیش‌نویس (فقط برای خودم)</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="published_at" class="form-label">زمان انتشار (اختیاری)</label>
            <input id="published_at" type="datetime-local" name="published_at" dir="ltr"
                   value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                   class="form-control @error('published_at') is-invalid @enderror">
            <div class="form-text">خالی بماند یعنی همین حالا منتشر شود. با تاریخ آینده، خبر زمان‌بندی می‌شود.</div>
            @error('published_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if (auth()->user()->isEmployee())
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_pinned" name="is_pinned" value="1"
                           @checked(old('is_pinned', $post->is_pinned))>
                    <label class="form-check-label" for="is_pinned">سنجاق کردن در بالای صفحه‌ی اخبار</label>
                </div>
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="body" class="form-label">متن خبر</label>
        <textarea id="body" name="body" rows="10" required class="form-control @error('body') is-invalid @enderror">{{ old('body', $post->body) }}</textarea>
        <div class="form-text">پاراگراف‌ها را با یک خط خالی جدا کنید.</div>
        @error('body')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="image" class="form-label">تصویر (اختیاری، حداکثر ۲ مگابایت؛ jpg، png یا webp)</label>
        @if ($post->image_url)
            <div class="mb-2">
                <img src="{{ $post->image_url }}" alt="" class="img-thumbnail" style="max-height: 150px;">
            </div>
        @endif
        <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp"
               class="form-control @error('image') is-invalid @enderror">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary px-4">{{ $submitLabel }}</button>
    <a href="{{ route('news.index') }}" class="btn btn-link">انصراف</a>
</form>
