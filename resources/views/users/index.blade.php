@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 fw-bold mb-1">مدیریت کاربران</h1>
    <p class="text-muted mb-4">نقش و وضعیت حساب کاربران را تغییر دهید. حساب غیرفعال نمی‌تواند وارد سامانه شود.</p>

    <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="جستجو با نام، شماره یا ایمیل">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">همه‌ی نقش‌ها</option>
                @foreach (['student' => 'دانشجو', 'professor' => 'استاد', 'employee' => 'کارمند'] as $value => $label)
                    <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-outline-primary">فیلتر</button>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>کاربر</th>
                        <th>شماره</th>
                        <th>نقش</th>
                        <th>وضعیت</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php($isSelf = $user->id === auth()->id())
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @include('layouts._avatar', ['user' => $user, 'size' => 36])
                                    <div>
                                        <div class="fw-semibold">{{ $user->fullname }}</div>
                                        <div class="small text-muted" dir="ltr">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td dir="ltr">{{ $user->identification_code }}</td>
                            <td>
                                <select name="role" form="user-form-{{ $user->id }}" class="form-select form-select-sm" style="min-width: 130px;" @disabled($isSelf)>
                                    <option value="student" @selected($user->role === 'student')>دانشجو</option>
                                    <option value="professor" @selected($user->role === 'professor')>استاد</option>
                                    <option value="employee" @selected($user->role === 'employee')>کارمند</option>
                                </select>
                            </td>
                            <td>
                                <select name="status" form="user-form-{{ $user->id }}" class="form-select form-select-sm" style="min-width: 120px;" @disabled($isSelf)>
                                    <option value="active" @selected($user->status === 'active')>فعال</option>
                                    <option value="inactive" @selected($user->status === 'inactive')>غیرفعال</option>
                                </select>
                            </td>
                            <td class="text-end">
                                <form id="user-form-{{ $user->id }}" method="POST" action="{{ route('users.update', $user) }}">
                                    @csrf
                                    @method('PUT')
                                    @if ($isSelf)
                                        <span class="badge bg-secondary">حساب شما</span>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-primary">ذخیره</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">کاربری با این مشخصات پیدا نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
