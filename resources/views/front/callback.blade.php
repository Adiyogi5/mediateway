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
                        <label class="callback-title mb-4">Request a Callback</label>
                        <p class="callback-text mb-lg-5 mb-3">Please provide your contact Information and we shall connect
                            with you at your suitable time</p>
                    </div>
                    <div class="card-body p-0">

                        <form method="POST" action="{{route('front.requestcallback')}}" id="callbackForm">
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
                                    <label for="datetime">Preferred time for Call</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-clock"></i></span>
                                        <input type="datetime-local" name="datetime" id="datetime"
                                            style="border-left: 1px solid #ffffff00;"
                                            class="form-control @error('datetime') is-invalid @enderror">
                                    </div>
                                    @error('datetime')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="mb-lg-4 mb-3 form-check">
                                        <input type="checkbox" name="terms" class="form-check-input">
                                        <label class="form-check-label my-auto">I provide my consent to PrivateCourt to
                                            contact me.</label>
                                    </div>
                                </div>
                                <div class="col-12 text-center justify-content-center">
                                    <button type="submit" class="btn btn-warning-custom submit-btn">Request</button>
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
       document.addEventListener("DOMContentLoaded", function () {
        var datetimeInput = document.getElementById("datetime");
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());         
        var minDateTime = now.toISOString().slice(0, 16); 
        datetimeInput.setAttribute("min", minDateTime);
    });
    </script>
    <script>
       $(function() {
        $("#callbackForm").validate({
            errorClass: "is-invalid", // Bootstrap class for invalid fields
            errorElement: "span", // Ensure error messages use span (matches Bootstrap)
            errorPlacement: function(error, element) {
                if (element.closest(".input-group").length) {
                    element.addClass("is-invalid"); // Highlight input field
                    element.closest(".input-group").after(error); // Place error message after input-group
                } else {
                    error.insertAfter(element); // Default placement
                }
            },
            highlight: function(element) {
                $(element).addClass("is-invalid"); // Add Bootstrap error class
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid"); // Remove error class on valid input
            },
            rules: {
                name: {
                    required: true,
                    minlength: 5,
                    maxlength: 100
                },
                datetime: {
                    required: true,
                },
                mobile: {
                    required: true,
                    number: true,
                    indiaMobile: true,
                    exactlength: 10,
                },
                terms: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                datetime: {
                    required: "Please select Date Time",
                },
                mobile: {
                    required: "Please enter Mobile number",
                },
                terms: {
                    required: "Please select Terms and Conditions checkbox",
                },
            }
        });
    });
    </script>
@endsection
