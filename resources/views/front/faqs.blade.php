@extends('layouts.front')

@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}
    
    <div class="container faq-accordion">
        <div class="row my-5">
            <div class="col-12">
                <div class="row mb-4">
                    <div class="col-md-6 col-12 my-auto order-md-1 order-2">
                        <h3 class="section-title" style="color: #00000080 !important; font-size:28px !important;">{{ $frontHomecmsFaqs->title }}</h3>
                        <p class="section-content">
                            {{ $frontHomecmsFaqs->description }}
                        </p>
                    </div>
                    <div class="col-md-6 col-12 position-relative order-md-2 order-1">
                        <img class="img-fluid" src="{{ asset('storage/' . $frontHomecmsFaqs->image) }}" alt="{{ $frontHomecmsFaqs->title }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                
                <div class="text-center justify-content-center my-4">
                    <p style="color:#00000087;">Have Questions? We're here to help.</p>
                </div>

                <div class="accordion accordion-flush" id="faqAccount">
                    @foreach ($faqs as $faq)                        
                        <div class="accordion-item bg-transparent border-top border-bottom">
                            <h2 class="accordion-header" id="faq-head-{{ $loop->index }}">
                                <button
                                    class="accordion-button collapsed bg-transparent fw-semi-bold shadow-none text-second"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $loop->index }}"
                                    aria-expanded="false" aria-controls="faq-{{ $loop->index }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="faq-{{ $loop->index }}" class="accordion-collapse collapse"
                                aria-labelledby="faq-head-{{ $loop->index }}">
                                <div class="accordion-body">
                                    <p> {{ $faq->answer }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
