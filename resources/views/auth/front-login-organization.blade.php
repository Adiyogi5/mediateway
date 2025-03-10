@extends('layouts.auth')

@section('content')
<div class="container register">
    <div class="row">
        <div class="col-12 py-3">
            <div class="col-lg-3 gender_box mx-auto">
                <div>
                    <label><i class="fa-duotone fa-bed"></i> LOGIN</label>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card my-0 my-lg-5">
                <div class="card-body">
                    <h2>Sign In as <span class="text-secondary">Organization</span></h2>
                    <p>Enter your mobile and OTP to login</p>

                    <form method="POST" action="{{ route('login.post', ['guard' => 'organization']) }}">
                        @csrf
                        <input type="hidden" name="login_as" value="organization">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-duotone fa-envelope"></i></span>
                                    <input class="form-control @error('mobile') is-invalid @enderror"
                                        type="text" name="mobile" autocomplete="mobile"
                                        placeholder="Mobile / User ID" value="{{ old('mobile') }}" />
                                </div>
                                @error('mobile')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-7 col-6">
                                <input type="text" placeholder="OTP Code" name="otp"
                                    id="otp-organization" class="form-control @error('otp') is-invalid @enderror">
                                @error('otp')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-5 col-6 text-end">
                                <button type="button" class="btn btn-secondary sendOtp"
                                    data-target="otp-organization">
                                    Send OTP <i class="fa fa-refresh ms-2"></i>
                                </button>
                            </div>

                            <div class="col-12 p-3">
                                <div class="form-check form-check-primary form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="remember"
                                        id="remember-organization" checked="checked" />
                                    <label class="form-check-label" for="remember-organization">Remember me</label>
                                </div>
                            </div>

                            <div class="col-6 text-center">
                                <button class="btn bg-secondary text-white w-100" type="submit">LOG IN</button>
                            </div>

                            {{-- <div class="col-6 text-end">
                                @if (Route::has('forget.password'))
                                    <a class="text-second" href="{{ route('forget.password', 'organization') }}">
                                        Forgot Password?
                                    </a>
                                @endif
                            </div> --}}

                            <div class="col-12 text-center">
                                <p class="mb-0">Don't have an account?
                                    <a href="{{ route('register') }}" class="text-second">Sign Up</a>
                                </p>
                            </div>
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
            $("form").validate({
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
    </script>
@endsection
