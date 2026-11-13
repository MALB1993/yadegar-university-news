@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 fw-bold mb-4">پروفایل من</h1>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-center mb-3">
                        @include('layouts._avatar', ['user' => $user, 'size' => 110])
                    </div>
                    <h2 class="h5 mb-1">{{ $user->fullname }}</h2>
                    <span class="badge bg-primary">{{ $user->roleLabel }}</span>
                    <p class="small text-muted mt-3 mb-0">
                        شماره دانشجویی/پرسنلی: <span dir="ltr">{{ $user->identification_code }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                  class="card border-0 shadow-sm rounded-4">
                @csrf
                @method('PUT')

                <div class="card-body p-4">
                    <h2 class="h6 mb-3">اطلاعات شخصی</h2>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="fullname" class="form-label">نام و نام خانوادگی</label>
                            <input id="fullname" type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" required
                                   class="form-control @error('fullname') is-invalid @enderror">
                            @error('fullname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">پست الکترونیکی</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required dir="ltr"
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">شماره تماس</label>
                            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" dir="ltr"
                                   class="form-control @error('phone_number') is-invalid @enderror">
                            @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="profile_picture" class="form-label">تصویر پروفایل</label>
                            <input id="profile_picture" type="file" name="profile_picture" accept="image/jpeg,image/png,image/webp"
                                   class="form-control @error('profile_picture') is-invalid @enderror">
                            @error('profile_picture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h2 class="h6 mb-3">تغییر رمز عبور <span class="small text-muted fw-normal">(اختیاری)</span></h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label">رمز فعلی</label>
                            <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                                   class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="password" class="form-label">رمز جدید</label>
                            <input id="password" type="password" name="password" autocomplete="new-password"
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="password_confirmation" class="form-label">تکرار رمز جدید</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                                   class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">ذخیره‌ی تغییرات</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
