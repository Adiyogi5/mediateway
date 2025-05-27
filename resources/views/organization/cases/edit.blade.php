@extends('layouts.front')

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
                <div class="card mb-3 card-inner">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h6 class="mb-0" data-anchor="data-anchor">Organization Case File :: Upload Documents </h6>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                                    <a href="{{ route('organization.cases.filecaseview') }}"
                                        class="btn btn-outline-secondary"> <i class="fa fa-arrow-left me-1"></i> Go
                                        Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form class="row" id="uploadnoticeView" method="POST" action="{{ route('organization.cases.filecaseview.store', $caseviewData['id']) }}" enctype="multipart/form-data">
                            @csrf
                            {{-- ####### Notices - 1, 1A, 1B ######## --}}
                            <h4 class="livemeetingcard-heading text-center justify-content-center"
                            style="background-color: black;color: white;padding: 5px;border-radius: 8px">Upload Notices - 1, 1A and 1B</h4>

                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label">1. Notice - 1</label><br>
                                @if(isset($noticeType1))
                                <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                    href="{{ asset('storage/' . $noticeType1->notice) }}" target="_blank">
                                    <img src="{{ asset('public/assets/img/pdf.png') }}" height="30"
                                        alt="PDF File" />
                                    View Notice PDF
                                </a>
                                @else
                                <label for="notice_first" class="custom-file-upload">
                                    <span style="font-weight: 500;" id="file-label-notice_first">
                                        <span style="border:2px solid black; border-radius:50%; padding: 1px;">➕</span>
                                        Attach PDF
                                    </span>
                                </label>
                                <input type="file" id="notice_first" name="notice_first" accept="application/pdf" hidden/>
                                    @error('notice_first')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>
                        
                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label">2. Notice - 1A</label><br>
                                @if(isset($noticeType2))
                                    <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                                    href="{{ asset('storage/' . $noticeType2->notice) }}" target="_blank">
                                                    <img src="{{ asset('public/assets/img/pdf.png') }}" height="30"
                                                        alt="PDF File" />
                                                    View Notice PDF
                                                </a>
                                @else
                                <label for="notice_second" class="custom-file-upload">
                                    <span style="font-weight: 500;" id="file-label-notice_second">
                                        <span style="border:2px solid black; border-radius:50%; padding: 1px;">➕</span>
                                        Attach PDF
                                    </span>
                                </label>
                                <input type="file" id="notice_second" name="notice_second" accept="application/pdf" hidden/>
                                    @error('notice_second')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label">3. Notice - 1B</label><br>
                                @if(isset($noticeType3))
                                    <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                                    href="{{ asset('storage/' . $noticeType3->notice) }}" target="_blank">
                                                    <img src="{{ asset('public/assets/img/pdf.png') }}" height="30"
                                                        alt="PDF File" />
                                                    View Notice PDF
                                                </a>
                                @else
                                <label for="notice_third" class="custom-file-upload">
                                    <span style="font-weight: 500;" id="file-label-notice_third">
                                        <span style="border:2px solid black; border-radius:50%; padding: 1px;">➕</span>
                                        Attach PDF
                                    </span>
                                </label>
                                <input type="file" id="notice_third" name="notice_third" accept="application/pdf" hidden/>
                                    @error('notice_third')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>
                        
                            @if(!isset($noticeType1) || !isset($noticeType2) || !isset($noticeType3))
                                <div class="col-12 mb-3">
                                <button type="submit" class="btn btn-md btn-primary py-1 px-3">Save Notices</button>
                                </div>
                            @endif
                        </form>
                        
                        {{-- ####### Claim Petition ######## --}}
                        <div class="row mb-3">
                            <h4 class="livemeetingcard-heading text-center justify-content-center"
                            style="background-color: black;color: white;padding: 5px;border-radius: 8px">Filed Claim Petition</h4>
                            <label class="form-label">Claim Petition</label><br>
                            @if(isset($caseviewData['claim_petition']))
                                <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                                href="{{ asset('storage/' . $caseviewData['claim_petition']) }}" target="_blank">
                                                <img src="{{ asset('public/assets/img/pdf.png') }}" height="30"
                                                    alt="PDF File" />
                                                View Notice PDF
                                            </a>
                            @else
                                <small>No Claim Petition Filed Yet</small>
                            @endif
                        </div>
                        
                        {{-- ####### Other Documents ######## --}}
                        <form class="row" id="editcaseview" method="POST"
                            action="{{ route('organization.cases.filecaseview.update', $caseviewData['id']) }}"
                            enctype='multipart/form-data'>
                            @csrf
                            
                            <h4 class="livemeetingcard-heading text-center justify-content-center"
                            style="background-color: black;color: white;padding: 5px;border-radius: 8px">Upload Documents</h4>

                            {{-- claimant details  --}}
                            {{-- <div class="col-12 text-center justify-content-center">
                                <h6 class="border-bottom bg-dark text-white p-2 border-2 my-3">Claimant Details</h6>
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <label class="form-label" for="claimant_first_name">First Name <span class="error-text">
                                        *</span></label>
                                <input type="text" id="claimant_first_name" name="claimant_first_name"
                                    class="form-control"
                                    value="{{ old('claimant_first_name', $caseviewData['claimant_first_name']) }}">
                                @error('claimant_first_name')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <label class="form-label" for="claimant_middle_name">Middle Name</label>
                                <input type="text" id="claimant_middle_name" name="claimant_middle_name"
                                    class="form-control"
                                    value="{{ old('claimant_middle_name', $caseviewData['claimant_middle_name']) }}">
                                @error('claimant_middle_name')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <label class="form-label" for="claimant_last_name">Last Name</label>
                                <input type="text" id="claimant_last_name" name="claimant_last_name" class="form-control"
                                    value="{{ old('claimant_last_name', $caseviewData['claimant_last_name']) }}">
                                @error('claimant_last_name')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="claimant_mobile">Mobile Number <span class="error-text">
                                        *</span></label>
                                <input type="text" id="claimant_mobile" name="claimant_mobile" class="form-control"
                                    value="{{ old('claimant_mobile', $caseviewData['claimant_mobile']) }}">
                                @error('claimant_mobile')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="claimant_email">Email</label>
                                <input type="email" id="claimant_email" name="claimant_email" class="form-control"
                                    value="{{ old('claimant_email', $caseviewData['claimant_email']) }}">
                                @error('claimant_email')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="claimant_address1">Address Line 1 <span class="error-text">
                                        *</span></label>
                                <input type="text" id="claimant_address1" name="claimant_address1" class="form-control"
                                    value="{{ old('claimant_address1', $caseviewData['claimant_address1']) }}">
                                @error('claimant_address1')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="claimant_address2">Address Line 2</label>
                                <input type="text" id="claimant_address2" name="claimant_address2" class="form-control"
                                    value="{{ old('claimant_address2', $caseviewData['claimant_address2']) }}">
                                @error('claimant_address2')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-3 col-12 mb-3">
                                <label class="form-label" for="claimant_address_type">Address Type <span class="error-text">
                                        *</span></label>
                                <select name="claimant_address_type" id="claimant_address_type"
                                    class="form-control form-select">
                                    <option value="">Select Address Type</option>
                                    @foreach (config('constant.claimant_address_type', []) as $key => $value)
                                        <option value="{{ $key }}" @selected(old('claimant_address_type', $caseviewData['claimant_address_type']) == $key)>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('claimant_address_type')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 col-12 mb-3">
                                <label class="form-label" for="claimant_state_id">State <span class="error-text">
                                        *</span></label>
                                <select name="claimant_state_id" onchange="getCity(this.value)" class="form-select"
                                    id="claimant_state_id">
                                    <option value="">Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}" @selected(old('claimant_state_id', $caseviewData['claimant_state_id']) == $state->id)>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('claimant_state_id')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 col-12 mb-3">
                                <label class="form-label" for="claimant_city_id">City <span class="error-text">
                                        *</span></label>
                                <select name="claimant_city_id" class="form-select" id="claimant_city_id">
                                    <option value="">Select City</option>
                                </select>
                                @error('claimant_city_id')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 col-12 mb-3">
                                <label class="form-label" for="claimant_pincode">Pincode <span class="error-text">
                                        *</span></label>
                                <input type="text" id="claimant_pincode" name="claimant_pincode" class="form-control"
                                    value="{{ old('claimant_pincode', $caseviewData['claimant_pincode']) }}">
                                @error('claimant_pincode')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div> --}}


                            {{-- Respodent Data  --}}
                            {{-- <div class="col-12 text-center justify-content-center">
                                <h6 class="border-bottom bg-dark text-white p-2 border-2 my-3">Respondent Details</h6>
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <label class="form-label" for="respondent_first_name">First Name <span
                                        class="error-text"> *</span></label>
                                <input type="text" id="respondent_first_name" name="respondent_first_name"
                                    class="form-control"
                                    value="{{ old('respondent_first_name', $caseviewData['respondent_first_name']) }}">
                                @error('respondent_first_name')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <label class="form-label" for="respondent_middle_name">Middle Name</label>
                                <input type="text" id="respondent_middle_name" name="respondent_middle_name"
                                    class="form-control"
                                    value="{{ old('respondent_middle_name', $caseviewData['respondent_middle_name']) }}">
                                @error('respondent_middle_name')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <label class="form-label" for="respondent_last_name">Last Name</label>
                                <input type="text" id="respondent_last_name" name="respondent_last_name"
                                    class="form-control"
                                    value="{{ old('respondent_last_name', $caseviewData['respondent_last_name']) }}">
                                @error('respondent_last_name')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="respondent_mobile">Mobile Number <span class="error-text">
                                        *</span></label>
                                <input type="text" id="respondent_mobile" name="respondent_mobile"
                                    class="form-control"
                                    value="{{ old('respondent_mobile', $caseviewData['respondent_mobile']) }}">
                                @error('respondent_mobile')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="respondent_email">Email <span class="error-text">
                                        *</span></label>
                                <input type="email" id="respondent_email" name="respondent_email" class="form-control"
                                    value="{{ old('respondent_email', $caseviewData['respondent_email']) }}">
                                @error('respondent_email')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="respondent_address1">Address Line 1 <span
                                        class="error-text"> *</span></label>
                                <input type="text" id="respondent_address1" name="respondent_address1"
                                    class="form-control"
                                    value="{{ old('respondent_address1', $caseviewData['respondent_address1']) }}">
                                @error('respondent_address1')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="respondent_address2">Address Line 2</label>
                                <input type="text" id="respondent_address2" name="respondent_address2"
                                    class="form-control"
                                    value="{{ old('respondent_address2', $caseviewData['respondent_address2']) }}">
                                @error('respondent_address2')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-3 col-12 mb-3">
                                <label class="form-label" for="respondent_address_type">Address Type <span
                                        class="error-text"> *</span></label>
                                <select name="respondent_address_type" id="respondent_address_type"
                                    class="form-control form-select">
                                    <option value="">Select Address Type</option>
                                    @foreach (config('constant.respondent_address_type', []) as $key => $value)
                                        <option value="{{ $key }}" @selected(old('respondent_address_type', $caseviewData['respondent_address_type']) == $key)>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('respondent_address_type')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 col-12 mb-3">
                                <label class="form-label" for="respondent_state_id">State <span class="error-text">
                                        *</span></label>
                                <select name="respondent_state_id" onchange="getCity(this.value)" class="form-select"
                                    id="respondent_state_id">
                                    <option value="">Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}" @selected(old('respondent_state_id', $caseviewData['respondent_state_id']) == $state->id)>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('respondent_state_id')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 col-12 mb-3">
                                <label class="form-label" for="respondent_city_id">City <span class="error-text">
                                        *</span></label>
                                <select name="respondent_city_id" class="form-select" id="respondent_city_id">
                                    <option value="">Select City</option>
                                </select>
                                @error('respondent_city_id')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 col-12 mb-3">
                                <label class="form-label" for="respondent_pincode">Pincode <span class="error-text">
                                        *</span></label>
                                <input type="text" id="respondent_pincode" name="respondent_pincode"
                                    class="form-control"
                                    value="{{ old('respondent_pincode', $caseviewData['respondent_pincode']) }}">
                                @error('respondent_pincode')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div> --}}


                            {{-- step-3 fields  --}}
                            {{-- <div class="col-12 text-center justify-content-center">
                                <h6 class="border-bottom bg-dark text-white p-2 border-2 my-3">Other Details</h6>
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="brief_of_case">Brief of Case <span class="error-text">
                                        *</span></label>
                                <input type="text" name="brief_of_case" class="form-control"
                                    value="{{ old('brief_of_case', $caseviewData['brief_of_case']) }}">
                                @error('brief_of_case')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="amount_in_dispute">Amount in Dispute</label>
                                <input type="text" name="amount_in_dispute" class="form-control"
                                    value="{{ old('amount_in_dispute', $caseviewData['amount_in_dispute']) }}">
                                @error('amount_in_dispute')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="case_type">Case Type <span class="error-text">
                                        *</span></label>
                                <select name="case_type" class="form-select" id="case_type">
                                    <option value="">Select Case Type</option>
                                    @foreach (config('constant.case_type', []) as $key => $value)
                                        <option value="{{ $key }}" @selected(old('case_type', $caseviewData['case_type']) == $key)>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('case_type')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="language">Language</label>
                                <select name="language" class="form-select" id="language">
                                    <option value="">Select Language</option>
                                    @foreach (config('constant.language', []) as $key => $value)
                                        <option value="{{ $key }}" @selected(old('language', $caseviewData['language']) == $key)>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('language')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-6 col-12 mb-3">
                                <label class="form-label" for="agreement_exist">Agreement exist</label>
                                <select name="agreement_exist" class="form-select" id="agreement_exist">
                                    <option value="">Select Agreement exist</option>
                                    <option value="1" @selected(old('agreement_exist', $caseviewData['agreement_exist']) == 1)>Yes</option>
                                    <option value="2" @selected(old('agreement_exist', $caseviewData['agreement_exist']) == 2)>No</option>
                                </select>
                                @error('agreement_exist')
                                    <span class="text-danger fs-custom">{{ $message }}</span>
                                @enderror
                            </div> --}}

                            @php
                                $documents = [
                                    'application_form' => 'Application Form',
                                    'foreclosure_statement' => 'Foreclosure Statement',
                                    'loan_agreement' => 'Loan Agreement',
                                    'account_statement' => 'Account Statement',
                                    'other_document' => 'Other Document',
                                ];
                            @endphp

                            @foreach ($documents as $key => $label)
                                <div class="col-md-6 col-12 mt-5 document-upload" id="upload-{{ $key }}">
                                    <label for="{{ $key }}" class="custom-file-upload">
                                        <span style="font-weight: 500;" id="file-label-{{ $key }}">
                                            <span style="border:2px solid black; border-radius:50%; padding: 1px;">➕</span>
                                            Attach {{ $label }} Document
                                        </span>
                                    </label>
                                    <input type="file" id="{{ $key }}" name="{{ $key }}" hidden />

                                    @if (!empty($caseviewData->$key))
                                        @php
                                            $filePath = asset('storage/' . $caseviewData->$key);
                                            $extension = pathinfo($caseviewData->$key, PATHINFO_EXTENSION);
                                        @endphp

                                        <div class="my-2">
                                            @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ $filePath }}" class="img-thumbnail" width="100" />
                                            @elseif ($extension === 'pdf')
                                                <a class="text-decoration-none case-text" style="font-size: 13px"
                                                    href="{{ $filePath }}" target="_blank">
                                                    <img src="{{ asset('public/assets/img/pdf.png') }}" height="30"
                                                        alt="PDF File" />
                                                    View PDF
                                                </a>
                                            @elseif (in_array(strtolower($extension), ['doc', 'docx']))
                                                <a class="text-decoration-none case-text" style="font-size: 13px"
                                                    href="{{ $filePath }}" target="_blank">
                                                    <img src="{{ asset('public/assets/img/doc.png') }}" height="30"
                                                        alt="DOC File" />
                                                    View Document
                                                </a>
                                            @else
                                                <a class="text-decoration-none case-text" style="font-size: 13px" href="{{ $filePath }}" target="_blank">Download File</a>
                                            @endif
                                        </div>
                                    @endif

                                    @error($key)
                                        <span class="text-danger fs-custom">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach

                            {{-- <div class="col-lg-6 mt-2">
                                <label class="form-label" for="status">Status</label>
                                <select name="status" class="form-select" id="status">
                                    <option value="1" @selected(old('status', $caseviewData['status']) == 1)> Active </option>
                                    <option value="0" @selected(old('status', $caseviewData['status']) == 0)> Inactive </option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> --}}

                            <div class="col-lg-12 mt-5 d-flex justify-content-start">
                                <button class="btn btn-secondary submitbtn py-1 px-4" type="submit">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Combine both your documents and notices into one array
            const documents = [
                "application_form",
                "foreclosure_statement",
                "loan_agreement",
                "account_statement",
                "other_document",
                "notice_first",
                "notice_second",
                "notice_third",
            ];

            // Loop through each item and bind the event listener
            documents.forEach(function (id) {
                const inputFile = document.getElementById(id);
                const fileLabel = document.getElementById("file-label-" + id);

                if (inputFile) {
                    inputFile.addEventListener("change", function (event) {
                        let fileName = event.target.files.length > 0 
                            ? event.target.files[0].name 
                            : "Attach PDF";

                        // Update the label text
                        fileLabel.textContent = fileName;
                    });
                }
            });
        });
    </script>
    <script type="text/javascript">
       $("#uploadnoticeView").validate({
            errorClass: "text-danger fs-custom",
            errorElement: "span",
            ignore: [], // ensure hidden fields aren't skipped
            rules: {
                @if(!isset($noticeType1))
                notice_first: {
                    required: true
                },
                @endif
                @if(!isset($noticeType2))
                notice_second: {
                    required: true
                },
                @endif
                @if(!isset($noticeType3))
                notice_third: {
                    required: true
                },
                @endif
            },
            messages: {
                @if(!isset($noticeType1))
                notice_first: {
                    required: "Please upload First Notice PDF"
                },
                @endif
                @if(!isset($noticeType2))
                notice_second: {
                    required: "Please upload Second Notice PDF"
                },
                @endif
                @if(!isset($noticeType3))
                notice_third: {
                    required: "Please upload Second Notice PDF"
                },
                @endif
            },
        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            var claimantCityId = "{{ old('claimant_city_id', $caseviewData->claimant_city_id ?? '') }}";
            var respondentCityId = "{{ old('respondent_city_id', $caseviewData->respondent_city_id ?? '') }}";

            function getCity(state_id, cityDropdownId, selectedCityId) {
                if (!state_id) return; // Prevent AJAX call if state is not selected

                $.ajax({
                    type: "POST",
                    url: "{{ route('cities.list') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        state_id
                    },
                    success: function(data) {
                        $('#' + cityDropdownId).html(data);

                        // Wait a moment to ensure the cities are loaded before setting the selected city
                        setTimeout(function() {
                            if (selectedCityId) {
                                $('#' + cityDropdownId).val(selectedCityId);
                            }
                        }, 100);
                    },
                });
            }

            // Prefill claimant city dropdown when state is preselected
            var claimantStateId = "{{ old('claimant_state_id', $caseviewData->claimant_state_id ?? '') }}";
            if (claimantStateId) {
                getCity(claimantStateId, 'claimant_city_id', claimantCityId);
            }

            // Prefill respondent city dropdown when state is preselected
            var respondentStateId = "{{ old('respondent_state_id', $caseviewData->respondent_state_id ?? '') }}";
            if (respondentStateId) {
                getCity(respondentStateId, 'respondent_city_id', respondentCityId);
            }

            // Bind event listeners for state changes
            $('#claimant_state_id').on('change', function() {
                getCity(this.value, 'claimant_city_id', null);
            });

            $('#respondent_state_id').on('change', function() {
                getCity(this.value, 'respondent_city_id', null);
            });
        });
    </script> --}}
    {{-- <script type="text/javascript">
        $("#editcaseview").validate({
            errorClass: "text-danger fs-custom",
            errorElement: "span",
            rules: {
                claimant_first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                claimant_mobile: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                claimant_email: {
                    required: true,
                    email: true,
                    minlength: 2,
                    maxlength: 100
                },
                claimant_address1: {
                    required: true
                },
                claimant_address_type: {
                    required: true
                },
                claimant_state_id: {
                    required: true
                },
                claimant_city_id: {
                    required: true
                },
                claimant_pincode: {
                    required: true
                },
                respondent_first_name: {
                    required: true
                },
                respondent_mobile: {
                    required: true
                },
                respondent_email: {
                    required: true
                },
                respondent_address1: {
                    required: true
                },
                respondent_address_type: {
                    required: true
                },
                respondent_state_id: {
                    required: true
                },
                respondent_city_id: {
                    required: true
                },
                respondent_pincode: {
                    required: true
                },
                brief_of_case: {
                    required: true
                },
                case_type: {
                    required: true
                },
                add_respondent: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 4
                },
                upload_evidence: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 4
                },
            },
            messages: {
                claimant_first_name: {
                    required: "Please enter first name"
                },
                claimant_mobile: {
                    required: "Please enter mobile"
                },
                claimant_email: {
                    required: "Please enter email"
                },
                claimant_address1: {
                    required: "Please enter address line 1"
                },
                claimant_address_type: {
                    required: "Please select address type"
                },
                claimant_state_id: {
                    required: "Please select state"
                },
                claimant_city_id: {
                    required: "Please select city"
                },
                claimant_pincode: {
                    required: "Please enter pincode"
                },
                respondent_first_name: {
                    required: "Please enter first name"
                },
                respondent_mobile: {
                    required: "Please enter mobile"
                },
                respondent_email: {
                    required: "Please enter email"
                },
                respondent_address1: {
                    required: "Please enter address line 1"
                },
                respondent_address_type: {
                    required: "Please select address type"
                },
                respondent_state_id: {
                    required: "Please select state"
                },
                respondent_city_id: {
                    required: "Please select city"
                },
                respondent_pincode: {
                    required: "Please enter pincode"
                },
                brief_of_case: {
                    required: "Please enter brief of case"
                },
                case_type: {
                    required: "Please select case type"
                },
                add_respondent: {
                    extension: "Supported Format Only: jpg, jpeg, png, pdf"
                },
                upload_evidence: {
                    extension: "Supported Format Only: jpg, jpeg, png, pdf"
                },
            },
        });
    </script> --}}
@endsection
