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
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <input type="hidden" name="guard" value="{{ $guard }}">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="mobile">Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                                    <input type="text" name="name" style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('name') is-invalid @enderror" required>
                                                    @error('name')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="mobile">Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fa-solid fa-envelope"></i></span>
                                                    <input type="email" name="email" style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('email') is-invalid @enderror" required>
                                                    @error('email')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6 mb-4">
                                                <label for="mobile">Phone</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                                    <input type="text" name="mobile" style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('mobile') is-invalid @enderror" required
                                                        maxlength="10">
                                                    @error('mobile')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6 mb-4">
                                                <label for="mobile">Enter Otp</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                                    <input type="text" name="otp" id="otp-{{ $guard }}" style="border-left: 1px solid #ffffff00;"
                                                        class="form-control @error('otp') is-invalid @enderror">
                                                    @error('otp')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-8">
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" name="terms" class="form-check-input">
                                                    <label class="form-check-label">I agree to the <a href="#"> Terms
                                                            of Service and Privacy Policy</a></label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="mb-3 text-end">
                                                    <button type="button"
                                                        class="btn btn-send border-0 bg-transparent sendOtp pt-0 mt-0"
                                                        data-target="otp-{{ $guard }}">
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
    <script>
        $(function() {
            // Change the button text based on active tab
            $('.nav-link').on('click', function() {
                var guard = $(this).data('guard');
                $('.submit-btn').text('SIGN UP as ' + guard.charAt(0).toUpperCase() + guard.slice(1));
            });

            // OTP sending logic
            $('.sendOtp').on('click', function() {
                var targetOtpInput = $('#' + $(this).data('target'));
                var mobileInput = targetOtpInput.closest('form').find(
                    'input[name="mobile"]'); // Find the corresponding mobile input

                if (mobileInput.val().trim().length !== 10) {
                    toastr.error("Please enter a valid 10-digit mobile number before requesting OTP.");
                    return;
                }

                var generatedOtp = Math.floor(100000 + Math.random() * 900000);
                targetOtpInput.val(generatedOtp);
                toastr.success("OTP sent successfully! (For testing: " + generatedOtp + ")");
            });
        });
    </script>
    <script type="text/javascript">
        $(function() {
            var validator = $("form").validate({
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
                        required: true,
                        number: true,
                        exactlength: 6,
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

        });
    </script>
@endsection
