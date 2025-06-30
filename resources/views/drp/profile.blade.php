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
                <div class="card-inner form-validate">
                    <form class="row" id="profileUpdate" method="POST" action="{{ route('drp.profile.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <ul class="nav nav-tabs" id="myTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile" type="button" role="tab" aria-controls="profile"
                                    aria-selected="true">
                                    Profile Details
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="education-tab" data-bs-toggle="tab" data-bs-target="#education"
                                    type="button" role="tab" aria-controls="education" aria-selected="false">
                                    Educational & Professional Detail
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="myTabsContent">
                            <!-- Profile Details Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <div class="row">
                                    {{-- ########### Tab One start ############# --}}
                                    <h4 class="mb-3 d-flex item-align-self">
                                        <i class="fas fa-circle text-dark faa-form-heading my-auto"></i> Personal Details
                                    </h4>

                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="name">First Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $drp->name) }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control"
                                            value="{{ old('middle_name', $drp->middle_name) }}">
                                        @error('middle_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="last_name">Last Name</label>
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ old('last_name', $drp->last_name) }}">
                                        @error('last_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="dob">Date Of Birth</label>
                                        <input type="date" name="dob" class="form-control"
                                            value="{{ old('dob', $drp->dob) }}" max="{{ date('Y-m-d') }}">

                                        @error('dob')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="nationality">Nationality</label>
                                        <input type="text" name="nationality" class="form-control"
                                            value="{{ old('nationality', $drp->nationality) }}">
                                        @error('nationality')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="gender">Gender</label>
                                        <select name="gender" class="form-control form-select">
                                            <option value="">Select Gender</option>
                                            <option value="Male"
                                                {{ old('gender', $drp->gender) == 'Male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="Female"
                                                {{ old('gender', $drp->gender) == 'Female' ? 'selected' : '' }}>
                                                Female
                                            </option>
                                        </select>

                                        @error('gender')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="email">Email (Primary)</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $drp->email) }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="email_secondary">Email (Secondary opt.)</label>
                                        <input type="email_secondary" name="email_secondary" class="form-control"
                                            value="{{ old('email_secondary', $drp->email_secondary) }}">
                                        @error('email_secondary')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="mobile">Mobile Number (Primary)</label>
                                        <input type="text" name="mobile" class="form-control"
                                            value="{{ old('mobile', $drp->mobile) }}">
                                        @error('mobile')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="mobile_secondary">Mobile Number (Secondary
                                            opt.)</label>
                                        <input type="text" name="mobile_secondary" class="form-control"
                                            value="{{ old('mobile_secondary', $drp->mobile_secondary) }}">
                                        @error('mobile_secondary')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="state_id">State</label>
                                        <select name="state_id" onchange="getCity(this.value)" class="form-select"
                                            id="state_id">
                                            <option value="">Select State</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}" @selected(old('state_id', $drp->state_id) == $state->id)>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('state_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="city_id">City</label>
                                        <select name="city_id" class="form-select" id="city_id">
                                            <option value="">Select City</option>
                                        </select>
                                        @error('city_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="pincode">Pincode</label>
                                        <input type="text" name="pincode" class="form-control"
                                            value="{{ old('pincode', $drp->pincode) }}">
                                        @error('pincode')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="father_name">Father Name</label>
                                        <input type="text" name="father_name" class="form-control"
                                            value="{{ old('father_name', $drp->father_name) }}">
                                        @error('father_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="address1">Address Line 1</label>
                                        <input type="text" name="address1" class="form-control"
                                            value="{{ old('address1', $drp->address1) }}">
                                        @error('address1')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="address2">Address Line 2</label>
                                        <input type="text" name="address2" class="form-control"
                                            value="{{ old('address2', $drp->address2) }}">
                                        @error('address2')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="image">Profile Image</label>
                                        <input class="form-control" id="image" name="image" type="file" />
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        @if ($drp->image)
                                            <div class="my-2">
                                                <img src="{{ asset('storage/' . $drp->image) }}" class="img-thumbnail"
                                                    width="100" />
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="signature_drp">Signature Image</label>
                                        <input class="form-control" id="signature_drp" name="signature_drp" type="file" />
                                        @error('signature_drp')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        @if ($drp->signature_drp)
                                            <div class="my-2">
                                                <img src="{{ asset('storage/' . $drp->signature_drp) }}" class="img-thumbnail"
                                                    width="150" />
                                            </div>
                                        @endif
                                    </div>


                                    <h4 class="mb-3 d-flex item-align-self">
                                        <i class="fas fa-circle text-dark faa-form-heading my-auto"></i> Public Details
                                    </h4>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="profession">Profession</label>
                                        <input type="text" name="profession" class="form-control"
                                            value="{{ old('profession', $drp->profession) }}">
                                        @error('profession')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="specialization">Add Case Type /
                                            Specialization</label>
                                        <select name="specialization" class="form-control form-select">
                                            <option value="">Select Specialization</option>
                                            @foreach (config('constant.case_type') as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                                <option value="{{ $key }}" {{ old('specialization', $drp->specialization ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('specialization')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <span class="top-li">
                                            If you are facing any problem please contact us at :-
                                        </span><br>
                                        <ul
                                            class="nav align-self-center text-center justify-content-center text-md-start justify-content-md-start">
                                            <li class="top-li me-1 ps-lg-0 pe-xl-2 pe-2 my-auto d-block d-md-block">
                                                @if (!empty($site_settings['phone']))
                                                    <a class="text-decoration-none"
                                                        href="tel:{{ $site_settings['phone'] }}">
                                                        <i class="fa-solid fa-phone nav-icon-font"></i>
                                                        <span class="nav-icon-text">{{ $site_settings['phone'] }} </span>
                                                    </a>
                                                @endif
                                            </li>
                                            <li class="top-li-line">|</li>
                                            @if (!empty($site_settings['email']))
                                                <li class="top-li me-1 px-xl-2 my-auto d-block d-md-block">
                                                    <a class="text-decoration-none"
                                                        href="mailto:{{ $site_settings['email'] }}">
                                                        <i class="fa-solid fa-envelope nav-icon-font"></i>
                                                        <span class="nav-icon-text">{{ $site_settings['email'] }}</span>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                        <span class="top-li">
                                            Important Notes
                                        </span><br>
                                        <span class="top-li">
                                            Public profile is subject to edit to enhance presentation but finalization only
                                            after
                                            DRP approval
                                        </span>

                                        <div class="col-12">
                                            <div class="mt-3 form-check">
                                                <input type="checkbox" name="termsandconditions" class="form-check-input">
                                                <label class="form-check-label top-li">T&C ( That the given information &
                                                    Document
                                                    are Correct )</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- ########## Tab One End ##########  --}}
                                </div>
                            </div>


                            <!-- Educational & Professional Detail Tab -->
                            <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
                                <div class="row">
                                    {{-- ########## Tab Two Start ##########  --}}
                                    <h4 class="mb-3 d-flex item-align-self">
                                        <i class="fas fa-circle text-dark faa-form-heading my-auto"></i> Personal Details
                                    </h4>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="university">University / Collage</label>
                                        <input type="text" name="university" class="form-control"
                                            value="{{ old('university', optional($drpDetail)->university) }}">
                                        @error('university')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="field_of_study">Field of study</label>
                                        <input type="text" name="field_of_study" class="form-control"
                                            value="{{ old('field_of_study', optional($drpDetail)->field_of_study) }}">
                                        @error('field_of_study')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="degree">Degree</label>
                                        <input type="text" name="degree" class="form-control"
                                            value="{{ old('degree', optional($drpDetail)->degree) }}">
                                        @error('degree')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="year">Year</label>
                                        <input type="text" name="year" class="form-control"
                                            value="{{ old('year', optional($drpDetail)->year) }}">
                                        @error('year')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="description">Description</label>
                                        <textarea rows="4" id="description" name="description" class="form-control">
                                {{ old('description', optional($drpDetail)->description) }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="achievement_od_socities">Achievement and
                                            Societies</label>
                                        <textarea rows="4" id="achievement_od_socities" name="achievement_od_socities" class="form-control">
                                {{ old('achievement_od_socities', optional($drpDetail)->achievement_od_socities) }}</textarea>
                                        @error('year')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <h4 class="mb-3 d-flex item-align-self">
                                        <i class="fas fa-circle text-dark faa-form-heading my-auto"></i> Profession Details
                                    </h4>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="designation">Designation</label>
                                        <input type="text" name="designation" class="form-control"
                                            value="{{ old('designation', optional($drpDetail)->designation) }}">
                                        @error('designation')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="organization">Organization</label>
                                        <input type="text" name="organization" class="form-control"
                                            value="{{ old('organization', optional($drpDetail)->organization) }}">
                                        @error('organization')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="professional_degree">Profession / Degree </label>
                                        <select name="professional_degree" class="form-control form-select">
                                            <option value="">Select Profession / Degree</option>
                                        </select>
                                        @error('professional_degree')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="registration_no">Registration No</label>
                                        <input type="text" name="registration_no" class="form-control"
                                            value="{{ old('registration_no', optional($drpDetail)->registration_no) }}">
                                        @error('registration_no')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="job_description">Job Description </label>
                                        <input type="text" name="job_description" class="form-control"
                                            value="{{ old('job_description', optional($drpDetail)->job_description) }}">
                                        @error('job_description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="currently_working_here">Currently working
                                            here</label>
                                        <input type="text" name="currently_working_here" class="form-control"
                                            value="{{ old('currently_working_here', optional($drpDetail)->currently_working_here) }}">
                                        @error('currently_working_here')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="years_of_experience">Years of experience</label>
                                        <select name="years_of_experience" class="form-control form-select">
                                            <option value="">Select Years of experience</option>
                                        </select>
                                        @error('years_of_experience')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-12 mb-3">
                                        <label class="form-label" for="registration_certificate">Attach Registration
                                            Certificate</label>
                                        <input type="text" name="registration_certificate" class="form-control"
                                            value="{{ old('registration_certificate', optional($drpDetail)->registration_certificate) }}">
                                        @error('registration_certificate')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <ul class="list-styled">
                                        <span class="top-li">Professional Application and certificate</span>
                                        <li class="top-li ms-3">Professional App. ( Eg Bar Association , ADR Organization )
                                        </li>
                                        <li class="top-li ms-3">Certifications ( Eg Mediation Certification, ARBITRATOR
                                            Certification, Concilator Certification )</li>
                                    </ul>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label for="attach_registration_certificate" class="custom-file-upload">
                                            <span style="font-weight: 500;">
                                                <span style="border:2px solid black; border-radius:50%;padding: 1px;">➕</span> Upload Document</span>
                                        </label>
                                        <input type="file" id="attach_registration_certificate" name="attach_registration_certificate" hidden />
                                        @error('attach_registration_certificate')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    @if (optional($drpDetail)->attach_registration_certificate)
                                        @php
                                            $filePath = asset('storage/' . $drpDetail->attach_registration_certificate);
                                            $fileExtension = pathinfo($drpDetail->attach_registration_certificate, PATHINFO_EXTENSION);
                                        @endphp

                                        <div class="mb-5">
                                            @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                                                <img src="{{ $filePath }}" class="img-thumbnail" width="100" />
                                            @elseif (strtolower($fileExtension) == 'pdf')
                                                <a href="{{ $filePath }}" target="_blank" class="btn btn-sm py-0 btn-dark">View PDF</a>
                                            @endif
                                        </div>
                                    @endif


                                    <h4 class="mb-3 d-flex item-align-self">
                                        <i class="fas fa-circle text-dark faa-form-heading my-auto"></i> Summary of the
                                        Experience
                                        as a DRP if Any ( Arbitrator/Mediator/Concilitor )
                                    </h4>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="experience_in_the_field_of_drp">Experience in the
                                            field
                                            of
                                            DRP ( Arbitrator/Mediator/Concilitor )</label>
                                        <input type="text" name="experience_in_the_field_of_drp" class="form-control"
                                            value="{{ old('experience_in_the_field_of_drp', optional($drpDetail)->experience_in_the_field_of_drp) }}">
                                        @error('experience_in_the_field_of_drp')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="areas_of_expertise">Areas of Expertise</label>
                                        <select name="areas_of_expertise" class="form-control form-select">
                                            <option value="">Select Areas of Expertise</option>
                                        </select>
                                        @error('areas_of_expertise')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="membership_of_professional_organisation">Membership
                                            of
                                            Professional Organisation </label>
                                        <input type="text" name="membership_of_professional_organisation"
                                            class="form-control"
                                            value="{{ old('membership_of_professional_organisation', optional($drpDetail)->membership_of_professional_organisation) }}">
                                        @error('membership_of_professional_organisation')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="no_of_awards_as_arbitrator">No. of Awards as
                                            Arbitrator</label>
                                        <input type="text" name="no_of_awards_as_arbitrator" class="form-control"
                                            value="{{ old('no_of_awards_as_arbitrator', optional($drpDetail)->no_of_awards_as_arbitrator) }}">
                                        @error('no_of_awards_as_arbitrator')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="total_years_of_working_as_drp">Total years of
                                            Working
                                            as
                                            DRP ( Arbitrator/Mediator/Concilitor ) </label>
                                        <input type="text" name="total_years_of_working_as_drp" class="form-control"
                                            value="{{ old('total_years_of_working_as_drp', optional($drpDetail)->total_years_of_working_as_drp) }}">
                                        @error('total_years_of_working_as_drp')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="functional_area_of_drp">Functional Area of DRP (
                                            Arbitrator/Mediator/Concilitor )</label>
                                        <input type="text" name="functional_area_of_drp" class="form-control"
                                            value="{{ old('functional_area_of_drp', optional($drpDetail)->functional_area_of_drp) }}">
                                        @error('functional_area_of_drp')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    {{-- ########## Tab Two End ##########  --}}
                                </div>
                            </div>
                        </div>


                        <div class="col-12 mt-5 text-center justify-content-center">
                            <button type="submit" class="btn btn-warning px-4 py-1">Update</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        var city_id = "{{ old('city_id', $drp['city_id']) }}";

        function getCity(state_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('cities.list') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    state_id
                },
                success: function(data) {
                    $('#city_id').html(data);

                    // Ensure city is selected after cities are loaded
                    if (city_id) {
                        $('#city_id').val(city_id);
                    }
                },
            });
        }

        $(document).ready(function() {
            // Prefill city dropdown when state is preselected
            var selectedState = "{{ old('state_id', $drp['state_id']) }}";
            if (selectedState) {
                getCity(selectedState);
            }
        });

        $("#profileUpdate").validate({
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                email: {
                    required: true,
                    email: true,
                    minlength: 2,
                    maxlength: 100
                },
                mobile: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10
                },
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                signature_drp: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                middle_name: {
                    required: false,
                },
                last_name: {
                    required: false,
                },
                dob: {
                    required: false,
                },
                nationality: {
                    required: false,
                },
                gender: {
                    required: false,
                },
                email_secondary: {
                    required: false,
                },
                mobile_secondary: {
                    required: false,
                },
                pincode: {
                    required: false,
                },
                father_name: {
                    required: false,
                },
                address1: {
                    required: false,
                },
                address2: {
                    required: false,
                },
                profession: {
                    required: false,
                },
                specialization: {
                    required: false,
                },
                termsandconditions: {
                    required:true,
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                email: {
                    required: "Please enter Email",
                },
                mobile: {
                    required: "Please enter Mobile number",
                },
                image: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                },
                signature_drp: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                },
                termsandconditions: {
                    required: "Please Agree T&C",
                }
            },
        });
    </script>
@endsection
