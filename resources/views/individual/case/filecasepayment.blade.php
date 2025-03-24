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
                    <div class="card-inner form-validate pt-3 pb-3">
                        <div class="step step-5" id="step-5">
                            <!-- Step 5 form fields here -->
                            <div class="row mx-lg-3 mx-0">
                                <div class="col-md-12 mb-3 text-center justify-content-center">
                                    <img class="img-fluid img-razorpay my-5" src="{{ asset('assets/img/razorpay.png') }}"
                                        alt="">
                                    </br>
                                    @if(!empty($casefilepayment))
                                    <form name='razorpayform' action="{{ route('individual.case.verify_payment') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="razorpay_order_id" id="razorpay_order_idresources/views/individual/case/filecase.blade.php" value="{{ $razorpayOrderId }}">
                                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                                        <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >

                                        <table class="table table-bordered" style="width:100%">
                                            <tbody>
                                                <tr>
                                                    <td colspan="6">
                                                        <h3>Please Make Payment for Confirm File Case</h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        <h6>Filed Case No.</h6>
                                                    </td>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">{{ $casefilepayment->file_case_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        <h6>Name</h6>
                                                    </td>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">{{ $casefilepayment->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        <h6>Email</h6>
                                                    </td>
                                                    <td class="ps-md-5 ps-2">{{ $casefilepayment->email }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        <h6>Mobile</h6>
                                                    </td>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">{{ $casefilepayment->mobile }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        <h6>Disputed Amount</h6>
                                                    </td>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">{{ $casefilepayment->amount_in_dispute }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        <h6>Case Type</h6>
                                                    </td>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        {{ config('constant.case_type')[$casefilepayment->case_type] ?? 'Unknown' }}
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        <h6>Case File Payment Amount</h6>
                                                    </td>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">₹{{ $site_settings['case_file_amount'] }}/-</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        <h6>Payment Status</h6>
                                                    </td>
                                                    <td class="ps-lg-5 ps-md-3 ps-2">
                                                        @if ($casefilepayment->payment_status == 0)
                                                            <p class="btn btn-sm btn-warning">Pending</p>
                                                        @elseif($casefilepayment->payment_status == 1)
                                                            <p class="btn btn-sm btn-success">Paid</p>
                                                        @else
                                                            <p class="btn btn-sm btn-danger">{{ $casefilepayment->payment_status }}</p>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <button class="btn btn-success btn-razor-pay px-5 my-3 razorpay-btn" id="rzp-button1">PAY ₹{{ $site_settings['case_file_amount'] }}/-</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table> 

                                    </form>
                                    @endif
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
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        var options = <?=$payment_json?>;
        options.handler = function (response){
            
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.razorpayform.submit();
        };
        
        options.theme.image_padding = false;
        
        options.modal = {
            ondismiss: function() {
                console.log("This code runs when the popup is closed");
            },
            escape: true,
            backdropclose: false
        };
        
        var rzp = new Razorpay(options);
        
        document.getElementById('rzp-button1').onclick = function(e){
            rzp.open();
            e.preventDefault();
        }
    </script>
@endsection
