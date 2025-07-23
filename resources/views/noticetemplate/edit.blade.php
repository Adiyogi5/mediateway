@extends('layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Notices :: Edit </h5>
                </div>
                <div class="col-auto d-flex item-align-self ms-auto">
                    <div class="form-group my-3">
                        <a type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="fa fa-star"></i> View Variables</a>
                    </div>
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 my-auto ms-2" role="tablist">
                        <a href="{{ route('noticetemplate') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST" action="{{ route('noticetemplate.edit', $noticetemplate['id']) }}"
                enctype='multipart/form-data'>
                @csrf
                <div class="row">
                    <div class="col-md-6 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="name">Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $noticetemplate['name']) }}"
                                id="name" placeholder="Enter Name">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="subject">Subject <span class="required">*</span></label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject', $noticetemplate['subject']) }}"
                                id="subject" placeholder="Enter Subject">
                                @error('subject')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="email_content">Email Content <span class="required">*</span></label>
                            <textarea id="email_content" name="email_content" class="form-control" id="email_content">{{ old('email_content', $noticetemplate['email_content']) }}</textarea>
                            @error('email_content')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="notice_format">Notice Format <span class="required">*</span></label>
                            <textarea class="form-control" id="notice_format" name="notice_format">{{ old('notice_format', $noticetemplate['notice_format']) }}</textarea>
                            @error('notice_format')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mt-3 d-flex justify-content-start">
                    <button class="btn btn-secondary submitbtn" type="submit">Update</button>
                </div>
            </form>
            
            <div class="row">
                <div class="col-md-12">
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header text-center justify-content-center">
                                    <h4 class="modal-title text-dark fw-bold">Please Add Variables For Notice wise</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row list-styled px-3" id="variableList">
                                        
                                        @php
                                            $arbitratorVariable = "{{ARBITRATOR'S NAME}}";
                                            $caseManagerVariable = "{{CASE MANAGER'S NAME}}";
                                            $PhoneNoVariable = "{{PHONE NUMBER}}";
                                            $EmailVariable = "{{EMAIL ADDRESS}}";

                                            $caseRegistrationNoVariable = "{{CASE REGISTRATION NUMBER}}";
                                            $claimantOrganizationNameVariable = "{{BANK/ORGANISATION/CLAIMANT NAME}}";
                                            $claimantOrganizationAddressVariable = "{{BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS}}";

                                            $customerNameVariable = "{{CUSTOMER NAME}}";
                                            $customerAddressVariable = "{{CUSTOMER ADDRESS}}";
                                            $customerMobileNoVariable = "{{CUSTOMER MOBILE NO}}";
                                            $customerEmailVariable = "{{CUSTOMER MAIL ID}}";

                                            $officernameVariable = "{{CLAIM SIGNATORY/AUTHORISED OFFICER NAME}}";
                                            $officerMobileNoVariable = "{{CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO}}";
                                            $officesMailVariable = "{{CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID}}";

                                            $loanNoVariable = "{{LOAN NO}}";
                                            $agreementDateVariable = "{{AGREEMENT DATE}}";
                                            $financeAmountVariable = "{{FINANCE AMOUNT}}";
                                            $foreclosureVariable = "{{FORECLOSURE AMOUNT}}";
                                            $foreclosuredateVariable = "{{FORECLOSURE DATE}}";

                                            $arbitratorsNameVariable = "{{ARBITRATOR'S NAME}}";
                                            $arbitratorsSpecializationVariable = "{{ARBITRATOR'S SPECIALIZATION}}";
                                            $arbitratorsAddressVariable = "{{ARBITRATOR'S ADDRESS}}";

                                            $firstarbitratorsNameVariable = "{{FIRST ARBITRATOR'S NAME}}";
                                            $firstarbitratorsSpecializationVariable = "{{FIRST ARBITRATOR'S SPECIALIZATION}}";
                                            $firstarbitratorsAddressVariable = "{{FIRST ARBITRATOR'S ADDRESS}}";

                                            $secondarbitratorsNameVariable = "{{SECOND ARBITRATOR'S NAME}}";
                                            $secondarbitratorsSpecializationVariable = "{{SECOND ARBITRATOR'S SPECIALIZATION}}";
                                            $secondarbitratorsAddressVariable = "{{SECOND ARBITRATOR'S ADDRESS}}";

                                            $thirdarbitratorsNameVariable = "{{THIRD ARBITRATOR'S NAME}}";
                                            $thirdarbitratorsSpecializationVariable = "{{THIRD ARBITRATOR'S SPECIALIZATION}}";
                                            $thirdarbitratorsAddressVariable = "{{THIRD ARBITRATOR'S ADDRESS}}";

                                            $productVariable = "{{PRODUCT}}";
                                            $tenureVariable = "{{TENURE}}";
                                            $arbitrationClauseNoVariable = "{{ARBITRATION CLAUSE NO}}";
                                            $arbitrationDateVariable = "{{ARBITRATION DATE}}";

                                            $guarantor1nameVariable = "{{GUARANTOR 1 NAME}}";
                                            $guarantor1mobilenoVariable = "{{GUARANTOR 1 MOBILE NO}}";
                                            $guarantor1emailidVariable = "{{GUARANTOR 1 EMAIL ID}}";
                                            $guarantor1addressVariable = "{{GUARANTOR 1 ADDRESS}}";
                                            $guarantor1fathernameVariable = "{{GUARANTOR 1 FATHER NAME}}";

                                            $guarantor2nameVariable = "{{GUARANTOR 2 NAME}}";
                                            $guarantor2mobilenoVariable = "{{GUARANTOR 2 MOBILE NO}}";
                                            $guarantor2emailidVariable = "{{GUARANTOR 2 EMAIL ID}}";
                                            $guarantor2addressVariable = "{{GUARANTOR 2 ADDRESS}}";
                                            $guarantor2fathernameVariable = "{{GUARANTOR 2 FATHER NAME}}";

                                            $guarantor3nameVariable = "{{GUARANTOR 3 NAME}}";
                                            $guarantor3mobilenoVariable = "{{GUARANTOR 3 MOBILE NO}}";
                                            $guarantor3emailidVariable = "{{GUARANTOR 3 EMAIL ID}}";
                                            $guarantor3addressVariable = "{{GUARANTOR 3 ADDRESS}}";
                                            $guarantor3fathernameVariable = "{{GUARANTOR 3 FATHER NAME}}";

                                            $guarantor4nameVariable = "{{GUARANTOR 4 NAME}}";
                                            $guarantor4mobilenoVariable = "{{GUARANTOR 4 MOBILE NO}}";
                                            $guarantor4emailidVariable = "{{GUARANTOR 4 EMAIL ID}}";
                                            $guarantor4addressVariable = "{{GUARANTOR 4 ADDRESS}}";
                                            $guarantor4fathernameVariable = "{{GUARANTOR 4 FATHER NAME}}";

                                            $guarantor5nameVariable = "{{GUARANTOR 5 NAME}}";
                                            $guarantor5mobilenoVariable = "{{GUARANTOR 5 MOBILE NO}}";
                                            $guarantor5emailidVariable = "{{GUARANTOR 5 EMAIL ID}}";
                                            $guarantor5addressVariable = "{{GUARANTOR 5 ADDRESS}}";
                                            $guarantor5fathernameVariable = "{{GUARANTOR 5 FATHER NAME}}";

                                            $guarantor6nameVariable = "{{GUARANTOR 6 NAME}}";
                                            $guarantor6mobilenoVariable = "{{GUARANTOR 6 MOBILE NO}}";
                                            $guarantor6emailidVariable = "{{GUARANTOR 6 EMAIL ID}}";
                                            $guarantor6addressVariable = "{{GUARANTOR 6 ADDRESS}}";
                                            $guarantor6fathernameVariable = "{{GUARANTOR 6 FATHER NAME}}";

                                            $guarantor7nameVariable = "{{GUARANTOR 7 NAME}}";
                                            $guarantor7mobilenoVariable = "{{GUARANTOR 7 MOBILE NO}}";
                                            $guarantor7emailidVariable = "{{GUARANTOR 7 EMAIL ID}}";
                                            $guarantor7addressVariable = "{{GUARANTOR 7 ADDRESS}}";
                                            $guarantor7fathernameVariable = "{{GUARANTOR 7 FATHER NAME}}";

                                            $todayDateVariable = "{{DATE}}";
                                            $loanRecallVariable = "{{STAGE 1 NOTICE DATE}}";
                                            $stage1ANoticeDateVariable = "{{STAGE 1A NOTICE DATE}}";
                                            $stage1BNoticeDateVariable = "{{STAGE 1B NOTICE DATE}}";
                                            $stage2bNoticeVariable = "{{STAGE 2B NOTICE DATE}}";
                                            $appointmentofArbitratorVariable = "{{STAGE 3A NOTICE DATE}}";
                                            $stage3bNoticeVariable = "{{STAGE 3B NOTICE DATE}}";
                                            $stage3cNoticeVariable = "{{STAGE 3C NOTICE DATE}}";

                                            $stage3cpendingarbitrationclaimVariable = "{{TOTAL NUMBER OF PENDING ARBITRATION CLAIMS}}";
                                        @endphp

                                        <div class="col-12 text-center justify-content-center">
                                            <h6><b><span class="text-success">Common Variables for all Notice Types</span></b></h6>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $caseRegistrationNoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $caseRegistrationNoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $loanNoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $loanNoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $agreementDateVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $agreementDateVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $financeAmountVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $financeAmountVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $productVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $productVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $tenureVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $tenureVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $foreclosureVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $foreclosureVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $foreclosuredateVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $foreclosuredateVariable }}">Copy</button>
                                        </div>
                                         <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $officernameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $officernameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $officerMobileNoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $officerMobileNoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $officesMailVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $officesMailVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $claimantOrganizationNameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $claimantOrganizationNameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $claimantOrganizationAddressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $claimantOrganizationAddressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $customerNameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $customerNameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $customerAddressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $customerAddressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $customerMobileNoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $customerMobileNoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $customerEmailVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $customerEmailVariable }}">Copy</button>
                                        </div>
                                        
                                        <hr class="mt-2">

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $todayDateVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $todayDateVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $arbitrationClauseNoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $arbitrationClauseNoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $arbitrationDateVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $arbitrationDateVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $loanRecallVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $loanRecallVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $stage1ANoticeDateVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $stage1ANoticeDateVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $stage1BNoticeDateVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $stage1BNoticeDateVariable }}">Copy</button>
                                        </div>
                                        
                                        <hr class="mt-2">
                                        
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor1nameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor1nameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor1mobilenoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor1mobilenoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor1emailidVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor1emailidVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor1addressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor1addressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor1fathernameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor1fathernameVariable }}">Copy</button>
                                        </div>
                                        
                                        <hr class="mt-2">

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor2nameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor2nameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor2mobilenoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor2mobilenoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor2emailidVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor2emailidVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor2addressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor2addressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor2fathernameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor2fathernameVariable }}">Copy</button>
                                        </div>
                                        
                                        <hr class="mt-2">

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor3nameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor3nameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor3mobilenoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor3mobilenoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor3emailidVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor3emailidVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor3addressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor3addressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor3fathernameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor3fathernameVariable }}">Copy</button>
                                        </div>
                                        
                                        <hr class="mt-2">

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor4nameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor4nameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor4mobilenoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor4mobilenoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor4emailidVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor4emailidVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor4addressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor4addressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor4fathernameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor4fathernameVariable }}">Copy</button>
                                        </div>
                                        
                                        <hr class="mt-2">

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor5nameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor5nameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor5mobilenoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor5mobilenoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor5emailidVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor5emailidVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor5addressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor5addressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor5fathernameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor5fathernameVariable }}">Copy</button>
                                        </div>
                                        
                                        <hr class="mt-2">

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor6nameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor6nameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor6mobilenoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor6mobilenoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor6emailidVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor6emailidVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor6addressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor6addressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor6fathernameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor6fathernameVariable }}">Copy</button>
                                        </div>

                                        <hr class="mt-2">

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor7nameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor7nameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor7mobilenoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor7mobilenoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor7emailidVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor7emailidVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor7addressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor7addressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $guarantor7fathernameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $guarantor7fathernameVariable }}">Copy</button>
                                        </div>


                                        <hr class="mt-2">

                                        <div class="col-12 text-center justify-content-center">
                                            <h6><b><span class="text-success">Notice Type : 2B </span>- Appointment Of Case Manager</b></h6>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $arbitratorVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $arbitratorVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $caseManagerVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $caseManagerVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $PhoneNoVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $PhoneNoVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $EmailVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $EmailVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $stage2bNoticeVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $stage2bNoticeVariable }}">Copy</button>
                                        </div>

                                        <hr class="mt-2">

                                        <div class="col-12 text-center justify-content-center">
                                            <h6><b><span class="text-success">Notice Type : 3A </span>- Final Appointment Of Arbitrator</b></h6>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $firstarbitratorsNameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $firstarbitratorsNameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $firstarbitratorsSpecializationVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $firstarbitratorsSpecializationVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $firstarbitratorsAddressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $firstarbitratorsAddressVariable }}">Copy</button>
                                        </div>

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $secondarbitratorsNameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $secondarbitratorsNameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $secondarbitratorsSpecializationVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $secondarbitratorsSpecializationVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $secondarbitratorsAddressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $secondarbitratorsAddressVariable }}">Copy</button>
                                        </div>

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $thirdarbitratorsNameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $thirdarbitratorsNameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $thirdarbitratorsSpecializationVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $thirdarbitratorsSpecializationVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $thirdarbitratorsAddressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $thirdarbitratorsAddressVariable }}">Copy</button>
                                        </div>

                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $appointmentofArbitratorVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $appointmentofArbitratorVariable }}">Copy</button>
                                        </div>

                                        <hr class="mt-2">

                                        <div class="col-12 text-center justify-content-center">
                                            <h6><b><span class="text-success">Notice Type : 3B </span>- NOTICE Appointment of an Arbitrator doc</b></h6>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $arbitratorsNameVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $arbitratorsNameVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $arbitratorsSpecializationVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $arbitratorsSpecializationVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $arbitratorsAddressVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $arbitratorsAddressVariable }}">Copy</button>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $stage3bNoticeVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $stage3bNoticeVariable }}">Copy</button>
                                        </div>
                                        
                                        <hr class="mt-2">

                                        <div class="col-12 text-center justify-content-center">
                                            <h6><b><span class="text-success">Notice Type : 3C </span>- NOTICE Acceptance and Disclosure</b></h6>
                                        </div>
                                        <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $stage3cNoticeVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $stage3cNoticeVariable }}">Copy</button>
                                        </div>
                                         <div class="col-md-6 col-12 d-flex align-items-center justify-content-between my-1">
                                            <span class="variable-text">{{ $stage3cpendingarbitrationclaimVariable }}</span>
                                            <button class="btn btn-sm btn-outline-secondary copy-btn py-1" data-variable="{{ $stage3cpendingarbitrationclaimVariable }}">Copy</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>                        
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyButtons = document.querySelectorAll('.copy-btn');
        
        copyButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const textToCopy = this.getAttribute('data-variable');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    this.innerText = 'Copied!';
                    setTimeout(() => {
                        this.innerText = 'Copy';
                    }, 1500);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            });
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#notice_format').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview', 'help']],
            ]
        });
        let buttons = $('.note-editor button[data-toggle="dropdown"]');
        buttons.each((key, value) => {
            $(value).on('click', function (e) {
                $(this).attr('data-bs-toggle', 'dropdown')
            })
        })
    })
        $("#edit").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                subject: {
                    required: true,
                    minlength: 2,
                    maxlength: 200
                },
                email_content: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                },
                notice_format: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                },
               
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                subject: {
                    required: "Please enter subject",
                },
                email_content: {
                    required: "Please enter Email Content",
                },
                notice_format: {
                    required: "Please enter Order Sheet",
                },
            },
        });
    </script>
@endsection
