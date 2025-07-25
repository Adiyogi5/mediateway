@extends('layouts.auth')

@section('content')
    <div class="container register">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 ">
                    <div class="gender_box text-center justify-content-center">
                        <label class="login-title">REGISTRATION</label>
                        <p>You can choose to sign up as Individual, Organization, or DRP</p>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-5 text-center justify-content-around" id="registerTabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#individual"
                                    data-guard="individual">Individual</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#organization"
                                    data-guard="organization">Organization</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#drp" data-guard="drp">DRP</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            @foreach (['individual', 'organization', 'drp'] as $guard)
                                <div class="tab-pane fade @if ($guard === 'individual') show active @endif"
                                    id="{{ $guard }}">
                                    <form method="POST" action="{{ route('register') }}" id="{{ $guard . 'Form' }}">
                                        @csrf

                                        <input type="hidden" name="guard" value="{{ $guard }}">

                                        <div class="row">
                                            <!-- DRP Type field, initially hidden -->
                                            <div class="col-md-12 mb-3 drp-type-field" style="display: none;">
                                                <label for="drp_type">Select DRP Type</label>
                                                <select class="form-select @error('drp_type') is-invalid @enderror"
                                                    name="drp_type">
                                                    <option value="">Select DRP Type</option>
                                                    @foreach (config('constant.drp_type') as $typeKey => $typeValue)
                                                        <option value="{{ $typeKey }}"
                                                            {{ old('drp_type') == $typeKey ? 'selected' : '' }}>
                                                            {{ $typeValue }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('drp_type')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Other form fields like Name, Email, Mobile, OTP -->
                                            <div class="col-md-6 mb-3 name-field">
                                                <label for="name">Name</label>
                                                @if (old('guard') === $guard && $guard === 'organization')
                                                    <select name="name"
                                                        class="form-select @error('name') is-invalid @enderror">
                                                        <option value="">-- Select Organization --</option>
                                                        @foreach (\App\Models\OrganizationList::where('status', 1)->get() as $org)
                                                            <option value="{{ $org->name }}"
                                                                {{ old('name') == $org->name ? 'selected' : '' }}>
                                                                {{ $org->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fa-solid fa-user"></i></span>
                                                        <input type="text" name="name"
                                                            style="border-left: 1px solid #ffffff00;"
                                                            class="form-control @error('name') is-invalid @enderror">
                                                    </div>
                                                @endif
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            <div class="col-md-6 mb-3">
                                                <label for="mobile">Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fa-solid fa-envelope"></i></span>
                                                    <input type="email" name="email"
                                                        style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('email') is-invalid @enderror">
                                                    @error('email')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6 mb-4">
                                                <label for="mobile">Phone</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                                    <input type="text" name="mobile"
                                                        style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('mobile') is-invalid @enderror"
                                                        maxlength="10">
                                                    @error('mobile')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- <div class="col-6 mb-4">
                                                <label for="mobile">Enter Otp</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                                    <input type="text" name="otp" id="otp-{{ $guard }}"
                                                        style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('otp') is-invalid @enderror">
                                                    @error('otp')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div> --}}

                                            {{-- OTP Field --}}
                                            <div class="col-6 mb-4 otp-field"
                                                @if ($guard === 'organization') style="display: none;" @endif>
                                                <label for="mobile">Enter OTP</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                                    <input type="text" name="otp" id="otp-{{ $guard }}"
                                                        style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('otp') is-invalid @enderror">
                                                    @error('otp')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>


                                            {{-- Password + Confirm Password Fields (initially hidden for all except organization) --}}
                                            <div class="col-md-6 mb-4 password-fields"
                                                @if ($guard !== 'organization') style="display: none;" @endif>
                                                <label for="password">Password</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                                    <input type="password" name="password"
                                                        style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('password') is-invalid @enderror">
                                                </div>
                                                @error('password')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-4 password-fields"
                                                @if ($guard !== 'organization') style="display: none;" @endif>
                                                <label for="password_confirmation">Confirm Password</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                                    <input type="password" name="password_confirmation"
                                                        style="border-left: 1px solid #ffffff00;" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-4 password-fields"
                                                @if ($guard !== 'organization') style="display: none;" @endif>
                                                <div class="g-recaptcha" data-sitekey="{{ $googleRecaptchaData['GOOGLE_RECAPTCHA_KEY'] }}"></div>
                                                @if ($errors->has('g-recaptcha-response'))
                                                    <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                                                @endif
                                            </div>

                                            <div class="col-8">
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" name="terms" class="form-check-input">
                                                    <label class="form-check-label">I agree to the <a href="#">
                                                            Terms
                                                            of Service and Privacy Policy</a></label>
                                                </div>
                                            </div>
                                            <div class="col-4 send-otp-field"
                                                @if ($guard == 'organization') style="display: none;" @endif>
                                                <div class="mb-3 text-end">
                                                    <button type="button" id="sendOtp"
                                                        class="btn btn-send border-0 bg-transparent sendOtp pt-0 mt-0"
                                                        data-target="otp-{{ $guard }}"
                                                        data-guard="{{ $guard }}">
                                                        Send OTP <i class="fa fa-refresh ms-2"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="col-12 mb-3 text-center justify-content-center">
                                                <button class="btn btn-warning-custom submit-btn">SIGN UP as
                                                    {{ ucfirst($guard) }}</button>
                                            </div>

                                            <div class="col-12 text-center">
                                                <p class="mb-0">Already have an account? <a class="text-warning"
                                                        href="{{ url('individual/login') }}">Log In</a></p>
                                            </div>
                                            <div class="col-12 text-center">
                                                <p class="mb-0">Go To
                                                    <a href="{{ url('/') }}" class="text-warning">Home</a>
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        $(function() {
            // Set default active tab (Individual)
            $('#individual').addClass('show active');

            // Show/hide DRP type field based on active tab
            $('#registerTabs a').on('shown.bs.tab', function(e) {
                var targetTab = $(e.target).attr('href'); // e.g., '#organization'
                var $form = $(targetTab).find('form');

                // Show/hide DRP field
                if (targetTab === '#drp') {
                    $('.drp-type-field').show();
                } else {
                    $('.drp-type-field').hide();
                }
                
                // Show/hide password vs otp
                if (targetTab === '#organization') {
                    $form.find('.otp-field').hide();
                    $form.find('.send-otp-field').hide();
                    $form.find('.password-fields').show();
                } else {
                    $form.find('.otp-field').show();
                    $form.find('.send-otp-field').show();
                    $form.find('.password-fields').hide();
                }

                if (targetTab === '#organization') {
                    $.ajax({
                        url: "{{ url('/get-organizations') }}",
                        method: 'GET',
                        success: function(response) {
                            let options = '<option value="">-- Select Organization --</option>';
                            response.forEach(function(org) {
                                options +=
                                    `<option value="${org.name}">${org.name}</option>`;
                            });

                            $form.find('.name-field').html(`
                                <label for="name">Name</label>
                                <select name="name" class="form-select">
                                    ${options}
                                </select>
                            `);
                            initializeValidation(targetTab);
                        }
                    });
                } else {
                    initializeValidation(targetTab);
                }
            });


            // Trigger the event for the initial active tab
            $('#registerTabs a.active').trigger('shown.bs.tab');

            // Change the button text based on active tab
            $('.nav-link').on('click', function() {
                var guard = $(this).data('guard');
                $('.submit-btn').text('SIGN UP as ' + guard.charAt(0).toUpperCase() + guard.slice(1));
            });

            // OTP sending logic
            $(function() {
                $('.sendOtp').on('click', function() {
                    var targetOtpInput = $('#' + $(this).data('target'));
                    var form = targetOtpInput.closest('form');
                    var mobileInput = form.find('input[name="mobile"]');
                    var mobile = mobileInput.val().trim();
                    var button = $(this);

                    var guard = form.find('input[name="guard"]')
                .val(); // ✅ Correct: Dynamic from the form

                    if (!mobile || mobile.length !== 10) {
                        toastr.error(
                            "Please enter a valid 10-digit mobile number before requesting OTP."
                        );
                        return;
                    }

                    button.prop('disabled', true);
                    button.find('i').addClass('fa-spin');

                    $.ajax({
                        url: "{{ url('api/send-otp') }}",
                        type: 'POST',
                        data: {
                            mobile: mobile,
                            guard: guard, // send as string
                            is_register: 1 // or 0 depending on logic
                        },
                        headers: {
                            'x-api-key': "{{ config('constant.secret_token') }}"
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status) {
                                toastr.success(data.message);
                                setTimeout(() => {
                                    button.prop('disabled', false);
                                    button.find('i').removeClass('fa-spin');
                                }, 30000);
                            } else {
                                toastr.error(data.message);
                                if (data.data) {
                                    form.validate().showErrors(data.data);
                                }
                                button.prop('disabled', false);
                                button.find('i').removeClass('fa-spin');
                            }
                        },
                        error: function(xhr) {
                            toastr.error("Something went wrong while sending OTP.");
                            console.log(xhr.responseText);
                            button.prop('disabled', false);
                            button.find('i').removeClass('fa-spin');
                        }
                    });
                });
            });


            // Function to initialize validation based on the active tab
            function initializeValidation(targetTab) {
                var guard = $("input[name='guard']").val(); // Get the current guard value
                var formId = '#' + guard + 'Form'; // Generate the form ID based on guard

                // Initialize jQuery validation for the current form
                $(formId).validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 2,
                            maxlength: 100
                        },
                        email: {
                            required: true,
                            customEmail: true,
                            email: true
                        },
                        mobile: {
                            required: true,
                            number: true,
                            indiaMobile: true,
                            exactlength: 10,
                        },
                        otp: {
                            required: function () {
                                // OTP is only required if the guard is NOT organization
                                return form.find('input[name="guard"]').val() !== 'organization';
                            },
                            digits: true,
                            minlength: 6,
                            maxlength: 6
                        },
                        password: {
                            required: function () {
                                // Password only required for organization
                                return form.find('input[name="guard"]').val() === 'organization';
                            },
                            minlength: 6
                        },
                        password_confirmation: {
                            required: function () {
                                // Password only required for organization
                                return form.find('input[name="guard"]').val() === 'organization';
                            },
                            equalTo: 'input[name="password"]'
                        },
                        drp_type: {
                            required: function() {
                                // Apply validation for 'drp_type' only when the DRP tab is active
                                return targetTab === '#drp';
                            }
                        },
                        terms: {
                            required: true,
                        },
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
                        otp: {
                            required: "Please enter OTP Code.",
                        },
                        password: {
                            required: "Password is required",
                            minlength: "Password must be at least 6 characters"
                        },
                        password_confirmation: {
                            required: "Please confirm password",
                            equalTo: "Passwords do not match"
                        },
                        drp_type: {
                            required: "Please select DRP type",
                        },
                        terms: {
                            required: "Please select Terms and Conditions checkbox",
                        },
                    },
                    errorPlacement: function(error, element) {
                        if (element.attr("name") == "terms") {
                            error.insertAfter(".form-check");
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
            }

            // Initialize validation for the default active tab
            var initialTab = $('#registerTabs a.active').attr('href');
            initializeValidation(initialTab);
        });
    </script>
@endsection
