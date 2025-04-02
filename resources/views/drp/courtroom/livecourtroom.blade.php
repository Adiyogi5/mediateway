@extends('layouts.front')
<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/dt-global_style.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">

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
                <div class="card mb-3 card-inner form-validate">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Court Room - Live</h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    {{-- @if (Helper::userCan(111, 'can_add')) --}}
                                    <a href="{{ route('drp.courtroom.courtroomlist') }}" class="btn btn-outline-secondary">
                                        <i class="fa fa-list me-1"></i>
                                        Court Lists
                                    </a>
                                    {{-- @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 table-meetinglist">
                        <div class="row gy-3">
                            <div class="col-12">
                                <div class="livemeeting-card">
                                    <div class="w-100" style="height: 500px"></div>
                                </div>
                            </div>

                            <div class="col-lg-7 col-12 order-lg-1 order-2">
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading">UPDATES</h4>
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div
                                                    class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <h4 class="livemeetingcard-title">Registrar</h4>
                                                    <h4 class="livemeetingcard-title">Registrar</h4>
                                                    <h4 class="livemeetingcard-title">06-apr-2025 03:50pm</h4>
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </div>
                                                <div class="col">
                                                    <p class="livemeetingcard-text text-muted small ">
                                                        1st Hearing MOM : The Respodent failed to attend the 1st Hearing.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div
                                                    class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <h4 class="livemeetingcard-title">Registrar</h4>
                                                    <h4 class="livemeetingcard-title">Registrar</h4>
                                                    <h4 class="livemeetingcard-title">06-apr-2025 03:50pm</h4>
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </div>
                                                <div class="col">
                                                    <p class="livemeetingcard-text text-muted small ">
                                                        1st Hearing MOM : The Respodent failed to attend the 1st Hearing.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-5 col-12 order-lg-2 order-1">
                                <div class="livemeeting-card h-100">
                                    <div class="form-group">
                                        <textarea class="form-control" id="livemeetingdata" name="livemeetingdata">{{ old('livemeetingdata') }}</textarea>
                                        @error('livemeetingdata')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- Document Type and File Upload -->
                                    <div class="mt-3">
                                        <label class="form-label fw-bold">Select Document Type and Attach</label>
                                        <div class="row g-2">
                                            <div class="col-xl-6 col-12">
                                                <select class="form-select" id="docType">
                                                    <option selected disabled>Document Type</option>
                                                    <option value="ordersheet">Case OrderSheet</option>
                                                    <option value="settlementletter">Settlement Agreement</option>
                                                </select>
                                            </div>
                                            <div class="col-xl-6 col-12">
                                                <select class="form-select" id="tempType">
                                                    <option selected disabled>Template Type</option>
                                                </select>
                                            </div>
                                        </div>                                        
                                    </div>

                                    <!-- Upload Button -->
                                    <div class="text-center mt-3">
                                        <button class="btn btn-secondary w-100" id="uploadBtn">UPLOAD /
                                            SAVE</button>
                                    </div>
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
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/custom-methods.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/waves.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#livemeetingdata').summernote({
                toolbar: [
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['paragraph']],
                ]
            });
            let buttons = $('.note-editor button[data-toggle="dropdown"]');
            buttons.each((key, value) => {
                $(value).on('click', function(e) {
                    $(this).attr('data-bs-toggle', 'dropdown')
                })
            })
        })
    </script>
    <script>
        var orderSheetTemplates = @json($orderSheetTemplates);
        var settlementLetterTemplates = @json($settlementLetterTemplates);
    
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("docType").addEventListener("change", function () {
                var selectedDoc = this.value;
                var tempTypeDropdown = document.getElementById("tempType");
    
                // Clear existing options
                tempTypeDropdown.innerHTML = '<option selected disabled>Template Type</option>';
    
                let templates = [];
    
                if (selectedDoc === "ordersheet") {
                    templates = orderSheetTemplates;
                } else if (selectedDoc === "settlementletter") {
                    templates = settlementLetterTemplates;
                }
    
                // Append new options
                templates.forEach(function (template) {
                    let option = document.createElement("option");
                    option.value = template.id; // Use ID or any unique identifier
                    option.textContent = template.name; // Use the name column from the database
                    tempTypeDropdown.appendChild(option);
                });
            });
        });
    </script>    
@endsection
