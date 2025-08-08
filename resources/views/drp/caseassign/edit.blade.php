@extends('layouts.front')
<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
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
                                <h5 class="mb-0" data-anchor="data-anchor">Assign Case :: Edit </h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    @if (Helper::userCan(111, 'can_edit'))
                                        <a href="{{ route('drp.caseassign') }}" class="btn btn-outline-secondary">
                                            <i class="fa fa-list me-1"></i>
                                            Assign Case List
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body row">
                        <form class="row" action="{{ route('drp.caseassign.updatecasedetail', $case->id) }}"
                            id="editCase" method="POST" enctype='multipart/form-data'>
                            @csrf
                            @method('PUT')

                            <div class="col-md-6 col-12 mb-3">
                                <label for="claimant_name" class="form-label">Claimant Name</label>
                                <input type="text" id="claimant_name" name="claimant_name" class="form-control"
                                    value="{{ $case->claimant_first_name }}">
                                @error('claimant_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12 mb-3">
                                <label for="respondent_name" class="form-label">Respondent Name</label>
                                <input type="text" id="respondent_name" name="respondent_name" class="form-control"
                                    value="{{ $case->respondent_first_name }}">
                                @error('respondent_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12 mb-3">
                                <label for="case_type" class="form-label">Case Type</label>
                                <select id="case_type" name="case_type" class="form-select">
                                    @foreach (config('constant.case_type') as $key => $caseType)
                                        <option value="{{ $key }}"
                                            {{ $case->case_type == $key ? 'selected' : '' }}>
                                            {{ $caseType }}</option>
                                    @endforeach
                                </select>
                                @error('case_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select" required>
                                    <option value="1" {{ $case->status == 'Active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ $case->status == 'InActive' ? 'selected' : '' }}>InActive
                                    </option>
                                </select>
                            </div>


                            @php
                                $documents = [
                                    'section_seventeen_document' => 'Section Seventeen Document',
                                    'application_form' => 'Application Form',
                                    'foreclosure_statement' => 'Foreclosure Statement',
                                    'loan_agreement' => 'Loan Agreement',
                                    'account_statement' => 'Account Statement',
                                    'other_document' => 'Other Document',
                                ];
                            @endphp

                            @foreach ($documents as $key => $label)
                                <div class="col-md-6 col-12 mb-3 document-upload" id="upload-{{ $key }}">
                                    <label for="{{ $key }}" class="form-label">
                                        <span style="font-weight: 500;" id="file-label-{{ $key }}">
                                            Attach {{ $label }} Document
                                        </span>
                                    </label>
                                    <input class="form-control" type="file" id="{{ $key }}"
                                        name="{{ $key }}" />

                                    @if (!empty($case->$key))
                                        @php
                                            $filePath = asset('storage/' . $case->$key);
                                            $extension = pathinfo($case->$key, PATHINFO_EXTENSION);
                                        @endphp

                                        <div class="my-2">
                                            @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ $filePath }}" class="img-thumbnail" width="100" />
                                            @elseif ($extension === 'pdf')
                                                <a class="text-decoration-none case-text" style="font-size: 13px"
                                                    href="{{ $filePath }}" target="_blank">
                                                    <img src="{{ asset('assets/img/pdf.png') }}" height="30"
                                                        alt="PDF File" />
                                                    View PDF
                                                </a>
                                            @elseif (in_array(strtolower($extension), ['doc', 'docx']))
                                                <a class="text-decoration-none case-text" style="font-size: 13px"
                                                    href="{{ $filePath }}" target="_blank">
                                                    <img src="{{ asset('public/assets/img/doc.png') }}" height="30"
                                                        alt="DOC File" />
                                                    View Document
                                                </a>
                                            @else
                                                <a class="text-decoration-none case-text" style="font-size: 13px"
                                                    href="{{ $filePath }}" target="_blank">Download File</a>
                                            @endif
                                        </div>
                                    @endif

                                    @error($key)
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endforeach

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update</button>
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
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $("#editCase").validate({
            rules: {
                claimant_name: {
                    required: true,
                },
                respondent_name: {
                    required: true,
                },
                case_type: {
                    required: true,
                },
            },
            messages: {
                claimant_name: {
                    required: "Please Enter Claimant Name",
                },
                respondent_name: {
                    required: "Please Enter Respondent Name",
                },
                case_type: {
                    required: "Please Select Case Type",
                },
            },
        });
    </script>
@endsection
