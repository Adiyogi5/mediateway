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
                    <form class="row" id="profileUpdate" method="POST" action="{{ route('organization.profile.update') }}"
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
                                    Other Details
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
                                   
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="name">Organization Name</label>
                                        @if($organization->parent_id == 0)
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $organization->name) }}" disabled>
                                            @else
                                            <input type="text" name="name" class="form-control"
                                            value="{{ old('name') ?? ($organization->name ?? '') }}">
                                            @endif
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="email">Email (Primary)</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email') ?? ($organization->email ?? '') }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="email_secondary">Email (Secondary opt.)</label>
                                        <input type="email_secondary" name="email_secondary" class="form-control"
                                            value="{{ old('email_secondary') ?? ($organization->email_secondary ?? '') }}">
                                        @error('email_secondary')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="mobile">Mobile Number (Primary)</label>
                                        <input type="text" name="mobile" class="form-control"
                                            value="{{ old('mobile') ?? ($organization->mobile ?? '') }}">
                                        @error('mobile')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="mobile_secondary">Mobile Number (Secondary
                                            opt.)</label>
                                        <input type="text" name="mobile_secondary" class="form-control"
                                            value="{{ old('mobile_secondary') ?? ($organization->mobile_secondary ?? '') }}">
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
                                                <option value="{{ $state->id }}" @selected(old('state_id') ?? ($organization->state_id ?? '') == $state->id)>
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
                                            value="{{ old('pincode', $organization->pincode) }}">
                                        @error('pincode')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="address1">Address Line 1</label>
                                        <input type="text" name="address1" class="form-control"
                                            value="{{ old('address1') ?? ($organization->address1 ?? '') }}">
                                        @error('address1')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="address2">Address Line 2</label>
                                        <input type="text" name="address2" class="form-control"
                                            value="{{ old('address2') ?? ($organization->address2 ?? '') }}">
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
                                    </div>

                                    @if ($organization->image)
                                        <div class="mb-5">
                                            <img src="{{ asset('storage/' . $organization->image) }}"
                                                class="img-thumbnail" width="100" />
                                        </div>
                                    @endif


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
                                        <i class="fas fa-circle text-dark faa-form-heading my-auto"></i> Organization Details
                                    </h4>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="organization_type">Organization Type</label>
                                        <select name="organization_type" class="form-control form-select">
                                            <option value="">Select Organization Type</option>
                                        </select>
                                        @error('organization_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 col-12 mb-3">
                                        <label class="form-label" for="description">Description</label>
                                        <textarea rows="4" id="description" name="description" class="form-control">
                                {{ old('description', optional($organizationDetail)->description) }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                   
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="registration_no">Registration No</label>
                                        <input type="text" name="registration_no" class="form-control"
                                            value="{{ old('registration_no', optional($organizationDetail)->registration_no) }}">
                                        @error('registration_no')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="registration_certificate">Registration
                                            Certificate Name</label>
                                        <input type="text" name="registration_certificate" class="form-control"
                                            value="{{ old('registration_certificate', optional($organizationDetail)->registration_certificate) }}">
                                        @error('registration_certificate')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <ul class="list-styled">
                                        <span class="top-li">Attach Registration Certificate</span>
                                    </ul>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label for="attach_registration_certificate" class="custom-file-upload">
                                            <span style="font-weight: 500;" id="file-label">
                                                <span style="border:2px solid black; border-radius:50%;padding: 1px;">➕</span> Upload Document</span>
                                        </label>
                                        <input type="file" id="attach_registration_certificate" name="attach_registration_certificate" hidden />
                                        @error('attach_registration_certificate')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    @if (optional($organizationDetail)->attach_registration_certificate)
                                        @php
                                            $filePath = asset('storage/' . $organizationDetail->attach_registration_certificate);
                                            $fileExtension = pathinfo($organizationDetail->attach_registration_certificate, PATHINFO_EXTENSION);
                                        @endphp

                                        <div class="mb-5">
                                            @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                                                <img src="{{ $filePath }}" class="img-thumbnail" width="100" />
                                            @elseif (strtolower($fileExtension) == 'pdf')
                                                <a href="{{ $filePath }}" target="_blank" class="btn btn-sm py-0 btn-dark">View PDF</a>
                                            @endif
                                        </div>
                                    @endif

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
        document.getElementById('attach_registration_certificate').addEventListener('change', function(event) {
            let fileName = event.target.files.length > 0 ? event.target.files[0].name : "Upload Document";
            document.getElementById('file-label').textContent = fileName;
        });
    </script>
    <script>
        var city_id = "{{ old('city_id', $organization['city_id']) }}";

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
            var selectedState = "{{ old('state_id', $organization['state_id']) }}";
            if (selectedState) {
                getCity(selectedState);
            }
        });

        $("#profileUpdate").validate({
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                name: {
                    required: false,
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
                email_secondary: {
                    required: false,
                },
                mobile_secondary: {
                    required: false,
                },
                pincode: {
                    required: false,
                },
                address1: {
                    required: false,
                },
                address2: {
                    required: false,
                },
                termsandconditions: {
                    required: true,
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
                termsandconditions: {
                    required: "Please Agree T&C",
                }
            },
        });
    </script>
@endsection
