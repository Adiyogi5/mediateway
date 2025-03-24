@extends('layouts.front')

@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}
    
    <div class="container about-us">
        <div class="row my-5">
            <div class="col-lg-12">
                <div class="accordion accordion-flush" id="faqAccount">
                    @foreach ($faqs as $faq)
                        <div class="accordion-item bg-transparent border-top border-bottom py-2">
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
