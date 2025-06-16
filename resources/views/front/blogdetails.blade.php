@extends('layouts.front')

@section('content')
    {{-- Breadcrumb --}}
    @include('front.includes.profile_header')

    <div class="container blog-section">
        <div class="row my-5">
            <div class="col-md-9 col-12">
                <div class="row" id="blog-container">
                   @if(!empty($blogdetails))
                    <div class="col-12">
                         <img src="{{ asset('storage/' . $blogdetails->image) }}" class="card-img-top px-3" alt="{{$blogdetails->title}}">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-start gap-3">
                        <span><i class="fa-solid fa-calendar-days"></i> {{$blogdetails->date}}</span>
                        <span><i class="fa-solid fa-pencil"></i> {{$blogdetails->post_by}}</span>
                    </div>
                    <h5 class="card-title text-start">{{$blogdetails->title}}</h5>
                    <p class="card-text">{!! $blogdetails->description !!}</p>
                </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-md-3 col-12">
                <h5 class="popular-heading">POPULAR</h5>
                <div class="popular-list">
                    @foreach($blogssidebar as $sidebar)
                    <a class="text-decoration-none" href="{{ route('front.blogdetails', $sidebar['id']) }}">
                        <div class="popular-item d-flex align-items-center">
                            <img src="{{ asset('storage/' . $sidebar['image']) }}" alt="{{$sidebar['title']}}">
                            <p class="card-text fw-bold">{{$sidebar['title']}}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

