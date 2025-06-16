@extends('layouts.front')


@section('content')
    @include('front.includes.profile_header')

    {{-- ############### CMS Welcome Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-md-7 col-12 order-md-1 order-2 d-flex align-items-center text-start justify-content-center">
                    <div class="">
                        <p class="section-content">
                            {{ $frontHomecmsService->description }}
                        </p>
                    </div>
                </div>
                <div class="col-md-5 col-12 order-md-2 order-1 position-relative">
                    <img class="img-fluid" src="{{ asset('storage/' . $frontHomecmsService->image) }}"
                        alt="{{ $frontHomecmsService->title }}">
                </div>
            </div>
        </div>
    </section>
    {{-- ############### CMS Welcome End #################### --}}


    {{-- ############### CMS Welcome Start ################## --}}
    <section>
        <div class="container mb-xl-5 mb-3">
            <div class="row">
                <div class="col-12 text-center position-relative mt-5 mb-2">
                    <h1 class="section-heading">E SERVICES & OFFLINE SERVICES</h1>
                </div>
            </div>
            <div class="row gap-3 text-center justify-content-center">
                <div class="col-lg-10 col-12">
                    <div class="custom-servicecard">
                        <div class="d-md-flex d-column align-items-center text-ms-start text-center justify-content-md-between justify-content-center gap-xl-5 gap-lg-4 gap-md-3 gap-2">
                            <div class="servicecard-index">01.</div>
                            <div class="servicecard-title">MSME</div>
                            <div class="servicecard-text">
                                At the start of every project, we customize detailed project plan together you set
                                extointions ensure alightment and decline key milestones and developers.
                            </div>
                            <div class="servicecard-arrow">
                                <a href="{{ route('front.services.msme')}}"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-10 col-12">
                    <div class="custom-servicecard">
                        <div class="d-md-flex d-column align-items-center text-ms-start text-center justify-content-md-between justify-content-center gap-xl-5 gap-lg-4 gap-md-3 gap-2">
                            <div class="servicecard-index">02.</div>
                            <div class="servicecard-title">MEDIATION</div>
                            <div class="servicecard-text">
                                At the start of every project, we customize detailed project plan together you set
                                extointions ensure alightment and decline key milestones and developers.
                            </div>
                            <div class="servicecard-arrow">
                                <a href="{{ route('front.services.mediation')}}"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-10 col-12">
                    <div class="custom-servicecard">
                        <div class="d-md-flex d-column align-items-center text-ms-start text-center justify-content-md-between justify-content-center gap-xl-5 gap-lg-4 gap-md-3 gap-2">
                            <div class="servicecard-index">03.</div>
                            <div class="servicecard-title">CONCILIATION</div>
                            <div class="servicecard-text">
                                At the start of every project, we customize detailed project plan together you set
                                extointions ensure alightment and decline key milestones and developers.
                            </div>
                            <div class="servicecard-arrow">
                                <a href="{{ route('front.services.conciliation')}}"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-10 col-12">
                    <div class="custom-servicecard">
                        <div class="d-md-flex d-column align-items-center text-ms-start text-center justify-content-md-between justify-content-center gap-xl-5 gap-lg-4 gap-md-3 gap-2">
                            <div class="servicecard-index">04.</div>
                            <div class="servicecard-title">ARBITRATION</div>
                            <div class="servicecard-text">
                                At the start of every project, we customize detailed project plan together you set
                                extointions ensure alightment and decline key milestones and developers.
                            </div>
                            <div class="servicecard-arrow">
                                <a href="{{ route('front.services.arbitration')}}"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-10 col-12">
                    <div class="custom-servicecard">
                        <div class="d-md-flex d-column align-items-center text-ms-start text-center justify-content-md-between justify-content-center gap-xl-5 gap-lg-4 gap-md-3 gap-2">
                            <div class="servicecard-index">05.</div>
                            <div class="servicecard-title">ODR</div>
                            <div class="servicecard-text">
                                At the start of every project, we customize detailed project plan together you set
                                extointions ensure alightment and decline key milestones and developers.
                            </div>
                            <div class="servicecard-arrow">
                                <a href="{{ route('front.services.odr')}}"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-10 col-12">
                    <div class="custom-servicecard">
                        <div class="d-md-flex d-column align-items-center text-ms-start text-center justify-content-md-between justify-content-center gap-xl-5 gap-lg-4 gap-md-3 gap-2">
                            <div class="servicecard-index">06.</div>
                            <div class="servicecard-title">LOK ADALAT</div>
                            <div class="servicecard-text">
                                At the start of every project, we customize detailed project plan together you set
                                extointions ensure alightment and decline key milestones and developers.
                            </div>
                            <div class="servicecard-arrow">
                                <a href="{{ route('front.services.lokadalat')}}"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- ############### CMS Welcome End #################### --}}
@endsection
