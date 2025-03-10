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
                            $loginTypes = ['individual' => 'Individual', 'organization' => 'Organization', 'drp' => 'DRP'];
                        @endphp

                        @foreach ($loginTypes as $key => $label)
                        <div id="{{ $key }}-login" class="tab-pane fade {{ $key === 'individual' ? 'show active' : '' }}">
                            <form method="POST" action="{{ route('login.post', ['guard' => $key]) }}">
                                @csrf
                                <input type="hidden" name="login_as" value="{{ $key }}">
                                                        
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="mobile">Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                            <input class="form-control @error('mobile') is-invalid @enderror"
                                                type="text" name="mobile" autocomplete="mobile" value="{{ old('mobile') }}" style="border-left: 1px solid #ffffff00;" />
                                        </div>
                                        @error('mobile')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                            
                                    <div class="col-md-12 mb-5">
                                        <label for="otp">Enter Otp</label>
                                        <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                        <input type="text" name="otp"
                                            id="otp-{{ $key }}" class="form-control @error('otp') is-invalid @enderror" style="border-left: 1px solid #ffffff00;">
                                        </div>
                                        @error('otp')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                            
                                    {{-- <div class="col-12 p-3">
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember-{{ $key }}" checked="checked" />
                                            <label class="form-check-label" for="remember-{{ $key }}">Remember me</label>
                                        </div>
                                    </div> --}}
                            
                                    <div class="col-6 mb-3">
                                        <button class="btn btn-warning-custom px-5" type="submit">Submit</button>
                                    </div>

                                    <div class="col-6 text-end mb-3">
                                        <button type="button" class="btn btn-send border-0 bg-transparent sendOtp btn-sendOtp"
                                            data-target="otp-{{ $key }}">
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
<script>
    $(function () {
        // OTP sending logic
        $('.sendOtp').on('click', function () {
            var targetOtpInput = $('#' + $(this).data('target'));
            var mobileInput = targetOtpInput.closest('form').find('input[name="mobile"]');

            if (mobileInput.val().trim().length !== 10) {
                toastr.error("Please enter a valid 10-digit mobile number before requesting OTP.");
                return;
            }

            var generatedOtp = Math.floor(100000 + Math.random() * 900000);
            targetOtpInput.val(generatedOtp);
            toastr.success("OTP sent successfully! (For testing: " + generatedOtp + ")");
        });

        // Form validation
        $("form").each(function () {
            $(this).validate({
                rules: {
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    otp: {
                        required: true,
                        digits: true,
                        minlength: 6,
                        maxlength: 6
                    }
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
                    }
                }
            });
        });

        // Activate the correct tab on page reload based on the URL hash
        let hash = window.location.hash;
        if (hash) {
            $('.nav-link[href="' + hash + '"]').tab('show');
        }

        $('.nav-link').on('click', function () {
            history.pushState(null, null, $(this).attr('href'));
        });
    });
</script>
@endsection
