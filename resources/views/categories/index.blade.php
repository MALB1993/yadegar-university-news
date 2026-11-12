@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h4 mb-4">مدیریت دسته‌بندی‌ها</h1>

    <div class="card mb-4">
        <div class="card-header">افزودن دسته‌بندی</div>
        <div class="card-body">
            <form method="POST" action="{{ route('categories.store') }}" class="row g-2">
                @csrf
                <div class="col-md-9">
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="نام دسته‌بندی" maxlength="100" required
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">افزودن</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-white align-middle">
            <thead>
                <tr>
                    <th>نام</th>
                    <th class="text-center">تعداد خبرها</th>
                    <th class="text-center">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="text-center">{{ $category->posts_count }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary btn-sm">ویرایش</a>
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('این دسته‌بندی حذف شود؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">دسته‌بندی‌ای وجود ندارد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
