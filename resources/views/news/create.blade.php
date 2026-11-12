@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">ثبت خبر جدید</div>
                <div class="card-body">
                    @include('news._form', [
                        'action' => route('news.store'),
                        'method' => 'POST',
                        'submitLabel' => 'ثبت خبر',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
