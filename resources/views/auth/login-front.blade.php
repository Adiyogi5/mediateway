@extends('layouts.auth')

@section('content')
    <div class="container register">
        <div class="row">
            <div class="col-lg-10 col-12 mx-lg-auto mx-none position-relative">
                <div class="card border-0 ">
                    <div class="gender_box text-center justify-content-center">
                        <label class="login-title">LOGIN</label>
                    </div>
                    <div class="card-body px-lg-5 px-0">
                        <!-- Login Tabs -->
                        <ul class="nav nav-pills mb-5 text-center justify-content-around" id="loginTabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="pill" href="#individual-login">Individual</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#organization-login">Organization</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#drp-login">DRP</a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            @php
                                $loginTypes = [
                                    'individual' => 'Individual',
                                    'organization' => 'Organization',
                                    'drp' => 'DRP',
                                ];
                            @endphp

                            @foreach ($loginTypes as $key => $label)
                                <div id="{{ $key }}-login"
                                    class="tab-pane fade {{ $key === 'individual' ? 'show active' : '' }}">
                                    <form method="POST" action="{{ route('login.post', ['guard' => $key]) }}">
                                        @csrf
                                        <input type="hidden" name="guard" value="{{ $key }}">

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="mobile">Phone</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                                    <input class="form-control @error('mobile') is-invalid @enderror"
                                                        type="text" name="mobile" autocomplete="mobile"
                                                        value="{{ old('mobile') }}"
                                                        style="border-left: 1px solid #ffffff00;" />
                                                </div>
                                                @error('mobile')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-12 mb-5 otp-field"
                                                @if ($guard === 'organization') style="display: none;" @endif>
                                                <label for="otp">Enter Otp</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                                    <input type="text" name="otp" id="otp-{{ $key }}"
                                                        class="form-control @error('otp') is-invalid @enderror"
                                                        style="border-left: 1px solid #ffffff00;">
                                                </div>
                                                @error('otp')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-12 mb-3 password-fields"
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
                                            
                                            <div class="col-md-12 mb-5 password-fields"
                                                @if ($guard !== 'organization') style="display: none;" @endif>
                                                <div class="g-recaptcha" data-sitekey="{{ $googleRecaptchaData['GOOGLE_RECAPTCHA_KEY'] }}"></div>
                                                @if ($errors->has('g-recaptcha-response'))
                                                    <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                                                @endif
                                            </div>

                                            <div class="col-6 mb-3">
                                                <button class="btn btn-warning-custom px-5" type="submit">Submit</button>
                                            </div>

                                            <div class="col-6 text-end mb-3 send-otp-field"
                                                @if ($guard == 'organization') style="display: none;" @endif>
                                                <button type="button" id="sendOtp"
                                                    class="btn btn-send border-0 bg-transparent sendOtp btn-sendOtp"
                                                    data-target="otp-{{ $key }}" data-guard="{{ $key }}">
                                                    Send OTP <i class="fa fa-refresh ms-2"></i>
                                                </button>
                                            </div>

                                            <div class="col-12 text-center">
                                                <p class="mb-0">Don't have an account?
                                                    <a href="{{ route('register') }}" class="text-warning">Sign Up</a>
                                                </p>
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
                        </div> <!-- End Tab Content -->
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
            // OTP sending logic
            $(function() {
                $('.sendOtp').on('click', function() {
                    var targetOtpInput = $('#' + $(this).data('target'));
                    var form = targetOtpInput.closest('form');
                    var mobileInput = form.find('input[name="mobile"]');
                    var mobile = mobileInput.val().trim();
                    var button = $(this);

                    var guard = form.find('input[name="guard"]').val();

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

            $(function () {
                $('#loginTabs a').on('shown.bs.tab', function (e) {
                    var targetId = $(e.target).attr('href'); // e.g., "#organization-login"

                    $('.tab-pane').each(function () {
                        var paneId = '#' + $(this).attr('id');
                        var $pane = $(this);

                        if (paneId === targetId && paneId.includes('organization')) {
                            $pane.find('.otp-field').hide();
                            $pane.find('.send-otp-field').hide();
                            $pane.find('.password-fields').show();
                        } else {
                            $pane.find('.otp-field').show();
                            $pane.find('.send-otp-field').show();
                            $pane.find('.password-fields').hide();
                        }
                    });
                });

                // Trigger default tab
                $('#loginTabs a.active').trigger('shown.bs.tab');
            });

            // Form validation
            $("form").each(function() {
                $(this).validate({
                    rules: {
                        mobile: {
                            required: true,
                            digits: true,
                            minlength: 10,
                            maxlength: 15
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
                                // Password only required for organization login
                                return form.find('input[name="guard"]').val() === 'organization';
                            },
                            minlength: 6
                        },
                    },
                    messages: {
                        mobile: {
                            required: "Please enter your mobile number.",
                            digits: "Only numbers are allowed.",
                            minlength: "Mobile number must be at least 10 digits.",
                            maxlength: "Mobile number must not exceed 15 digits."
                        },
                        otp: {
                            required: "Please enter the OTP.",
                            digits: "Only numbers are allowed.",
                            minlength: "OTP must be 6 digits.",
                            maxlength: "OTP must be 6 digits."
                        },
                        password: {
                            required: "Password is required",
                            minlength: "Password must be at least 6 characters"
                        },
                    }
                });
            });

            // Activate the correct tab on page reload based on the URL hash
            let hash = window.location.hash;
            if (hash) {
                $('.nav-link[href="' + hash + '"]').tab('show');
            }

            $('.nav-link').on('click', function() {
                history.pushState(null, null, $(this).attr('href'));
            });
        });
    </script>
@endsection
