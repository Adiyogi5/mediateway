@extends('layouts.front')


@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}

    <section>
        <div class="container my-xl-5 my-lg-4 my-3">
            <div class="row">
                <div class="col-md-3 col-12">

                    @include('front.includes.sidebar_inner')

                </div>

                <div class="col-md-9 col-12 mt-md-0 mt-3">
                    <div class="card-inner form-validate pt-5 pb-5">
                        <div class="step step-5" id="step-5">
                            <!-- Step 5 form fields here -->
                            <div class="row mx-lg-3 mx-0 mb-3">
                                    <div class="col-md-8 col-12 mx-auto text-center justify-content-center ">
                                        <img src="{{ asset('public/assets/img/success-booking.png') }}" alt="" style="height: 100px; width:100px;">
                                        <p class="mt-2"><strong>File Case No.</strong> :  {{ $casefilepayment->file_case_no }}</p>
                                        <p><strong>Transaction ID.</strong> :  {{ $casefilepayment->transaction_id }}</p>
                                        <h3 class="my-5 text-success">You Successfully Created Your File Case Payment.</h3>
                                        <h6><a href="{{ route('individual.dashboard') }}" class=" text-warning"> Go Home</a></h6>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
@endsection
