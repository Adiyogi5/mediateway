@extends('layouts.front')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/flexslider/flexslider.css') }}">
@endsection

@section('content')
    <div class="container contact-us">
        <div class="card my-5">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="p-lg-4 p-0">
                            <form action="{{ request()->url() }}" method="post" id="contactUs">
                                <h4 class="text-second fw-semi-bold">I'm Intersted In</h4>
                                <div class="d-flex justify-content-between intersted-in my-3 gap-2 flex-wrap">
                                    <div class="flex-grow-1">
                                        <input type="radio" value="1" name="type" class="d-none" id="type_2" checked>
                                        <label class="btn btn-outline-primary rounded-5 px-4" for="type_2">Library</label>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="radio" value="2" name="type" class="d-none" id="type_3">
                                        <label class="flex-shrink-0 btn btn-outline-primary rounded-5 px-4" for="type_3">
                                            Student
                                        </label>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="radio" value="3" name="type" class="d-none" id="type_4">
                                        <label class="flex-shrink-0 btn btn-outline-primary rounded-5 px-4"
                                            for="type_4">Other</label>
                                    </div>
                                </div>
                                @error('name')
                                    <p class="small mb-0 text-danger">{{ $message }}</p>
                                @enderror
                                <div>
                                    <h4 class="text-muted fw-semi-bold">You have any question? feel free to contact us.</h4>
                                    <div class="mb-3">
                                        @csrf
                                        <input type="name" class="form-control" name="name" id="name"
                                            placeholder="Your Name" value="{{ old('name') }}">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" name="email" id="email"
                                            placeholder="Email" value="{{ old('email') }}">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control" id="message" name="message" rows="3" placeholder="Your Message">{{ old('message') }}</textarea>
                                        @error('message')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-contact btn-lg">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-5 list">
                        <div class="h-100 ">
                            <h2 class="fw-bold text-second">Feeling confused?</h2>
                            <h2 class="fw-bold text-first">Just chill, talk to us!</h2>
                            <div class="my-5">
                                <div class="d-flex gap-3 align-items-center my-4">
                                    <div class="icon"><i class="fa fa-phone"></i></div>
                                    <p class="mb-0 text-muted fw-semi-bold">{{ $site_settings['phone'] }}</p>
                                </div>
                                <div class="d-flex gap-3 align-items-center my-4">
                                    <div class="icon"><i class="fa fa-envelope"></i></div>
                                    <p class="mb-0 text-muted fw-semi-bold">{{ $site_settings['email'] }}</p>
                                </div>
                                <div class="d-flex gap-3 align-items-center my-4">
                                    <div class="icon"><i class="fa-regular fa-location-dot"></i></div>
                                    <p class="mb-0 text-muted fw-semi-bold">{{ $site_settings['address'] }}</p>
                                </div>
                            </div>
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
            $("#contactUs").validate({
                errorClass: "text-danger fs--1",
                errorElement: "small",
                rules: {
                    name: {
                        required: true,
                        minlength: 5,
                        maxlength: 100
                    },
                    email: {
                        required: true,
                        email: true,
                        customEmail: true,
                        minlength: 2,
                        maxlength: 100
                    },
                    message: {
                        required: true,
                        minlength: 10,
                        maxlength: 1000
                    },
                },
                messages: {
                    name: {
                        required: "Please enter name",
                    },
                    email: {
                        required: "Please enter Email",
                    },
                    message: {
                        required: "Please enter message.",
                    },
                },
            });
        })
    </script>
@endsection
