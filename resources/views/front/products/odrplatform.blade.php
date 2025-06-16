@extends('layouts.front')


@section('content')
    @include('front.includes.profile_header')

    {{-- ############### CMS Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative mt-5 mb-2">
                    <h1 class="section-heading">ODR PLATFORM</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 col-12 order-md-1 order-2 d-flex align-items-center text-start justify-content-center">
                    <div class="">
                        <h3 class="section-title">{{ $frontHomecmsodrplatform->title }}</h3>
                        <p class="section-content">
                            {{ $frontHomecmsodrplatform->description }}
                        </p>
                    </div>
                </div>
                <div class="col-md-5 col-12 order-md-2 order-1 position-relative">
                    <img class="img-fluid" src="{{ asset('storage/' . $frontHomecmsodrplatform->image) }}"
                        alt="{{ $frontHomecmsodrplatform->title }}">
                </div>
            </div>
        </div>
    </section>
    {{-- ############### CMS End #################### --}}


@endsection
