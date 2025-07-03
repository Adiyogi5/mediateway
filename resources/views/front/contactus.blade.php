@extends('layouts.front')


@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}


    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="contactus-card d-flex flex-column flex-lg-row my-5">
            <!-- Contact Information Card -->
            <div class="contact-info text-white">
                <h5 class="contact-contact">Contact</h5>
                <h2 class="contact-getin">Get in Touch</h2>
                <p class="contact-p"><strong>Phone</strong><br>{{$site_settings['phone']}}</p>
                <p class="contact-p"><strong>E-mail</strong><br>{{$site_settings['email']}}</p>
                <p class="contact-p">
                    <strong>Address</strong><br>
                    {!! nl2br(wordwrap($site_settings['address'], 30, "\n", true)) !!}
                </p>                
            </div>

            <!-- Contact Form -->
            <div class="contact-form bg-white">
                <form method="POST" action="{{ route('front.submitcontactus') }}" id="submitcontactusForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" name="first_name" class="form-control" placeholder="First Name">
                            @error('first_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name">
                            @error('last_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" name="mobile" class="form-control" placeholder="Phone">
                            @error('mobile')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="email" name="email" class="form-control" placeholder="E-mail">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="subject" class="form-control" placeholder="Subject">
                        @error('subject')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="message" rows="3" placeholder="Message"></textarea>
                        @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ $data['GOOGLE_RECAPTCHA_KEY'] }}"></div>
                        @if ($errors->has('g-recaptcha-response'))
                            <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                        @endif
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container-fluid px-0 my-5">
        {!! $site_settings['google_iframe'] !!}
    </div>
@endsection


@section('js')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        $(function() {
            $.validator.addMethod("indiaMobile", function(value, element) {
                return this.optional(element) || /^[6789]\d{9}$/.test(value);
            }, "Please enter a valid Indian mobile number.");

            $("#submitcontactusForm").validate({
                errorClass: "is-invalid",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.parent(".input-group").length) {
                        error.insertAfter(element.parent());
                    } else if (element.hasClass("form-control")) {
                        error.insertAfter(element);
                    } else {
                        error.appendTo(element.parent());
                    }
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid").css("border-color", "red");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid").css("border-color", "");
                },
                rules: {
                    first_name: {
                        required: true,
                        maxlength: 100
                    },
                    last_name: {
                        required: true,
                        maxlength: 100
                    },
                    mobile: {
                        required: true,
                        indiaMobile: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    subject: {
                        required: true
                    },
                    message: {
                        required: true
                    }
                },
                messages: {
                    first_name: {
                        required: "Please enter First Name"
                    },
                    last_name: {
                        required: "Please enter Last Name"
                    },
                    mobile: {
                        required: "Please enter Mobile number",
                        indiaMobile: "Please enter a valid Indian mobile number"
                    },
                    email: {
                        required: "Please enter Email Address",
                        email: "Please enter a valid email address"
                    },
                    subject: {
                        required: "Please Enter Subject"
                    },
                    message: {
                        required: "Please Enter Message"
                    }
                }
            });
        });
    </script>
@endsection
