@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">ویرایش دسته‌بندی</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">نام دسته‌بندی</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" maxlength="100" required
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">ذخیره</button>
                        <a href="{{ route('categories.index') }}" class="btn btn-link">انصراف</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
