@extends('layouts.front')

@section('content')
    @include('partial.frontend.breadcrumb')
    <div class="container about-us">
        <div class="row my-5">
            <div class="col-12">
                {!! $content->description !!}
            </div>
        </div>
    </div>
@endsection
