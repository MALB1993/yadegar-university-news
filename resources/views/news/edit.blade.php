@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">ویرایش خبر</div>
                <div class="card-body">
                    @include('news._form', [
                        'action' => route('news.update', $post),
                        'method' => 'PUT',
                        'submitLabel' => 'ذخیره تغییرات',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
