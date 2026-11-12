<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-3">
        <label for="title" class="form-label">عنوان خبر</label>
        <input id="title" type="text" name="title" value="{{ old('title', $post->title) }}" maxlength="255" required
               class="form-control @error('title') is-invalid @enderror">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="category_id" class="form-label">دسته‌بندی</label>
        <select id="category_id" name="category_id" required class="form-select @error('category_id') is-invalid @enderror">
            <option value="">انتخاب کنید</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="body" class="form-label">متن خبر</label>
        <textarea id="body" name="body" rows="10" required class="form-control @error('body') is-invalid @enderror">{{ old('body', $post->body) }}</textarea>
        @error('body')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">تصویر (اختیاری، حداکثر ۲ مگابایت، فرمت‌های jpg، png یا webp)</label>
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

    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('news.index') }}" class="btn btn-link">انصراف</a>
</form>
