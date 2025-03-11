@extends('layouts.front')

<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/dt-global_style.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />

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
                <div class="card mb-3 card-inner">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Staffs :: Staffs Add </h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                                    <a href="{{ route('organization.staffs') }}" class="btn btn-outline-secondary"> <i
                                            class="fa fa-arrow-left me-1"></i> Go
                                        Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="row" id="addStaff" method="POST" action="{{ route('organization.staffs.add') }}"
                            enctype='multipart/form-data'>
                            @csrf
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="role_id">Role <span class="required">*</span></label>
                                <select name="role_id" class="form-select" id="role_id">
                                    <option value="">Please Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role['id'] }}" @selected(old('role_id') == $role['id'])>
                                            {{ $role['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="name">First Name <span class="required">*</span></label>
                                <input class="form-control" id="name" placeholder="Enter Name" name="name"
                                    type="text" value="{{ old('name') }}" />
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="email">Email <span class="required">*</span></label>
                                <input class="form-control" id="email" placeholder="Enter Email" type="email"
                                    name="email" value="{{ old('email') }}" />
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="mobile">Mobile <span class="required">*</span></label>
                                <input class="form-control" id="mobile" placeholder="Enter Mobile Number" name="mobile"
                                    type="text" value="{{ old('mobile') }}" />
                                @error('mobile')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="status">Status</label>
                                <select name="status" class="form-select" id="status">
                                    <option value="1" @selected(old('status', 1) == 1)> Active </option>
                                    <option value="0" @selected(old('status', 1) == 0)> Inactive </option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="image">Image </label>
                                <input class="form-control" id="image" name="image" type="file" value="" />
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="password">New Password <span
                                        class="required">*</span></label>
                                <input class="form-control" placeholder="Enter Password" name="password" id="new-password"
                                    type="password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="password_confirmation">Confirm Password <span
                                        class="required">*</span></label>
                                <input class="form-control" placeholder="Enter Confirm Password"
                                    name="password_confirmation" id="password_confirmation" type="password">
                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                                <input type="hidden" name="table" value="organizations">
                                <input type="hidden" name="organization_parent_id" value="{{$organization_authData->id}}">
                                <button class="btn btn-secondary submitbtn" type="submit">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/custom-methods.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/waves.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script type="text/javascript">
        $("#addStaff").validate({
            rules: {
                role_id: {
                    required: true
                },
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10
                },
                password: {
                    required: true,
                    minlength: 8,
                    maxlength: 50
                },
                password_confirmation: {
                    required: true,
                    minlength: 8,
                    maxlength: 50,
                    equalTo: "#new-password"
                },
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                }
            },
            messages: {
                role_id: {
                    required: "Please Select Role",
                },
                name: {
                    required: "Please enter name",
                },
                email: {
                    required: "Please enter Email",
                },
                mobile: {
                    required: "Please enter Mobile number",
                },
                password: {
                    required: "Please enter New Password",
                },
                password_confirmation: {
                    required: "Please enter Confirm Password",
                },
                image: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                }
            },
        });
    </script>
@endsection
