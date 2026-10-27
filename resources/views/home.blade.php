@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-bg-primary d-flex justify-content-between align-items-center">
                    <span>{{ auth()->user()->fullname }}</span>
                    <span>
                        {{-- نمایش نقش --}}
                        @if (auth()->user()->isStudent())
                            <span class="badge bg-primary">دانشجو</span>
                        @elseif(auth()->user()->isProfessor())
                            <span class="badge bg-success">استاد</span>
                        @elseif(auth()->user()->isEmployee())
                            <span class="badge bg-info">کارمند</span>
                        @endif
                    </span>
                </div>


                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{-- نمایش پیام مربوط به نقش --}}
                    @switch(auth()->user()->role)
                        @case('professor')
                            <p>شما به عنوان <b class="font-weight-bold text-success">استاد</b> وارد سامانه دانایار شده‌اید.</p>
                            <p>می‌توانید به بخش‌های مربوط به مدیریت کلاس‌ها و دانشجویان دسترسی داشته باشید.</p>
                            @break
                        @case('employee')
                            <p>شما به عنوان <b class="font-weight-bold text-info">کارمند</b> وارد سامانه دانایار شده‌اید.</p>
                            <p>می‌توانید به بخش‌های مربوط به مدیریت دانشجویان و اساتید دسترسی داشته باشید.</p>
                            @break
                        @case('student')
                            <p>شما به عنوان <b class="font-weight-bold text-primary">دانشجو</b> وارد سامانه دانایار شده‌اید.</p>
                            <p>می‌توانید به بخش‌های مربوط به مشاهده نمرات و ثبت‌نام در کلاس‌ها دسترسی داشته باشید.</p>
                            <p>ویرایش جزوات و مشاهده جزوات و اخبارهای مربوط به کلاس را در این بخش داشته باشید.</p>
                            @break
                        @default
                            
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
