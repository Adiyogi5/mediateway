@extends('layouts.front')


@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}

    <div class="container about-us">
        <div class="row my-5">
            <div class="col-lg-8 col-md-10 col-12 mx-md-auto mx-none">
                <div class="card callback-card ">
                    <div class="gender_box text-center justify-content-center">
                        <label class="callback-title mb-4">Book Appointment</label>
                        <p class="callback-text mb-lg-5 mb-2">Please provide your contact Information </p>
                    </div>
                    <div class="card-body p-0">

                        <form method="POST" action="{{ route('front.requestbookappointment') }}" id="bookappointmentForm">
                            @csrf
                            <div class="row">
                                <div class="col-12 mb-lg-4 mb-3">
                                    <label for="mobile">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                        <input type="text" name="name" style="border-left: 1px solid #ffffff00;"
                                            class="form-control @error('name') is-invalid @enderror">
                                    </div>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-lg-4 mb-3">
                                    <label for="mobile">Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                        <input type="text" name="mobile" style="border-left: 1px solid #ffffff00;"
                                            class="form-control @error('mobile') is-invalid @enderror" maxlength="10">
                                    </div>
                                    @error('mobile')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-lg-4 mb-3">
                                    <label for="email">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                        <input type="text" name="email" style="border-left: 1px solid #ffffff00;"
                                            class="form-control @error('email') is-invalid @enderror">
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-lg-4 mb-3">
                                    <label for="datestart">Preferred Date</label>
                                    <div class="row gy-2">
                                        <div class="col-md-6 col-12">
                                            <div class="input-group">
                                                <span class="input-group-text"></span>
                                                <input type="date" name="datestart" id="datestart"
                                                    style="border-left: 1px solid #ffffff00;"
                                                    class="form-control @error('datestart') is-invalid @enderror">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="input-group">
                                                <span class="input-group-text"></span>
                                                <input type="date" name="dateend" id="dateend"
                                                    style="border-left: 1px solid #ffffff00;"
                                                    class="form-control @error('dateend') is-invalid @enderror">
                                            </div>
                                        </div>
                                    </div>
                                    @error('dateend')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="mb-lg-4 mb-3 form-check">
                                        <input type="checkbox" name="terms" class="form-check-input">
                                        <label class="form-check-label my-auto">I provide my consent to Mediateway to
                                            contact me.</label>
                                    </div>
                                </div>
                                <div class="col-12 text-center justify-content-center">
                                    <button type="submit" class="btn btn-warning-custom submit-btn">Book</button>
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
        $(function() {
            let today = new Date().toISOString().split('T')[0];
            $("#datestart").attr("min", today);

            $("#datestart").on("change", function() {
                let selectedDate = $(this).val();
                $("#dateend").attr("min", selectedDate);
            });

            $.validator.addMethod("indiaMobile", function(value, element) {
                return this.optional(element) || /^[6789]\d{9}$/.test(value);
            }, "Please enter a valid Indian mobile number.");

            $("#bookappointmentForm").validate({
                errorClass: "is-invalid",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.closest(".input-group").length) {
                        element.addClass("is-invalid");
                        element.closest(".input-group").after(error);
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                rules: {
                    name: {
                        required: true,
                        minlength: 5,
                        maxlength: 100
                    },
                    datestart: {
                        required: true,
                    },
                    dateend: {
                        required: true,
                    },
                    mobile: {
                        required: true,
                        number: true,
                        indiaMobile: true,
                        minlength: 10,
                        maxlength: 10,
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    terms: {
                        required: true,
                    },
                },
                messages: {
                    name: {
                        required: "Please enter name",
                    },
                    datestart: {
                        required: "Please select Date From",
                    },
                    dateend: {
                        required: "Please select End Date",
                    },
                    mobile: {
                        required: "Please enter Mobile number",
                        number: "Please enter a valid number",
                        minlength: "Mobile number must be exactly 10 digits",
                        maxlength: "Mobile number must be exactly 10 digits",
                        indiaMobile: "Please enter a valid Indian mobile number",
                    },
                    email: {
                        required: "Please enter Email Address",
                        email: "Please enter a valid email address",
                    },
                    terms: {
                        required: "Please select Terms and Conditions checkbox",
                    },
                }
            });
        });
    </script>
@endsection
