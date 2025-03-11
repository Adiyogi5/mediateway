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
                        <div class="progress mt-5 px-1" style="height: 4px;">
                            <div class="progress-bar" role="progressbar" style="width: 20%;" aria-valuenow="0"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="step-container mb-5 d-flex justify-content-around">
                            <div class="step-line"></div>
                            <div class="step-circle" onclick="displayStep(1)" data-step="1">
                                <div class="d-grid">
                                    <img src="{{ asset('assets/img/file-case/candidacy.png') }}"
                                        class="img-fluid img-progress-file" alt="">
                                    <span class="title-progress-file">Claimant</span>
                                </div>
                            </div>
                            <div class="step-circle" onclick="displayStep(2)" data-step="2">
                                <div class="d-grid">
                                    <img src="{{ asset('assets/img/file-case/respondent.png') }}"
                                        class="img-fluid img-progress-file" alt="">
                                    <span class="title-progress-file">Respondent</span>
                                </div>
                            </div>
                            <div class="step-circle" onclick="displayStep(3)" data-step="3">
                                <div class="d-grid">
                                    <img src="{{ asset('assets/img/file-case/case-detail.png') }}"
                                        class="img-fluid img-progress-file" alt="">
                                    <span class="title-progress-file">Case detail</span>
                                </div>
                            </div>
                            <div class="step-circle" onclick="displayStep(4)" data-step="4">
                                <div class="d-grid">
                                    <img src="{{ asset('assets/img/file-case/preview.png') }}"
                                        class="img-fluid img-progress-file" alt="">
                                    <span class="title-progress-file">Preview</span>
                                </div>
                            </div>
                            <div class="step-circle" onclick="displayStep(5)" data-step="5">
                                <div class="d-grid">
                                    <img src="{{ asset('assets/img/file-case/save.png') }}"
                                        class="img-fluid img-progress-file" alt="">
                                    <span class="title-progress-file">Payment</span>
                                </div>
                            </div>
                        </div>

                        <form id="multiStepForm" method="POST" action="{{ route('individual.case.registercase') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="step step-1">
                                <!-- Step 1 form fields here -->
                                <div class="row mx-lg-3 mx-0 mt-3">
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="claimant_first_name">First Name <span
                                                class="error-text"> *</span></label>
                                        <input type="text" id="claimant_first_name" name="claimant_first_name"
                                            class="form-control" value="{{ old('claimant_first_name') }}">
                                        @error('claimant_first_name')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="claimant_middle_name">Middle Name</label>
                                        <input type="text" id="claimant_middle_name" name="claimant_middle_name"
                                            class="form-control" value="{{ old('claimant_middle_name') }}">
                                        @error('claimant_middle_name')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="claimant_last_name">Last Name</label>
                                        <input type="text" id="claimant_last_name" name="claimant_last_name"
                                            class="form-control" value="{{ old('claimant_last_name') }}">
                                        @error('claimant_last_name')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="claimant_mobile">Mobile Number <span
                                                class="error-text"> *</span></label>
                                        <input type="text" id="claimant_mobile" name="claimant_mobile"
                                            class="form-control" value="{{ old('claimant_mobile') }}">
                                        @error('claimant_mobile')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="claimant_email">Email</label>
                                        <input type="email" id="claimant_email" name="claimant_email"
                                            class="form-control" value="{{ old('claimant_email') }}">
                                        @error('claimant_email')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="claimant_address1">Address Line 1 <span
                                                class="error-text"> *</span></label>
                                        <input type="text" id="claimant_address1" name="claimant_address1"
                                            class="form-control" value="{{ old('claimant_address1') }}">
                                        @error('claimant_address1')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="claimant_address2">Address Line 2</label>
                                        <input type="text" id="claimant_address2" name="claimant_address2"
                                            class="form-control" value="{{ old('claimant_address2') }}">
                                        @error('claimant_address2')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-3 col-12 mb-3">
                                        <label class="form-label" for="claimant_address_type">Address Type <span
                                                class="error-text"> *</span></label>
                                        <select name="claimant_address_type" id="claimant_address_type"
                                            class="form-control form-select">
                                            <option value="">Select Address Type</option>
                                            @foreach (config('constant.claimant_address_type', []) as $key => $value)
                                                <option value="{{ $key }}" @selected(old('claimant_address_type') == $key)>
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
                                        <select name="claimant_state_id" onchange="getCity(this.value)"
                                            class="form-select" id="claimant_state_id">
                                            <option value="">Select State</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}" @selected(old('claimant_state_id') == $state->id)>
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
                                        <label class="form-label" for="claimant_pincode">Pincode <span
                                                class="error-text"> *</span></label>
                                        <input type="text" id="claimant_pincode" name="claimant_pincode"
                                            class="form-control" value="{{ old('claimant_pincode') }}">
                                        @error('claimant_pincode')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="button"
                                        class="btn btn-warning-custom next-step w-auto mx-auto mt-xl-5 mt-3">Next
                                        &nbsp;&nbsp;&nbsp; <i class="fa-solid fa-arrow-right"></i></button>
                                </div>

                            </div>

                            <div class="step step-2">
                                <!-- Step 2 form fields here -->
                                <div class="row mx-lg-3 mx-0 mt-3">
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="respondent_first_name">First Name <span
                                                class="error-text"> *</span></label>
                                        <input type="text" id="respondent_first_name" name="respondent_first_name"
                                            class="form-control" value="{{ old('respondent_first_name') }}">
                                        @error('respondent_first_name')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="respondent_middle_name">Middle Name</label>
                                        <input type="text" id="respondent_middle_name" name="respondent_middle_name"
                                            class="form-control" value="{{ old('respondent_middle_name') }}">
                                        @error('respondent_middle_name')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="respondent_last_name">Last Name</label>
                                        <input type="text" id="respondent_last_name" name="respondent_last_name"
                                            class="form-control" value="{{ old('respondent_last_name') }}">
                                        @error('respondent_last_name')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="respondent_mobile">Mobile Number <span
                                                class="error-text"> *</span></label>
                                        <input type="text" id="respondent_mobile" name="respondent_mobile"
                                            class="form-control" value="{{ old('respondent_mobile') }}">
                                        @error('respondent_mobile')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="respondent_email">Email <span class="error-text">
                                                *</span></label>
                                        <input type="email" id="respondent_email" name="respondent_email"
                                            class="form-control" value="{{ old('respondent_email') }}">
                                        @error('respondent_email')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="respondent_address1">Address Line 1 <span
                                                class="error-text"> *</span></label>
                                        <input type="text" id="respondent_address1" name="respondent_address1"
                                            class="form-control" value="{{ old('respondent_address1') }}">
                                        @error('respondent_address1')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="respondent_address2">Address Line 2</label>
                                        <input type="text" id="respondent_address2" name="respondent_address2"
                                            class="form-control" value="{{ old('respondent_address2') }}">
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
                                                <option value="{{ $key }}" @selected(old('respondent_address_type') == $key)>
                                                    {{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('respondent_address_type')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 col-12 mb-3">
                                        <label class="form-label" for="respondent_state_id">State <span
                                                class="error-text"> *</span></label>
                                        <select name="respondent_state_id" onchange="getCity(this.value)"
                                            class="form-select" id="respondent_state_id">
                                            <option value="">Select State</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}" @selected(old('respondent_state_id') == $state->id)>
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
                                        <label class="form-label" for="respondent_pincode">Pincode <span
                                                class="error-text"> *</span></label>
                                        <input type="text" id="respondent_pincode" name="respondent_pincode"
                                            class="form-control" value="{{ old('respondent_pincode') }}">
                                        @error('respondent_pincode')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 my-3">
                                        <label for="add_respondent" class="custom-file-upload">
                                            <span style="font-weight: 500;" id="file-label1">
                                                <span
                                                    style="border:2px solid black; border-radius:50%;padding: 1px;">➕</span>
                                                Add Respondent</span>
                                        </label>
                                        <input type="file" id="add_respondent" name="add_respondent" hidden />
                                        @error('add_respondent')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="text-center justify-content-center">
                                        <button type="button"
                                            class="btn btn-warning-custom prev-step w-auto mt-xl-5 mt-3">Previous</button>
                                        <button type="button"
                                            class="btn btn-warning-custom next-step w-auto mt-xl-5 mt-3">Next</button>
                                    </div>

                                </div>
                            </div>

                            <div class="step step-3">
                                <!-- Step 3 form fields here -->
                                <div class="row mx-lg-3 mx-0 mt-3">
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="brief_of_case">Brief of Case <span
                                                class="error-text"> *</span></label>
                                        <input type="text" name="brief_of_case" class="form-control"
                                            value="{{ old('brief_of_case') }}">
                                        @error('brief_of_case')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="amount_in_dispute">Amount in Dispute</label>
                                        <input type="text" name="amount_in_dispute" class="form-control"
                                            value="{{ old('amount_in_dispute') }}">
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
                                                <option value="{{ $key }}" @selected(old('case_type') == $key)>
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
                                                <option value="{{ $key }}" @selected(old('language') == $key)>
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
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                        @error('agreement_exist')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mt-4">
                                        <label for="upload_evidence" class="custom-file-upload mt-1 w-100">
                                            <span style="font-weight: 500;" id="file-label2">
                                                <span
                                                    style="border:2px solid black; border-radius:50%;padding: 1px;">➕</span>
                                                Attach document / Upload Evidence</span>
                                        </label>
                                        <input type="file" id="upload_evidence" name="upload_evidence" hidden />
                                        @error('upload_evidence')
                                            <span class="text-danger fs-custom">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-12">
                                        <div class="mt-3 form-check">
                                            <input type="checkbox" name="termsandconditions" class="form-check-input">
                                            <label class="form-check-label top-li">I agree to the Terms of Service and
                                                Privacy Policy</label>
                                        </div>
                                    </div>


                                    <div class="text-center justify-content-center">
                                        <button type="button"
                                            class="btn btn-warning-custom prev-step w-auto mt-xl-5 mt-3">Previous</button>
                                        <button type="button"
                                            class="btn btn-warning-custom next-step w-auto mt-xl-5 mt-3">Next</button>
                                    </div>
                                </div>
                            </div>

                            <div class="step step-4">
                                <!-- Step 4 form fields here -->
                                <div class="row mx-lg-3 mx-0 mt-3">
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-case-card">
                                            <h4 class="case-heading">Claimant Details</h4>
                                            <div class="row gx-5 gy-3">
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Name:</p>
                                                    <p class="case-text" id="preview_claimant_name"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Contact Number:</p>
                                                    <p class="case-text" id="preview_claimant_mobile"></p>
                                                </div>
                                                <div class="col-md-10 col-12">
                                                    <p class="case-title">Email:</p>
                                                    <p class="case-text" id="preview_claimant_email"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Address:</p>
                                                    <p class="case-text" id="preview_claimant_address1"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Address Line 2:</p>
                                                    <p class="case-text" id="preview_claimant_address2"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">State:</p>
                                                    <p class="case-text" id="preview_claimant_state_id"> </p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">City:</p>
                                                    <p class="case-text" id="preview_claimant_city_id"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Pin Code:</p>
                                                    <p class="case-text" id="preview_claimant_pincode"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-case-card">
                                            <h4 class="case-heading">Respondent Details</h4>
                                            <div class="row gx-5 gy-3">
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Name:</p>
                                                    <p class="case-text" id="preview_respondent_name"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Contact Number:</p>
                                                    <p class="case-text" id="preview_respondent_mobile"></p>
                                                </div>
                                                <div class="col-md-10 col-12">
                                                    <p class="case-title">Email:</p>
                                                    <p class="case-text" id="preview_respondent_email"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Address Line 1:</p>
                                                    <p class="case-text" id="preview_respondent_address1"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Address Line 2:</p>
                                                    <p class="case-text" id="preview_respondent_address2"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">State:</p>
                                                    <p class="case-text" id="preview_respondent_state_id"> </p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">City:</p>
                                                    <p class="case-text" id="preview_respondent_city_id"></p>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <p class="case-title">Pin Code:</p>
                                                    <p class="case-text" id="preview_respondent_pincode"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mt-3 form-check">
                                            <input type="checkbox" name="termsandconditions" class="form-check-input">
                                            <label class="form-check-label top-li">I agree to the Terms of Service and
                                                Privacy Policy</label>
                                        </div>
                                    </div>


                                    <div class="text-center justify-content-center">
                                        <button type="button"
                                            class="btn btn-warning-custom prev-step w-auto mt-xl-5 mt-3">Previous</button>

                                        <button type="submit"
                                            class="btn btn-warning-custom next-step w-auto mt-xl-5 mt-3 submit-btn">Submit</button>
                                    </div>
                                </div>

                            </div>

                        </form>

                        <div class="step step-5" id="step-5">
                            <!-- Step 5 form fields here -->
                            <div class="row mx-lg-3 mx-0 mt-3">
                                <div class="col-md-12 mb-3 text-center justify-content-center">
                                    <h6>Payment With</h6>
                                    <img class="img-fluid img-razorpay my-5" src="{{ asset('assets/img/razorpay.png') }}"
                                        alt="">
                                    </br>
                                    <a href="#" class="btn btn-success btn-razor-pay px-5 my-3">Pay</a>
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
    <script>
        $(document).ready(function() {
            let currentStep = 1;

            // Hide all steps except Step 1 on page load
            $(".step").hide();
            $(".step-1").show();

            // Next Step Button Click
            $(".next-step").click(function() {
                if (!validateStep(currentStep)) return; // Stop if validation fails

                $(".step-" + currentStep).hide();
                currentStep++;
                $(".step-" + currentStep).show();

                if (currentStep === 4) {
                    updatePreview(); // Update Preview in Step 4
                }
            });

            // Previous Step Button Click
            $(".prev-step").click(function() {
                $(".step-" + currentStep).hide();
                currentStep--;
                $(".step-" + currentStep).show();
            });

            // Form Submission (Step 4)
            $("#multiStepForm").submit(function(event) {
                event.preventDefault();
                $(".submit-btn").prop("disabled", true); // Prevent multiple submissions

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('individual.case.registercase') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $(".step-4").hide(); // Hide Step 4
                        $(".step-5").show(); // Show Step 5

                        // Show success toast
                        toastr.success("Your case has been registered successfully!");

                        $(".submit-btn").prop("disabled", false);
                    },
                    error: function(response) {
                        $(".step-5").hide(); // Hide Step 4
                        $(".step-4").show(); // Show Step 5

                        // Show error toast
                        toastr.error("Something went wrong. Please try again.");

                        $(".submit-btn").prop("disabled", false); 
                    }
                });
            });

            // Function to Validate Required Fields Before Moving to the Next Step
            function validateStep(step) {
                let isValid = true;

                $(".step-" + step)
                    .find("input[required], select[required]")
                    .each(function() {
                        if ($(this).val().trim() === "") {
                            isValid = false;
                            $(this).addClass("is-invalid"); // Highlight invalid fields
                        } else {
                            $(this).removeClass("is-invalid");
                        }
                    });

                return isValid;
            }

            // Function to Update Preview in Step 4
            function updatePreview() {
                $("#preview_claimant_name").text($("#claimant_first_name").val() + " " + $("#claimant_last_name")
                    .val());
                $("#preview_claimant_mobile").text($("#claimant_mobile").val());
                $("#preview_claimant_email").text($("#claimant_email").val());
                $("#preview_claimant_address1").text($("#claimant_address1").val());
                $("#preview_claimant_address2").text($("#claimant_address2").val());
                $("#preview_claimant_state_id").text($("#claimant_state_id option:selected").text());
                $("#preview_claimant_city_id").text($("#claimant_city_id option:selected").text());
                $("#preview_claimant_pincode").text($("#claimant_pincode").val());
                $("#preview_respondent_name").text($("#respondent_first_name").val() + " " + $(
                    "#respondent_last_name").val());
                $("#preview_respondent_mobile").text($("#respondent_mobile").val());
                $("#preview_respondent_email").text($("#respondent_email").val());
                $("#preview_respondent_address1").text($("#respondent_address1").val());
                $("#preview_respondent_address2").text($("#respondent_address2").val());
                $("#preview_respondent_state_id").text($("#respondent_state_id option:selected").text());
                $("#preview_respondent_city_id").text($("#respondent_city_id option:selected").text());
                $("#preview_respondent_pincode").text($("#respondent_pincode").val());
            }
        });
    </script>
    <script>
        document.getElementById('add_respondent').addEventListener('change', function(event) {
            let fileName = event.target.files.length > 0 ? event.target.files[0].name : "Add Respondent";
            document.getElementById('file-label1').textContent = fileName;
        });
        document.getElementById('upload_evidence').addEventListener('change', function(event) {
            let fileName = event.target.files.length > 0 ? event.target.files[0].name :
                "Attach document / Upload Evidence";
            document.getElementById('file-label2').textContent = fileName;
        });
    </script>
    <script>
        $(document).ready(function() {
            var currentStep = 1;
            var totalSteps = $(".step").length;

            function updateStepUI() {
                $(".step").hide(); // Hide all steps
                $(".step-" + currentStep).show(); // Show only the current step

                // Reset all images to white
                $(".img-progress-file").css("background-color", "#ffffff");

                // Change background of all previous and current steps to #C3C9EF
                $(".step-circle").each(function() {
                    var stepNum = $(this).data("step");
                    if (stepNum <= currentStep) {
                        $(this).find(".img-progress-file").css("background-color", "#FFB53F");
                    }
                });

                // Update Progress Bar
                var progressPercentage = (currentStep / totalSteps) * 100;
                $(".progress-bar").css("width", progressPercentage + "%");
            }

            $(".next-step").click(function() {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateStepUI();
                }
            });

            $(".prev-step").click(function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateStepUI();
                }
            });

            updateStepUI(); // Initialize on load
        });
    </script>
    <script>
        $(document).ready(function() {
            var claimantCityId = "{{ old('claimant_city_id') }}";
            var respondentCityId = "{{ old('respondent_city_id') }}";

            function getCity(state_id, cityDropdownId, selectedCityId) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('cities.list') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        state_id
                    },
                    success: function(data) {
                        $('#' + cityDropdownId).html(data);

                        // Ensure city is selected after cities are loaded
                        if (selectedCityId) {
                            $('#' + cityDropdownId).val(selectedCityId);
                        }
                    },
                });
            }

            // Prefill claimant city dropdown when state is preselected
            var claimantStateId = "{{ old('claimant_state_id') }}";
            if (claimantStateId) {
                getCity(claimantStateId, 'claimant_city_id', claimantCityId);
            }

            // Prefill respondent city dropdown when state is preselected
            var respondentStateId = "{{ old('respondent_state_id') }}";
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
    </script>
    <script>
        $(document).ready(function() {
            // Custom file size validation method (Max: 4MB)
            $.validator.addMethod("filesize", function(value, element, param) {
                if (element.files.length === 0) {
                    return true; // Skip validation if no file is selected
                }
                return element.files[0].size <= param * 1024 * 1024;
            }, "File size must be less than {0} MB");

            $("#multiStepForm").validate({
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
                    termsandconditions: {
                        required: true
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
                    termsandconditions: {
                        required: "Please agree to terms and conditions"
                    },
                },
            });
        });
    </script>
@endsection
