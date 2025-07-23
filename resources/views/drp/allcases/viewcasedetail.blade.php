@extends('layouts.front')
<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/dt-global_style.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />

@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}

    <div class="container my-xl-5 my-lg-4 my-3">
        <div class="row">
            <div class="col-md-3 col-12">

                @include('front.includes.sidebar_inner')

            </div>

            <div class="col-md-9 col-12">
                <div class="card mb-3 card-inner form-validate">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Filed Case Details</h5>
                            </div>
                             <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                                    <a href="{{ route('drp.allcases.caselist') }}"
                                        class="btn btn-outline-secondary"> <i class="fa fa-arrow-left me-1"></i> Go
                                        Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($caseData->isNotEmpty())
                        <div class="card-body table-padding">
                            <div class="col-md-12 col-12 position-relative">
                                <div class="custom-case-card">
                                    @foreach ($caseData as $case)
                                        <hr style="border: 3px solid #ffb000; margin-bottom:0px; margin-top:0px;">

                                        <h4 class="case-heading mt-0">Assigned Case :</h4>
                                        @if ($case->assignedCases->isNotEmpty())
                                            @foreach ($case->assignedCases as $assigned)
                                                <div class="row gx-5 gy-3">
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To arbitrator</p>
                                                        <p class="case-text">{{ $assigned->arbitrator?->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To advocate</p>
                                                        <p class="case-text">{{ $assigned->advocate?->name ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To case_manager</p>
                                                        <p class="case-text">{{ $assigned->caseManager?->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To mediator</p>
                                                        <p class="case-text">{{ $assigned->mediator?->name ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To conciliator</p>
                                                        <p class="case-text">{{ $assigned->conciliator?->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <small class="text-center justify-content-center">
                                                <p class="mb-0 text-danger">Wait for case assignment.</p>
                                            </small>
                                        @endif

                                        <hr style="border: 3px solid #ffb000; margin-bottom:0px;">
                                        <h4 class="case-heading mt-0">Case Overview :</h4>
                                        <div class="row gx-5 gy-3">
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Case Number</p>
                                                <p class="case-text">{{ $case->case_number ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Loan Number</p>
                                                <p class="case-text">{{ $case->loan_number ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Language</p>
                                                <p class="case-text">
                                                    {{ $case->language == 1 ? 'Hindi' : ($case->language == 2 ? 'English' : '--') }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Dispute Type</p>
                                                <p class="case-text">
                                                    {{ config('constant.case_type')[$case->case_type] ?? '--' }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Case Status</p>
                                                <p
                                                    class="case-text {{ $case->status == 1 ? 'text-success' : 'text-danger' }}">
                                                    {{ $case->status == 1 ? 'Active' : 'Inactive' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Days Since Filing</p>
                                                <p class="case-text">
                                                    {{ intval(\Carbon\Carbon::parse($case->created_at)->diffInDays(now())) }}
                                                    days
                                                </p>
                                            </div>

                                            <div class="col-12">
                                                <hr class="m-0">
                                            </div>

                                            <div class="col-md-4 col-6">
                                                <p class="case-title">First Party</p>
                                                <p class="case-text">{{ $case->claimant_first_name }}
                                                    {{ $case->claimant_last_name }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">First Party Mobile</p>
                                                <p class="case-text">{{ $case->claimant_mobile ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">First Party Email</p>
                                                <p class="case-text">{{ $case->claimant_email ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">First Party Address Type</p>
                                                <p class="case-text">
                                                    {{ $case->claimant_address_type == 1 ? 'Home' : ($case->claimant_address_type == 2 ? 'Office' : '--') }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">First Party Address</p>
                                                <p class="case-text">{{ $case->claimant_address1 }}
                                                    {{ $case->claimant_address2 }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">First Party Pincode</p>
                                                <p class="case-text">{{ $case->claimant_pincode ?? '--' }}</p>
                                            </div>

                                            <div class="col-12">
                                                <hr class="m-0">
                                            </div>

                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Opposite Party</p>
                                                <p class="case-text">{{ $case->respondent_first_name }}
                                                    {{ $case->respondent_last_name }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Opposite Party Mobile</p>
                                                <p class="case-text">{{ $case->respondent_mobile ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Opposite Party Email</p>
                                                <p class="case-text">{{ $case->respondent_email ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Opposite Party Address Type</p>
                                                <p class="case-text">
                                                    {{ $case->respondent_address_type == 1 ? 'Home' : ($case->respondent_address_type == 2 ? 'Office' : '--') }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Opposite Party Address</p>
                                                <p class="case-text">{{ $case->respondent_address1 }}
                                                    {{ $case->respondent_address2 }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Opposite Party Pincode</p>
                                                <p class="case-text">{{ $case->respondent_pincode ?? '--' }}</p>
                                            </div>

                                            <div class="col-12">
                                                <hr class="m-0">
                                            </div>

                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Agreement Date</p>
                                                <p class="case-text">{{ $case->agreement_date ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Loan Application Date</p>
                                                <p class="case-text">{{ $case->loan_application_date ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Arbitration Date</p>
                                                <p class="case-text">{{ $case->arbitration_date ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Brief of Case</p>
                                                <p class="case-text">{{ $case->brief_of_case ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Amount In Dispute</p>
                                                <p class="case-text">{{ $case->amount_in_dispute ?? '--' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Agreement Exist</p>
                                                <p class="case-text">{{ $case->agreement_exist ?? '--' }}</p>
                                            </div>
                                        </div>

                                        @if ($case->file_case_details)
                                            <hr style="border: 3px solid #ffb000; margin-bottom:0px;">
                                            <h4 class="case-heading mt-0">File Case Details :</h4>
                                            <div class="row gx-5 gy-3">
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Product Type</p>
                                                    <p class="case-text">
                                                        {{ config('constant.product_type')[$case->product_type] ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Product</p>
                                                    <p class="case-text">{{ $case->file_case_details->product ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Asset Description</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->asset_description ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Sanction Letter Date</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->sanction_letter_date ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Rate of Intrest</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->rate_of_interest ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Registration No</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->registration_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Chassis No</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->chassis_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Engine No</p>
                                                    <p class="case-text">{{ $case->file_case_details->engin_no ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Finance Amount</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->finance_amount ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Finance Amount in Words</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->finance_amount_in_words ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">EMI Amount</p>
                                                    <p class="case-text">{{ $case->file_case_details->emi_amt ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">EMI Due Date</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->emi_due_date ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Tenure</p>
                                                    <p class="case-text">{{ $case->file_case_details->tenure ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Foreclosure Amount Date</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->foreclosure_amount_date ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Foreclosure Amount</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->foreclosure_amount ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Foreclosure Amount in Words</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->foreclosure_amount_in_words ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Claim Signatory Authorised Officer Name</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->claim_signatory_authorised_officer_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Claim Signatory Authorised Officer Father Name
                                                    </p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->claim_signatory_authorised_officer_father_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Claim Signatory Authorised Officer Designation
                                                    </p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->claim_signatory_authorised_officer_designation ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Claim Signatory Authorised Officer Mobile No</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Claim Signatory Authorised Officer Mail</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->claim_signatory_authorised_officer_mail_id ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Receiver Name</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->receiver_name ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Receiver Designation</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->receiver_designation ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Auction Date</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->auction_date ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Auction Amount</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->auction_amount ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Auction Amount in Words</p>
                                                    <p class="case-text">
                                                        {{ $case->file_case_details->auction_amount_in_words ?? '--' }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($case->guarantors)
                                            <hr style="border: 3px solid #ffb000; margin-bottom:0px;">
                                            <h4 class="case-heading mt-0">Guarantors :</h4>
                                            <div class="row gx-5 gy-3">
                                                @if ($case->guarantors->guarantor_1_name)
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">First Guarantor Name</p>
                                                    <p class="case-text">{{ $case->guarantors->guarantor_1_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">First Guarantor Mobile</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_1_mobile_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">First Guarantor Email</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_1_email_id ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">First Guarantor Father Name</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_1_father_name ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">First Guarantor Address</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_1_address ?? '--' }}
                                                    </p>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <hr class="m-0">
                                                </div>
                                                @endif

                                                @if ($case->guarantors->guarantor_2_name)
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Second Guarantor Name</p>
                                                    <p class="case-text">{{ $case->guarantors->guarantor_2_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Second Guarantor Mobile</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_2_mobile_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Second Guarantor Email</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_2_email_id ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Second Guarantor Father Name</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_2_father_name ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Second Guarantor Address</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_2_address ?? '--' }}
                                                    </p>
                                                </div>

                                                <div class="col-12">
                                                    <hr class="m-0">
                                                </div>
                                                @endif

                                                @if ($case->guarantors->guarantor_3_name)
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Third Guarantor Name</p>
                                                    <p class="case-text">{{ $case->guarantors->guarantor_3_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Third Guarantor Mobile</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_3_mobile_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Third Guarantor Email</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_3_email_id ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Third Guarantor Father Name</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_3_father_name ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Third Guarantor Address</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_3_address ?? '--' }}
                                                    </p>
                                                </div>

                                                <div class="col-12">
                                                    <hr class="m-0">
                                                </div>
                                                @endif

                                                @if ($case->guarantors->guarantor_4_name)
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fourth Guarantor Name</p>
                                                    <p class="case-text">{{ $case->guarantors->guarantor_4_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fourth Guarantor Mobile</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_4_mobile_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fourth Guarantor Email</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_4_email_id ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fourth Guarantor Father Name</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_4_father_name ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fourth Guarantor Address</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_4_address ?? '--' }}
                                                    </p>
                                                </div>

                                                <div class="col-12">
                                                    <hr class="m-0">
                                                </div>
                                                @endif

                                                @if ($case->guarantors->guarantor_5_name)
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fifth Guarantor Name</p>
                                                    <p class="case-text">{{ $case->guarantors->guarantor_5_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fifth Guarantor Mobile</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_5_mobile_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fifth Guarantor Email</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_5_email_id ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fifth Guarantor Father Name</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_5_father_name ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Fifth Guarantor Address</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_5_address ?? '--' }}
                                                    </p>
                                                </div>

                                                <div class="col-12">
                                                    <hr class="m-0">
                                                </div>
                                                @endif

                                                @if ($case->guarantors->guarantor_6_name)
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Sixth Guarantor Name</p>
                                                    <p class="case-text">{{ $case->guarantors->guarantor_6_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Sixth Guarantor Mobile</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_6_mobile_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Sixth Guarantor Email</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_6_email_id ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Sixth Guarantor Father Name</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_6_father_name ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Sixth Guarantor Address</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_6_address ?? '--' }}
                                                    </p>
                                                </div>

                                                <div class="col-12">
                                                    <hr class="m-0">
                                                </div>
                                                @endif

                                                @if ($case->guarantors->guarantor_7_name)
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Seventh Guarantor Name</p>
                                                    <p class="case-text">{{ $case->guarantors->guarantor_7_name ?? '--' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Seventh Guarantor Mobile</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_7_mobile_no ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Seventh Guarantor Email</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_7_email_id ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Seventh Guarantor Father Name</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_7_father_name ?? '--' }}</p>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <p class="case-title">Seventh Guarantor Address</p>
                                                    <p class="case-text">
                                                        {{ $case->guarantors->guarantor_7_address ?? '--' }}
                                                    </p>
                                                </div>
                                                @endif
                                            </div>
                                        @endif

                                        <hr>


                                        <div class="accordion" id="accordionImportantInfo">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                        aria-expanded="true" aria-controls="collapseOne">
                                                        Important Communication :
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse"
                                                    aria-labelledby="headingOne" data-bs-parent="#accordionImportantInfo">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Notice Document</th>
                                                                        <th>Notice Type</th>
                                                                        <th>Notice Date</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if ($case->notices->isNotEmpty())
                                                                        @foreach ($case->notices->filter(fn($n) => $n->notice_type >= 1 && $n->notice_type <= 10)->sortBy('notice_type') as $notice)
                                                                            <tr>
                                                                                <td class="text-start ps-3">
                                                                                    <a class="text-decoration-none text-dark"
                                                                                        href="{{ asset('storage/' . $notice->notice) }}"
                                                                                        target="_blank">
                                                                                        <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                            height="30"
                                                                                            alt="PDF File" />
                                                                                        View
                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    @switch($notice->notice_type)
                                                                                        @case(1)
                                                                                            Notice 1
                                                                                        @break

                                                                                        @case(2)
                                                                                            Notice 1A
                                                                                        @break

                                                                                        @case(3)
                                                                                            Notice 1B
                                                                                        @break

                                                                                        @case(4)
                                                                                            Notice 2B
                                                                                        @break

                                                                                        @case(5)
                                                                                            Notice 3A
                                                                                        @break

                                                                                        @case(6)
                                                                                            Notice 3B
                                                                                        @break

                                                                                        @case(7)
                                                                                            Notice 3C
                                                                                        @break

                                                                                        @case(8)
                                                                                            Notice 3D
                                                                                        @break

                                                                                        @case(9)
                                                                                            Notice 4A
                                                                                        @break

                                                                                        @case(10)
                                                                                            Notice 5A
                                                                                        @break

                                                                                        @default
                                                                                            Unknown
                                                                                    @endswitch
                                                                                </td>
                                                                                <td>{{ $notice->notice_date }}</td>
                                                                                <td class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        Email:
                                                                                        <span
                                                                                            class="{{ $notice->email_status ? 'text-success' : 'text-danger' }}">
                                                                                            {{ $notice->email_status ? 'Sent' : 'Not Sent' }}
                                                                                        </span>
                                                                                    </div>
                                                                                    <div>
                                                                                        Whatsapp:
                                                                                        <span
                                                                                            class="{{ $notice->whatsapp_status ? 'text-success' : 'text-danger' }}">
                                                                                            {{ $notice->whatsapp_status ? 'Seen' : 'UnSeen' }}
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <small
                                                                                    class="text-center justify-content-center">
                                                                                    <p class="mb-0 text-danger">No Notice
                                                                                        Found.</p>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="accordion" id="accordionHearingDate">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                        aria-expanded="true" aria-controls="collapseTwo">
                                                        Last Hearing :
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwo" data-bs-parent="#accordionHearingDate">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Hearing Date/Time</th>
                                                                        <th>Hearing Type</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if ($closedHearings->isNotEmpty())
                                                                        @foreach ($closedHearings as $closedHearing)
                                                                            @php
                                                                                $hearingTypes = [
                                                                                    1 => 'First Hearing',
                                                                                    2 => 'Second Hearing',
                                                                                    3 => 'Final Hearing',
                                                                                ];
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{ $closedHearing->date }}/{{ $closedHearing->time }}
                                                                                </td>
                                                                                <td>
                                                                                    {{ $hearingTypes[$closedHearing->hearing_type] ?? 'Unknown' }}
                                                                                </td>
                                                                                <td>{{ $closedHearing->status ? 'Active' : 'Closed' }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="3">
                                                                                <small
                                                                                    class="text-center justify-content-center">
                                                                                    <p class="mb-0 text-danger">No Hearing
                                                                                        Found.</p>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="accordion" id="accordionNextDate">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingThree">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                        aria-expanded="true" aria-controls="collapseThree">
                                                        Next Hearing :
                                                    </button>
                                                </h2>
                                                <div id="collapseThree" class="accordion-collapse collapse"
                                                    aria-labelledby="headingThree" data-bs-parent="#accordionNextDate">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Hearing Date/Time</th>
                                                                        <th>Hearing Type</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if ($upcomingHearings->isNotEmpty())
                                                                        @foreach ($upcomingHearings as $upcomingHearing)
                                                                            @php
                                                                                $hearingTypes = [
                                                                                    1 => 'First Hearing',
                                                                                    2 => 'Second Hearing',
                                                                                    3 => 'Final Hearing',
                                                                                ];
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{ $upcomingHearing->date }}/{{ $upcomingHearing->time }}
                                                                                </td>
                                                                                <td>
                                                                                    {{ $hearingTypes[$upcomingHearing->hearing_type] ?? 'Unknown' }}
                                                                                </td>
                                                                                <td>{{ $upcomingHearing->status ? 'Active' : 'Upcoming' }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="3">
                                                                                <small
                                                                                    class="text-center justify-content-center">
                                                                                    <p class="mb-0 text-danger">No Hearing
                                                                                        Found.</p>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="accordion" id="accordionStage">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingFour">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                        aria-expanded="true" aria-controls="collapseFour">
                                                        Stage :
                                                    </button>
                                                </h2>
                                                <div id="collapseFour" class="accordion-collapse collapse"
                                                    aria-labelledby="headingFour" data-bs-parent="#accordionStage">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Stage Document</th>
                                                                        <th>Stage Type</th>
                                                                        <th>Stage Date</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if ($case->notices->isNotEmpty())
                                                                        @foreach ($case->notices->filter(fn($n) => $n->notice_type >= 11 && $n->notice_type <= 25)->sortBy('notice_type') as $notice)
                                                                            <tr>
                                                                                <td class="text-start ps-3">
                                                                                    <a class="text-decoration-none text-dark"
                                                                                        href="{{ asset('storage/' . $notice->notice) }}"
                                                                                        target="_blank">
                                                                                        <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                            height="30"
                                                                                            alt="PDF File" />
                                                                                        View
                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    @switch($notice->notice_type)
                                                                                        @case(11)
                                                                                            Stage 1
                                                                                        @break

                                                                                        @case(12)
                                                                                            Stage 2
                                                                                        @break

                                                                                        @case(13)
                                                                                            Stage 3
                                                                                        @break

                                                                                        @case(14)
                                                                                            Stage 4
                                                                                        @break

                                                                                        @case(15)
                                                                                            Stage 5
                                                                                        @break

                                                                                        @case(16)
                                                                                            Stage 6
                                                                                        @break

                                                                                        @case(17)
                                                                                            Stage 7
                                                                                        @break

                                                                                        @case(18)
                                                                                            Stage 8
                                                                                        @break

                                                                                        @case(19)
                                                                                            Stage 9
                                                                                        @break

                                                                                        @case(20)
                                                                                            Stage 10
                                                                                        @break

                                                                                        @case(21)
                                                                                            Stage 11
                                                                                        @break

                                                                                        @case(22)
                                                                                            Stage 12
                                                                                        @break

                                                                                        @case(23)
                                                                                            Stage 13
                                                                                        @break

                                                                                        @case(24)
                                                                                            Stage 14
                                                                                        @break

                                                                                        @case(25)
                                                                                            Stage 15
                                                                                        @break

                                                                                        @default
                                                                                            Unknown
                                                                                    @endswitch
                                                                                </td>
                                                                                <td>{{ $notice->notice_date }}</td>
                                                                                <td class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        Email:
                                                                                        <span
                                                                                            class="{{ $notice->email_status ? 'text-success' : 'text-danger' }}">
                                                                                            {{ $notice->email_status ? 'Sent' : 'Not Sent' }}
                                                                                        </span>
                                                                                    </div>
                                                                                    <div>
                                                                                        Whatsapp:
                                                                                        <span
                                                                                            class="{{ $notice->whatsapp_status ? 'text-success' : 'text-danger' }}">
                                                                                            {{ $notice->whatsapp_status ? 'Seen' : 'UnSeen' }}
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="4">
                                                                                <small
                                                                                    class="text-center justify-content-center">
                                                                                    <p class="mb-0 text-danger">No Stage
                                                                                        Found.
                                                                                    </p>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <hr>

                                    <div class="accordion" id="accordionIntrimOrder">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFive">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseFive" aria-expanded="true"
                                                    aria-controls="collapseFive">
                                                    Interim Order : If Any
                                                </button>
                                            </h2>
                                            <div id="collapseFive" class="accordion-collapse collapse"
                                                aria-labelledby="headingFive" data-bs-parent="#accordionIntrimOrder">
                                                <div class="accordion-body p-0">
                                                    <div class="custom-table-container">
                                                        <table class="table custom-table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-start ps-3">Order Type</th>
                                                                    <th>Date of Order</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>Service of summons on defendant</td>
                                                                    <td>------</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Appearance of parties</td>
                                                                    <td>------</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Examination of parties</td>
                                                                    <td>------</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Argument</td>
                                                                    <td>------</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Judgment</td>
                                                                    <td>------</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="accordion" id="accordionToOrganization">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingSeven">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseSeven" aria-expanded="true"
                                                    aria-controls="collapseSeven">
                                                    Assignment Notice To Organization :
                                                </button>
                                            </h2>
                                            <div id="collapseSeven" class="accordion-collapse collapse"
                                                aria-labelledby="headingSeven" data-bs-parent="#accordionToOrganization">
                                                <div class="accordion-body p-0">
                                                    <div class="custom-table-container">
                                                        <table class="table custom-table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-start ps-3">Notice</th>
                                                                    <th>Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="accordion" id="accordionByOrganization">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingSix">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseSix" aria-expanded="true"
                                                    aria-controls="collapseSix">
                                                    Notice Given By Organization :
                                                </button>
                                            </h2>
                                            <div id="collapseSix" class="accordion-collapse collapse"
                                                aria-labelledby="headingSix" data-bs-parent="#accordionByOrganization">
                                                <div class="accordion-body p-0">
                                                    <div class="custom-table-container">
                                                        <table class="table custom-table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-start ps-3">Notice</th>
                                                                    <th>Date</th>
                                                                    <th>Summary</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    <td>----------</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    <td>----------</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    <td>----------</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    <td>----------</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                        <hr>

                                        <div class="accordion" id="accordionCaseDocuments">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingEight">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseEight"
                                                        aria-expanded="true" aria-controls="collapseEight">
                                                        Case Documents :
                                                    </button>
                                                </h2>
                                                <div id="collapseEight"
                                                    class="accordion-collapse collapse overflow-hidden"
                                                    aria-labelledby="headingEight"
                                                    data-bs-parent="#accordionCaseDocuments">
                                                    <div class="accordion-body">
                                                        <div class="custom-table-container">
                                                            <div class="row gx-5 gy-3">
                                                                @php
                                                                    $documents = [
                                                                        'application_form' => $case->application_form,
                                                                        'account_statement' => $case->account_statement,
                                                                        'foreclosure_statement' =>
                                                                            $case->foreclosure_statement,
                                                                        'loan_agreement' => $case->loan_agreement,
                                                                        'other_document' => $case->other_document,
                                                                    ];
                                                                @endphp

                                                                @foreach ($documents as $label => $filename)
                                                                    @php
                                                                        $filePath = $filename
                                                                            ? asset('storage/' . $filename)
                                                                            : null;
                                                                        $extension = $filename
                                                                            ? strtolower(
                                                                                pathinfo($filename, PATHINFO_EXTENSION),
                                                                            )
                                                                            : null;
                                                                    @endphp

                                                                    <div class="col-md-4 col-6">
                                                                        <p class="case-title">
                                                                            {{ str_replace('_', ' ', ucfirst($label)) }}
                                                                        </p>
                                                                        <p class="case-text">
                                                                            @if ($filePath && $extension)
                                                                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                                    <img src="{{ $filePath }}"
                                                                                        class="img-thumbnail"
                                                                                        width="100"
                                                                                        alt="Image Preview" />
                                                                                @elseif ($extension === 'pdf')
                                                                                    <a class="text-decoration-none case-text"
                                                                                        style="font-size: 13px"
                                                                                        href="{{ $filePath }}"
                                                                                        target="_blank">
                                                                                        <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                            height="30"
                                                                                            alt="PDF File" />
                                                                                        View PDF
                                                                                    </a>
                                                                                @elseif (in_array($extension, ['doc', 'docx']))
                                                                                    <a class="text-decoration-none case-text"
                                                                                        style="font-size: 13px"
                                                                                        href="{{ $filePath }}"
                                                                                        target="_blank">
                                                                                        <img src="{{ asset('public/assets/img/doc.png') }}"
                                                                                            height="30"
                                                                                            alt="DOC File" />
                                                                                        View Document
                                                                                    </a>
                                                                                @else
                                                                                    <a class="text-decoration-none case-text"
                                                                                        style="font-size: 13px"
                                                                                        href="{{ $filePath }}"
                                                                                        target="_blank">Download File</a>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">No file
                                                                                    uploaded</span>
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                @endforeach

                                                                <hr>

                                                                <div
                                                                    class="col-12 d-flex justify-content-between item-align-self my-0">
                                                                    <p class="case-title my-auto">Claimant Petition Filed
                                                                        Or
                                                                        Not</p>
                                                                    @php
                                                                        $petitionPath = storage_path(
                                                                            'app/public/' . $case->claim_petition,
                                                                        );
                                                                    @endphp

                                                                    @if (!empty($case->claim_petition) && file_exists($petitionPath))
                                                                        <a class="text-decoration-none case-text"
                                                                            style="font-size: 13px"
                                                                            href="{{ asset('storage/' . $case->claim_petition) }}"
                                                                            target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" />
                                                                            View
                                                                        </a>
                                                                    @else
                                                                        <small class="text-center justify-content-center">
                                                                            <p class="mb-0 text-danger">No Petition Found.
                                                                            </p>
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                                {{-- 
                                                            <hr>
                                                            <div
                                                                class="col-12 d-flex justify-content-between item-align-self my-0">
                                                                <p class="case-title my-auto">Evidence Filed Or Not</p>
                                                                <a class="text-decoration-none case-text"
                                                                    style="font-size: 13px" href="#"
                                                                    target="_blank">
                                                                    <img src="{{ asset('assets/img/pdf.png') }}"
                                                                        height="30" alt="PDF File" />
                                                                    View PDF
                                                                </a>
                                                            </div>
                                                            <hr>
                                                            <div
                                                                class="col-12 d-flex justify-content-between item-align-self my-0">
                                                                <p class="case-title my-auto">Award Recieved Or Not</p>
                                                                <a class="text-decoration-none case-text"
                                                                    style="font-size: 13px" href="#"
                                                                    target="_blank">
                                                                    <img src="{{ asset('assets/img/pdf.png') }}"
                                                                        height="30" alt="PDF File" />
                                                                    View PDF
                                                                </a>
                                                            </div>
                                                            <hr>
                                                            <div
                                                                class="col-12 d-flex justify-content-between item-align-self my-0">
                                                                <p class="case-title my-auto">Interim Order</p>
                                                                <a class="text-decoration-none case-text"
                                                                    style="font-size: 13px" href="#"
                                                                    target="_blank">
                                                                    <img src="{{ asset('assets/img/pdf.png') }}"
                                                                        height="30" alt="PDF File" />
                                                                    View PDF
                                                                </a>
                                                            </div> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/custom-methods.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/waves.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript"></script>
@endsection
