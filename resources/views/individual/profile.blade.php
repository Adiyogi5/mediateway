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
                    <form id="profileUpdate" method="POST" action="{{ route('individual.profile.update') }}"
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
                                            value="{{ old('name', $individual->name) }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control"
                                            value="{{ old('middle_name', $individual->middle_name) }}">
                                        @error('middle_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="last_name">Last Name</label>
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ old('last_name', $individual->last_name) }}">
                                        @error('last_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="dob">Date Of Birth</label>
                                        <input type="date" name="dob" class="form-control"
                                            value="{{ old('dob', $individual->dob) }}" max="{{ date('Y-m-d') }}">

                                        @error('dob')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="nationality">Nationality</label>
                                        <input type="text" name="nationality" class="form-control"
                                            value="{{ old('nationality', $individual->nationality) }}">
                                        @error('nationality')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label class="form-label" for="gender">Gender</label>
                                        <select name="gender" class="form-control form-select">
                                            <option value="">Select Gender</option>
                                            <option value="Male"
                                                {{ old('gender', $individual->gender) == 'Male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="Female"
                                                {{ old('gender', $individual->gender) == 'Female' ? 'selected' : '' }}>
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
                                            value="{{ old('email', $individual->email) }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="email_secondary">Email (Secondary opt.)</label>
                                        <input type="email_secondary" name="email_secondary" class="form-control"
                                            value="{{ old('email_secondary', $individual->email_secondary) }}">
                                        @error('email_secondary')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="mobile">Mobile Number (Primary)</label>
                                        <input type="text" name="mobile" class="form-control"
                                            value="{{ old('mobile', $individual->mobile) }}">
                                        @error('mobile')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="mobile_secondary">Mobile Number (Secondary
                                            opt.)</label>
                                        <input type="text" name="mobile_secondary" class="form-control"
                                            value="{{ old('mobile_secondary', $individual->mobile_secondary) }}">
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
                                                <option value="{{ $state->id }}" @selected(old('state_id', $individual->state_id) == $state->id)>
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
                                            value="{{ old('pincode', $individual->pincode) }}">
                                        @error('pincode')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="father_name">Father Name</label>
                                        <input type="text" name="father_name" class="form-control"
                                            value="{{ old('father_name', $individual->father_name) }}">
                                        @error('father_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="address1">Address Line 1</label>
                                        <input type="text" name="address1" class="form-control"
                                            value="{{ old('address1', $individual->address1) }}">
                                        @error('address1')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="address2">Address Line 2</label>
                                        <input type="text" name="address2" class="form-control"
                                            value="{{ old('address2', $individual->address2) }}">
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

                                    @if ($individual->image)
                                        <div class="mb-5">
                                            <img src="{{ asset('storage/' . $individual->image) }}" class="img-thumbnail"
                                                width="100" />
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
                                        <i class="fas fa-circle text-dark faa-form-heading my-auto"></i> Personal Details
                                    </h4>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="university">University / Collage</label>
                                        <input type="text" name="university" class="form-control"
                                            value="{{ old('university', optional($individualDetail)->university) }}">
                                        @error('university')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="degree">Degree</label>
                                        <input type="text" name="degree" class="form-control"
                                            value="{{ old('degree', optional($individualDetail)->degree) }}">
                                        @error('degree')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="year">Year</label>
                                        <input type="text" name="year" class="form-control"
                                            value="{{ old('year', optional($individualDetail)->year) }}">
                                        @error('year')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <h4 class="mb-3 d-flex item-align-self">
                                        <i class="fas fa-circle text-dark faa-form-heading my-auto"></i> Upload Documents
                                    </h4>

                                    <ul class="list-styled">
                                        <span class="top-li">Adhar Card</span>
                                    </ul>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label for="adhar_card" class="custom-file-upload">
                                            <span style="font-weight: 500;" id="file-label">
                                                <span style="border:2px solid black; border-radius:50%;padding: 1px;">➕</span> Upload Document</span>
                                        </label>
                                        <input type="file" id="adhar_card" name="adhar_card" hidden />
                                        @error('adhar_card')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    @if (optional($individualDetail)->adhar_card)
                                        @php
                                            $filePath = asset('storage/' . $individualDetail->adhar_card);
                                            $fileExtension = pathinfo($individualDetail->adhar_card, PATHINFO_EXTENSION);
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
        document.getElementById('adhar_card').addEventListener('change', function(event) {
            let fileName = event.target.files.length > 0 ? event.target.files[0].name : "Upload Document";
            document.getElementById('file-label').textContent = fileName;
        });
    </script>
    <script>
        var city_id = "{{ old('city_id', $individual['city_id']) }}";

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
            var selectedState = "{{ old('state_id', $individual['state_id']) }}";
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
